<?php

namespace Modules\School\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class CanterburySchoolsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command?->info('Seeding schools for tenant 6b62a38e-d867-445c-b6cd-f1b1ab060f21...');

        Artisan::call('school:import-tenant', [
            'tenant_id' => '6b62a38e-d867-445c-b6cd-f1b1ab060f21',
            '--force' => true,
        ], $this->command?->getOutput());
    }
}
