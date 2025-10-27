<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Exception;

class CountryDataImportService
{
    protected $supportedFormats = ['csv', 'xlsx', 'xls', 'txt'];
    protected $requiredFields = ['postal_code', 'city'];
    protected $optionalFields = ['sub_city', 'region', 'latitude', 'longitude', 'population'];

    /**
     * Import geographic data for a specific country
     */
    public function importCountryData(Country $country, $filePath, array $options = [])
    {
        $countryCode = strtolower($country->code);
        $tableName = "postal_codes_{$countryCode}";

        try {
            // Step 1: Create country-specific table if it doesn't exist
            $this->createCountryTable($tableName, $countryCode);

            // Step 2: Parse the import file
            $data = $this->parseImportFile($filePath, $options);

            // Step 3: Validate the data structure
            $validatedData = $this->validateImportData($data, $country);

            // Step 4: Import data in batches
            $importResult = $this->importDataInBatches($tableName, $validatedData);

            return [
                'success' => true,
                'message' => "Import erfolgreich abgeschlossen für {$country->name}",
                'stats' => $importResult,
                'table_name' => $tableName
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => "Import-Fehler: " . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Create country-specific postal codes table
     */
    protected function createCountryTable(string $tableName, string $countryCode)
    {
        if (Schema::hasTable($tableName)) {
            return; // Table already exists
        }

        Schema::create($tableName, function (Blueprint $table) use ($countryCode) {
            $table->id();
            $table->string('country_code', 2)->default(strtoupper($countryCode))->index();
            $table->string('postal_code', 20)->index(); // Variable length for different countries
            $table->string('city', 100)->index();
            $table->string('sub_city', 100)->nullable()->index(); // District/Borough
            $table->string('region', 100)->nullable()->index(); // State/Province
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('population')->nullable()->index();
            $table->json('additional_data')->nullable(); // For country-specific extra fields
            $table->timestamps();

            // Composite indexes for performance
            $table->index(['postal_code', 'city']);
            $table->index(['region', 'city']);
            $table->index(['population', 'city']);

            // Unique constraint to prevent duplicates
            $table->unique(['postal_code', 'city', 'sub_city']);
        });
    }

    /**
     * Parse import file (CSV, Excel)
     */
    protected function parseImportFile(string $filePath, array $options = []): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (!in_array($extension, $this->supportedFormats)) {
            throw new Exception("Unsupported file format: {$extension}");
        }

        try {
            if ($extension === 'csv' || $extension === 'txt') {
                return $this->parseCsvFile($filePath, $options);
            } else {
                return $this->parseExcelFile($filePath, $options);
            }
        } catch (Exception $e) {
            throw new Exception("Error parsing file: " . $e->getMessage());
        }
    }

    /**
     * Parse CSV file
     */
    protected function parseCsvFile(string $filePath, array $options = []): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        // Auto-detect delimiter for TXT files (likely tab-separated)
        if ($extension === 'txt') {
            $delimiter = $options['delimiter'] ?? "\t";
            $hasHeader = $options['has_header'] ?? false; // TXT files often don't have headers
        } else {
            $delimiter = $options['delimiter'] ?? ',';
            $hasHeader = $options['has_header'] ?? true;
        }

        $enclosure = $options['enclosure'] ?? '"';
        $escape = $options['escape'] ?? '\\';

        $data = [];
        $headers = [];

        if (!file_exists($filePath)) {
            throw new Exception("File not found: {$filePath}");
        }

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $rowNumber = 0;
            $errorRows = [];

            while (($row = fgetcsv($handle, 1000, $delimiter, $enclosure, $escape)) !== FALSE) {
                try {
                    if ($rowNumber === 0 && $hasHeader) {
                        $headers = array_map('trim', $row);
                        $headers = array_map('strtolower', $headers);
                    } else {
                        if ($hasHeader && !empty($headers)) {
                            $rowData = array_combine($headers, $row);
                        } else {
                            // Default column mapping if no headers
                            if (count($row) >= 12 && ($row[0] === 'CH' || strlen($row[0]) === 2)) {
                                // Swiss postal codes format: CH postal_code city region region_code district id municipality mun_id lat lng type
                                $rowData = [
                                    'postal_code' => trim($row[1] ?? ''),
                                    'city' => trim($row[2] ?? ''),
                                    'region' => trim($row[3] ?? null),
                                    'sub_city' => trim($row[5] ?? null), // District
                                    'latitude' => is_numeric($row[9] ?? null) ? (float) $row[9] : null,
                                    'longitude' => is_numeric($row[10] ?? null) ? (float) $row[10] : null,
                                    'population' => null, // Not available in this format
                                ];
                            } else {
                                // Standard format: postal_code city sub_city region latitude longitude population
                                $rowData = [
                                    'postal_code' => trim($row[0] ?? ''),
                                    'city' => trim($row[1] ?? ''),
                                    'sub_city' => trim($row[2] ?? null),
                                    'region' => trim($row[3] ?? null),
                                    'latitude' => is_numeric($row[4] ?? null) ? (float) $row[4] : null,
                                    'longitude' => is_numeric($row[5] ?? null) ? (float) $row[5] : null,
                                    'population' => is_numeric($row[6] ?? null) ? (int) $row[6] : null,
                                ];
                            }

                            // Clean up empty strings to null and ensure required fields
                            foreach ($rowData as $key => $value) {
                                if ($value === '' || $value === '0') {
                                    $rowData[$key] = null;
                                }
                            }

                            // Skip rows without essential data
                            if (empty($rowData['postal_code']) || empty($rowData['city'])) {
                                continue;
                            }
                        }
                        $data[] = $rowData;
                    }
                } catch (Exception $e) {
                    $errorRows[] = "Row {$rowNumber}: " . $e->getMessage();
                    // Continue processing other rows
                }
                $rowNumber++;
            }
            fclose($handle);

            // Log any row errors but don't fail completely
            if (!empty($errorRows)) {
                \Log::warning('CSV parsing warnings', ['errors' => $errorRows, 'file' => $filePath]);
            }
        } else {
            throw new Exception("Could not open file for reading: {$filePath}");
        }

        return $data;
    }

