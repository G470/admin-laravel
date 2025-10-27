<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Services\CountryDataImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportCountryData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'import:country-data 
                           {country : The country code (e.g., DE, AT, CH)}
                           {file : Path to the import file}
                           {--format=csv : File format (csv, xlsx, xls)}
                           {--delimiter=, : CSV delimiter}
                           {--no-header : File has no header row}
                           {--dry-run : Preview import without executing}
                           {--clear : Clear existing data before import}
                           {--batch-size=1000 : Number of records to process at once}';

    /**
     * The console command description.
     */
    protected $description = 'Import postal code data for a specific country from CSV/Excel files';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $countryCode = strtoupper($this->argument('country'));
        $filePath = $this->argument('file');

        // Validate country
        $country = Country::where('code', $countryCode)->first();
        if (!$country) {
            $this->error("Country with code '{$countryCode}' not found.");
            return Command::FAILURE;
        }

        // Validate file
        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return Command::FAILURE;
        }

        $this->info("Starting import for {$country->name} ({$country->code})");
        $this->info("File: {$filePath}");

        $importService = new CountryDataImportService();

        // Validate file format
        $validation = $importService->validateImportFile($filePath);
        if (!$validation['valid']) {
            $this->error("File validation failed:");
            foreach ($validation['errors'] as $error) {
                $this->error("- {$error}");
            }
            return Command::FAILURE;
        }

        $this->info("File size: {$validation['file_info']['size_formatted']}");

        // Prepare import options
        $options = [
            'has_header' => !$this->option('no-header'),
            'delimiter' => $this->option('delimiter'),
        ];

        try {
            // Get preview
            $this->info("Analyzing file structure...");
            $preview = $importService->getDataPreview($filePath, $options, 5);

            $this->info("Found " . count($preview) . " sample records:");
            $this->table(
                ['Field', 'Sample Values'],
                collect($preview)->map(function ($row, $index) {
                    return collect($row)->map(function ($value, $key) use ($index) {
                        return [$key, $value];
                    })->toArray();
                })->collapse()->groupBy(0)->map(function ($values, $key) {
                    return [$key, $values->pluck(1)->take(3)->implode(', ')];
                })->values()->toArray()
            );

            // Dry run check
            if ($this->option('dry-run')) {
                $this->info("Dry run completed. Use --no-dry-run to execute the import.");
                return Command::SUCCESS;
            }

            // Clear existing data if requested
            if ($this->option('clear')) {
                if ($this->confirm("This will delete all existing postal code data for {$country->name}. Continue?")) {
                    $this->info("Clearing existing data...");
                    $importService->clearCountryData($country);
                } else {
                    $this->info("Import cancelled.");
                    return Command::FAILURE;
                }
            }

            // Get current stats
            $statsBefore = $importService->getImportStats($country);
            $this->info("Records before import: " . number_format($statsBefore['total_records']));

            // Execute import
            $this->info("Starting import process...");
            $progressBar = $this->output->createProgressBar(100);

            $result = $importService->importCountryData($country, $filePath, $options);

            $progressBar->finish();
            $this->newLine();

            if ($result['success']) {
                $this->info("Import completed successfully!");

                $stats = $result['stats'];
                $this->table(
                    ['Metric', 'Count'],
                    [
                        ['Total Rows Processed', number_format($stats['total_rows'])],
                        ['Successfully Inserted', number_format($stats['inserted_rows'])],
                        ['Skipped (Duplicates)', number_format($stats['skipped_rows'])],
                        ['Table Name', $stats['table_name']],
                    ]
                );

                // Get updated stats
                $statsAfter = $importService->getImportStats($country);
                $this->info("Records after import: " . number_format($statsAfter['total_records']));
                $this->info("Unique cities: " . number_format($statsAfter['unique_cities']));
                $this->info("Records with coordinates: " . number_format($statsAfter['records_with_coordinates']));

            } else {
                $this->error("Import failed: " . $result['message']);
                if (isset($result['error'])) {
                    $this->error("Error details: " . $result['error']);
                }
                return Command::FAILURE;
            }

        } catch (\Exception $e) {
            $this->error("Import error: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Get available countries for autocomplete
     */
    protected function getCountryOptions(): array
    {
        return Country::where('is_active', true)
            ->orderBy('name')
            ->pluck('code', 'name')
            ->toArray();
    }

    /**
     * Display import guidelines
     */
    protected function displayGuidelines(): void
    {
        $this->info("Import Guidelines:");
        $this->info("- Supported formats: CSV, XLSX, XLS");
        $this->info("- Required columns: postal_code, city");
        $this->info("- Optional columns: sub_city, region, latitude, longitude, population");
        $this->info("- Maximum file size: 50MB");
        $this->info("- Encoding: UTF-8 recommended");
        $this->newLine();
    }
}