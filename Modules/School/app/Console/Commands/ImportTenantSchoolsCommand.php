<?php

namespace Modules\School\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Modules\Country\Models\City;
use Modules\Country\Models\Country;
use Modules\Country\Models\Region;
use Modules\School\Models\School;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportTenantSchoolsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'school:import-tenant
                            {tenant_id=6b62a38e-d867-445c-b6cd-f1b1ab060f21 : The tenant UUID}
                            {--file= : Path to JSON or XLSX file containing schools}
                            {--force : Commit valid schools without prompting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import schools for a specific tenant matching existing Country, City, and Region';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tenantId = (string) $this->argument('tenant_id');
        $this->info("Starting school import for Tenant ID: [{$tenantId}]");

        $filePath = $this->option('file') ?: base_path('Modules/School/database/data/canterbury_schools.json');

        if (! file_exists($filePath)) {
            $this->error("Schools data file not found at: {$filePath}");

            return self::FAILURE;
        }

        $schools = $this->loadSchoolsData($filePath);

        if (empty($schools)) {
            $this->error("No schools found in file: {$filePath}");

            return self::FAILURE;
        }

        $this->info('Total schools to process: '.count($schools));

        // 1. Resolve Country (Egypt) for this tenant
        $countries = Country::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->get();

        $country = $countries->first(function (Country $c) {
            return $this->normalizeName($c->getTranslation('title', 'en')) === 'egypt';
        });

        if (! $country) {
            $this->error("Country 'Egypt' not found for tenant [{$tenantId}].");
            if ($countries->isNotEmpty()) {
                $this->line('Available countries for tenant: '.implode(', ', $countries->map(fn ($c) => $c->getTranslation('title', 'en'))->toArray()));
            }

            return self::FAILURE;
        }

        $this->info("Matched Country: ID [{$country->id}] - {$country->getTranslation('title', 'en')}");

        // 2. Load all Cities for this tenant & country
        $cities = City::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('country_id', $country->id)
            ->get();

        $cityMap = [];
        foreach ($cities as $city) {
            $cityEn = $city->getTranslation('title', 'en');
            $norm = $this->normalizeName($cityEn);
            $cityMap[$norm] = $city;
        }

        // 3. Load all Regions for this tenant
        $regions = Region::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->get();

        $regionMap = [];
        foreach ($regions as $region) {
            $regionEn = $region->getTranslation('title', 'en');
            $norm = $this->normalizeName($regionEn);
            $regionMap[$region->city_id][$norm] = $region;
        }

        // 4. Process each school within a database transaction
        $created = 0;
        $updated = 0;
        $unresolved = [];

        DB::beginTransaction();

        try {
            foreach ($schools as $index => $item) {
                $rowNum = $index + 1;
                $nameEn = trim($item['name_en'] ?? '');
                $nameAr = trim($item['name_ar'] ?? '');
                $cityName = trim($item['city'] ?? '');
                $regionName = trim($item['region'] ?? '');

                if (empty($nameEn)) {
                    $unresolved[] = [
                        'row' => $rowNum,
                        'school' => 'N/A',
                        'issue' => 'Missing English name',
                    ];

                    continue;
                }

                // Resolve City
                $city = $this->findMatchingCity($cityMap, $cityName);
                if (! $city) {
                    $unresolved[] = [
                        'row' => $rowNum,
                        'school' => $nameEn,
                        'issue' => "City '{$cityName}' not found in database",
                    ];

                    continue;
                }

                // Resolve Region
                $region = $this->findMatchingRegion($regionMap, $city->id, $regionName);
                if (! $region) {
                    $unresolved[] = [
                        'row' => $rowNum,
                        'school' => $nameEn,
                        'issue' => "Region '{$regionName}' not found under city '{$cityName}' (ID: {$city->id})",
                    ];

                    continue;
                }

                // Check for existing school
                $existing = School::withoutGlobalScopes()
                    ->where('tenant_id', $tenantId)
                    ->where('city_id', $city->id)
                    ->where('region_id', $region->id)
                    ->get()
                    ->first(function (School $s) use ($nameEn) {
                        return strtolower(trim($s->getTranslation('title', 'en'))) === strtolower($nameEn);
                    });

                if ($existing) {
                    $existing->setTranslation('title', 'en', $nameEn);
                    $existing->setTranslation('title', 'ar', $nameAr ?: $nameEn);
                    $existing->is_active = true;
                    $existing->save();
                    $updated++;
                } else {
                    School::create([
                        'title' => [
                            'en' => $nameEn,
                            'ar' => $nameAr ?: $nameEn,
                        ],
                        'phone' => null,
                        'email' => null,
                        'country_id' => $country->id,
                        'city_id' => $city->id,
                        'region_id' => $region->id,
                        'tenant_id' => $tenantId,
                        'is_active' => true,
                    ]);
                    $created++;
                }
            }

            if (! empty($unresolved)) {
                $this->warn('Unresolved items found during verification: '.count($unresolved));
                $this->table(['Row', 'School', 'Issue'], array_slice($unresolved, 0, 15));

                if (! $this->option('force') && $this->input->isInteractive() && ! $this->confirm('Proceed with committing the valid schools?', true)) {
                    DB::rollBack();
                    $this->warn('Import aborted by user. Database was rolled back.');

                    return self::FAILURE;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error("An error occurred during import: {$e->getMessage()}");

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('=== Import Summary ===');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Processed', count($schools)],
                ['Created Schools', $created],
                ['Updated Schools', $updated],
                ['Unresolved / Skipped', count($unresolved)],
            ]
        );

        $this->info('Schools successfully imported for tenant: '.$tenantId);

        return self::SUCCESS;
    }

    /**
     * Load schools data from JSON or XLSX file.
     *
     * @return array<int, array{name_en: string, name_ar: string, country: string, city: string, region: string}>
     */
    private function loadSchoolsData(string $filePath): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($extension === 'json') {
            $content = file_get_contents($filePath);

            return json_decode($content, true) ?: [];
        }

        if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
            $spreadsheet = IOFactory::load($filePath);
            $rows = $spreadsheet->getActiveSheet()->toArray();

            array_shift($rows); // Remove header row

            $schools = [];
            foreach ($rows as $row) {
                $schools[] = [
                    'name_en' => trim($row[4] ?? $row[0] ?? ''),
                    'name_ar' => trim($row[5] ?? ''),
                    'country' => trim($row[1] ?? 'Egypt'),
                    'city' => trim($row[2] ?? ''),
                    'region' => trim($row[3] ?? ''),
                ];
            }

            return $schools;
        }

        return [];
    }

    /**
     * Find matching City using direct and fuzzy matching.
     *
     * @param  array<string, City>  $cityMap
     */
    private function findMatchingCity(array $cityMap, string $cityName): ?City
    {
        $normalized = $this->normalizeName($cityName);

        if (isset($cityMap[$normalized])) {
            return $cityMap[$normalized];
        }

        // Fuzzy match: check if one contains the other or levenshtein distance
        foreach ($cityMap as $normKey => $city) {
            if (str_contains($normKey, $normalized) || str_contains($normalized, $normKey)) {
                return $city;
            }
            if (levenshtein($normalized, $normKey) <= 2) {
                return $city;
            }
        }

        return null;
    }

    /**
     * Find matching Region under city using direct and fuzzy matching.
     *
     * @param  array<int, array<string, Region>>  $regionMap
     */
    private function findMatchingRegion(array $regionMap, int $cityId, string $regionName): ?Region
    {
        if (! isset($regionMap[$cityId])) {
            return null;
        }

        $cityRegions = $regionMap[$cityId];
        $normalized = $this->normalizeName($regionName);

        if (isset($cityRegions[$normalized])) {
            return $cityRegions[$normalized];
        }

        // Fuzzy match
        foreach ($cityRegions as $normKey => $region) {
            if (str_contains($normKey, $normalized) || str_contains($normalized, $normKey)) {
                return $region;
            }
            if (levenshtein($normalized, $normKey) <= 2) {
                return $region;
            }
        }

        return null;
    }

    /**
     * Normalize a name for resilient matching.
     */
    private function normalizeName(string $name): string
    {
        $name = strtolower(trim($name));
        $name = str_replace(['-', '_', '\'', '"', '`', '.', ','], ' ', $name);

        return preg_replace('/\s+/', ' ', $name) ?? $name;
    }
}
