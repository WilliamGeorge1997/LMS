<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Book\Imports\BookCodesImport;

#[Signature('import:book-codes {filename : The name of the excel file} {tenant : The tenant ID}')]
#[Description('Import book codes from an Excel file')]
class ImportBookCodesCommand extends Command
{
    public function handle()
    {
        // Build the path to look directly in storage/app/private/excel/
        $filename = $this->argument('filename');
        $path = storage_path("app/private/excel/{$filename}");

        if (!file_exists($path)) {
            $this->error("File not found at path: {$path}");
            return Command::FAILURE;
        }

        $tenantId = $this->argument('tenant');
        $this->info("Importing book codes from {$path} for tenant {$tenantId}...");

        $import = new BookCodesImport($tenantId);
        $import->withOutput($this->output);
        Excel::import($import, $path);

        $this->info("Import completed!");
        $this->info("Successfully Imported: " . $import->importedCount);
        $this->warn("Skipped (Duplicates/Not Found): " . $import->skippedCount);

        return Command::SUCCESS;
    }
}