    /**
     * Parse Excel file
     */
    protected function parseExcelFile(string $filePath, array $options = []): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $data = [];
        $headers = [];
        $hasHeader = $options['has_header'] ?? true;

        foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(FALSE);
            $rowData = [];

            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            if ($rowIndex === 1 && $hasHeader) {
                $headers = array_map('trim', $rowData);
                $headers = array_map('strtolower', $headers);
            } else {
                if ($hasHeader && !empty($headers)) {
                    $combinedData = array_combine($headers, $rowData);
                } else {
                    // Default mapping
                    $combinedData = [
                        'postal_code' => $rowData[0] ?? '',
                        'city' => $rowData[1] ?? '',
                        'sub_city' => $rowData[2] ?? null,
                        'region' => $rowData[3] ?? null,
                        'latitude' => $rowData[4] ?? null,
                        'longitude' => $rowData[5] ?? null,
                        'population' => $rowData[6] ?? null,
                    ];
                }
                $data[] = $combinedData;
            }
        }

        return $data;
    }

    /**
     * Validate import data
     */
    protected function validateImportData(array $data, Country $country): array
    {
        $validatedData = [];
        $errors = [];

        foreach ($data as $index => $row) {
            $validator = Validator::make($row, [
                'postal_code' => 'required|string|max:20',
                'city' => 'required|string|max:100',
                'sub_city' => 'nullable|string|max:100',
                'region' => 'nullable|string|max:100',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'population' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                $errors[] = [
                    'row' => $index + 1,
                    'errors' => $validator->errors()->toArray()
                ];
                continue;
            }

            // Clean and format data
            $cleanRow = [
                'country_code' => strtoupper($country->code),
                'postal_code' => trim($row['postal_code']),
                'city' => trim($row['city']),
                'sub_city' => !empty($row['sub_city']) ? trim($row['sub_city']) : null,
                'region' => !empty($row['region']) ? trim($row['region']) : null,
                'latitude' => !empty($row['latitude']) ? (float) $row['latitude'] : null,
                'longitude' => !empty($row['longitude']) ? (float) $row['longitude'] : null,
                'population' => !empty($row['population']) ? (int) $row['population'] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Store additional fields in JSON if present
            $additionalData = [];
            foreach ($row as $key => $value) {
                if (!in_array($key, ['postal_code', 'city', 'sub_city', 'region', 'latitude', 'longitude', 'population'])) {
                    $additionalData[$key] = $value;
                }
            }
            if (!empty($additionalData)) {
                $cleanRow['additional_data'] = json_encode($additionalData);
            }

            $validatedData[] = $cleanRow;
        }

        if (!empty($errors)) {
            throw new Exception("Validation errors found in " . count($errors) . " rows: " . json_encode($errors));
        }

        return $validatedData;
    }

    /**
     * Import data in batches for performance
     */
    protected function importDataInBatches(string $tableName, array $data): array
    {
        $batchSize = 1000;
        $totalRows = count($data);
        $insertedRows = 0;
        $updatedRows = 0;
        $skippedRows = 0;

        $batches = array_chunk($data, $batchSize);

        foreach ($batches as $batch) {
            try {
                // Use INSERT IGNORE or UPSERT to handle duplicates
                $inserted = DB::table($tableName)->insertOrIgnore($batch);
                $insertedRows += $inserted;
            } catch (Exception $e) {
                // Handle individual rows if batch fails
                foreach ($batch as $row) {
                    try {
                        DB::table($tableName)->insertOrIgnore($row);
                        $insertedRows++;
                    } catch (Exception $e) {
                        $skippedRows++;
                    }
                }
            }
        }

        return [
            'total_rows' => $totalRows,
            'inserted_rows' => $insertedRows,
            'updated_rows' => $updatedRows,
            'skipped_rows' => $skippedRows,
            'table_name' => $tableName
        ];
    }

    /**
     * Get import statistics for a country
     */
    public function getImportStats(Country $country): array
    {
        $countryCode = strtolower($country->code);
        $tableName = "postal_codes_{$countryCode}";

        if (!Schema::hasTable($tableName)) {
            return [
                'table_exists' => false,
                'total_records' => 0,
                'unique_cities' => 0,
                'unique_regions' => 0,
                'last_import' => null
            ];
        }

        $stats = [
            'table_exists' => true,
            'table_name' => $tableName,
            'total_records' => DB::table($tableName)->count(),
            'unique_cities' => DB::table($tableName)->distinct('city')->count(),
            'unique_regions' => DB::table($tableName)->distinct('region')->count(),
            'records_with_coordinates' => DB::table($tableName)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->count(),
            'records_with_population' => DB::table($tableName)
                ->whereNotNull('population')
                ->count(),
            'last_import' => DB::table($tableName)
                ->orderBy('created_at', 'desc')
                ->value('created_at')
        ];

        return $stats;
    }

    /**
     * Clear all data for a country
     */
    public function clearCountryData(Country $country): bool
    {
        $countryCode = strtolower($country->code);
        $tableName = "postal_codes_{$countryCode}";

        if (!Schema::hasTable($tableName)) {
            return false;
        }

        DB::table($tableName)->truncate();
        return true;
    }

    /**
     * Drop country table
     */
    public function dropCountryTable(Country $country): bool
    {
        $countryCode = strtolower($country->code);
        $tableName = "postal_codes_{$countryCode}";

        if (!Schema::hasTable($tableName)) {
            return false;
        }

        Schema::dropIfExists($tableName);
        return true;
    }

    /**
     * Get sample data structure for preview
     */
    public function getDataPreview(string $filePath, array $options = [], int $limit = 5): array
    {
        try {
            $data = $this->parseImportFile($filePath, $options);
            return array_slice($data, 0, $limit);
        } catch (Exception $e) {
            throw new Exception("Preview error: " . $e->getMessage());
        }
    }

    /**
     * Validate file before import
     */
    public function validateImportFile(string $filePath): array
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $fileSize = filesize($filePath);
        $maxSize = 50 * 1024 * 1024; // 50MB

        $validation = [
            'valid' => true,
            'errors' => [],
            'warnings' => [],
            'file_info' => [
                'extension' => $extension,
                'size' => $fileSize,
                'size_formatted' => $this->formatBytes($fileSize)
            ]
        ];

        // Check file format
        if (!in_array($extension, $this->supportedFormats)) {
            $validation['valid'] = false;
            $validation['errors'][] = "Unsupported file format: {$extension}. Supported formats: " . implode(', ', $this->supportedFormats);
        }

        // Check file size
        if ($fileSize > $maxSize) {
            $validation['valid'] = false;
            $validation['errors'][] = "File too large. Maximum size: " . $this->formatBytes($maxSize);
        }

        // Check if file is readable
        if (!is_readable($filePath)) {
            $validation['valid'] = false;
            $validation['errors'][] = "File is not readable";
        }

        return $validation;
    }

    /**
     * Format bytes to human readable format
     */
    protected function formatBytes($bytes, $precision = 2): string
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}