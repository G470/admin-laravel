<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CountryPostalCode;
use App\Services\CountryDataImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CountryController extends Controller
{
    /**
     * Display a listing of countries using Livewire component
     */
    public function index()
    {
        return view('content.admin.countries');
    }

    /**
     * Show the form for creating a new country
     */
    public function create()
    {
        return view('content.admin.countries-create');
    }

    /**
     * Store a newly created country
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:2|unique:countries,code',
            'phone_code' => 'nullable|string|max:5',
            'is_active' => 'boolean',
        ]);

        // Ensure proper formatting
        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $validated['is_active'] ?? true;

        Country::create($validated);

        return redirect()->route('admin.countries.index')
            ->with('success', 'Land wurde erfolgreich erstellt.');
    }

    /**
     * Display the specified country
     */
    public function show(Country $country)
    {
        return view('content.admin.countries-show', compact('country'));
    }

    /**
     * Show the form for editing the specified country
     */
    public function edit(Country $country)
    {
        return view('content.admin.countries-edit', compact('country'));
    }

    /**
     * Update the specified country
     */
    public function update(Request $request, Country $country)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|size:2|unique:countries,code,' . $country->id,
            'phone_code' => 'nullable|string|max:5',
            'is_active' => 'boolean',
        ]);

        // Ensure proper formatting
        $validated['code'] = strtoupper($validated['code']);
        $validated['is_active'] = $validated['is_active'] ?? $country->is_active;

        $country->update($validated);

        return redirect()->route('admin.countries.index')
            ->with('success', 'Land wurde erfolgreich aktualisiert.');
    }

    /**
     * Remove the specified country from storage
     */
    public function destroy(Country $country)
    {
        // Check if country is in use by locations
        $locationsCount = $country->locations()->count();

        if ($locationsCount > 0) {
            return redirect()->route('admin.countries.index')
                ->with('error', "Land kann nicht gelöscht werden. Es wird von {$locationsCount} Standort(en) verwendet.");
        }

        $country->delete();

        return redirect()->route('admin.countries.index')
            ->with('success', 'Land wurde erfolgreich gelöscht.');
    }

    /**
     * Toggle active status
     */
    public function toggle(Country $country)
    {
        $country->update(['is_active' => !$country->is_active]);

        $status = $country->is_active ? 'aktiviert' : 'deaktiviert';

        return response()->json([
            'success' => true,
            'message' => "Land wurde {$status}.",
            'is_active' => $country->is_active
        ]);
    }

    /**
     * Show data import form for a country
     */
    public function importForm(Country $country)
    {
        $importService = new CountryDataImportService();
        $stats = $importService->getImportStats($country);

        return view('content.admin.countries-import', compact('country', 'stats'));
    }

    /**
     * Handle file upload and import preview
     */
    public function importPreview(Request $request, Country $country)
    {
        try {
            $request->validate([
                'import_file' => 'required|file|mimes:csv,xlsx,xls,txt|max:51200', // 50MB
                'has_header' => 'boolean',
                'delimiter' => 'nullable|string|max:10', // Allow longer delimiters
            ]);

            $file = $request->file('import_file');
            $tempPath = $file->storeAs('imports/temp', uniqid() . '_' . $file->getClientOriginalName());
            $fullPath = Storage::path($tempPath);

            $importService = new CountryDataImportService();

            // Validate file
            $validation = $importService->validateImportFile($fullPath);
            if (!$validation['valid']) {
                Storage::delete($tempPath);
                return response()->json([
                    'success' => false,
                    'errors' => $validation['errors']
                ], 422);
            }

            // Get preview with error handling
            $options = [
                'has_header' => $request->boolean('has_header', true),
                'delimiter' => $request->input('delimiter', ',')
            ];

            // Convert tab placeholder to actual tab character
            if ($options['delimiter'] === 'tab' || $options['delimiter'] === '\\t') {
                $options['delimiter'] = "\t";
            }

            $preview = $importService->getDataPreview($fullPath, $options, 10);

            return response()->json([
                'success' => true,
                'preview' => $preview,
                'file_path' => $tempPath,
                'file_info' => $validation['file_info'],
                'options' => $options,
                'country' => [
                    'id' => $country->id,
                    'name' => $country->name,
                    'code' => $country->code
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            if (isset($tempPath)) {
                Storage::delete($tempPath);
            }

            \Log::error('Country import preview error: ' . $e->getMessage(), [
                'country_id' => $country->id,
                'file_info' => $request->file('import_file') ? [
                    'name' => $request->file('import_file')->getClientOriginalName(),
                    'size' => $request->file('import_file')->getSize(),
                    'type' => $request->file('import_file')->getMimeType()
                ] : null,
                'options' => $options ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Verarbeiten der Datei: ' . $e->getMessage(),
                'debug' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    /**
     * Execute the import
     */
    public function executeImport(Request $request, Country $country)
    {
        $request->validate([
            'file_path' => 'required|string',
            'has_header' => 'boolean',
            'delimiter' => 'nullable|string|max:1',
        ]);

        try {
            $filePath = Storage::path($request->input('file_path'));

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Import-Datei nicht gefunden.'
                ], 404);
            }

            $options = [
                'has_header' => $request->boolean('has_header', true),
                'delimiter' => $request->input('delimiter', ',')
            ];

            $importService = new CountryDataImportService();
            $result = $importService->importCountryData($country, $filePath, $options);

            // Clean up temp file
            Storage::delete($request->input('file_path'));

            return response()->json($result);

        } catch (\Exception $e) {
            // Clean up temp file on error
            if ($request->input('file_path')) {
                Storage::delete($request->input('file_path'));
            }

            return response()->json([
                'success' => false,
                'message' => 'Import-Fehler: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get import statistics for a country
     */
    public function importStats(Country $country)
    {
        $importService = new CountryDataImportService();
        $stats = $importService->getImportStats($country);

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'country' => [
                'id' => $country->id,
                'name' => $country->name,
                'code' => $country->code
            ]
        ]);
    }

    /**
     * Clear all postal code data for a country
     */
    public function clearData(Country $country)
    {
        $importService = new CountryDataImportService();
        $result = $importService->clearCountryData($country);

        if ($result) {
            return response()->json([
                'success' => true,
                'message' => "Alle Daten für {$country->name} wurden gelöscht."
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Keine Daten zum Löschen vorhanden oder Tabelle existiert nicht."
            ], 404);
        }
    }

    /**
     * Export postal code data for a country
     */
    public function exportData(Country $country)
    {
        try {
            $data = CountryPostalCode::exportCountryData($country->code);

            if ($data->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keine Daten zum Exportieren vorhanden.'
                ], 404);
            }

            $filename = "postal_codes_{$country->code}_" . date('Y-m-d_H-i-s') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];

            $callback = function () use ($data) {
                $file = fopen('php://output', 'w');

                // Write header
                fputcsv($file, [
                    'postal_code',
                    'city',
                    'sub_city',
                    'region',
                    'latitude',
                    'longitude',
                    'population'
                ]);

                // Write data
                foreach ($data as $row) {
                    fputcsv($file, [
                        $row->postal_code,
                        $row->city,
                        $row->sub_city,
                        $row->region,
                        $row->latitude,
                        $row->longitude,
                        $row->population
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export-Fehler: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View postal code data for a country
     */
    public function viewData(Country $country)
    {
        $importService = new CountryDataImportService();
        $stats = $importService->getImportStats($country);

        return view('content.admin.countries-data', compact('country', 'stats'));
    }

    /**
     * API endpoint for postal code data table
     */
    public function getDataTable(Request $request, Country $country)
    {
        try {
            $query = CountryPostalCode::getForCountry($country->code);

            // Search functionality
            if ($request->filled('search')) {
                $query->search($request->input('search'));
            }

            // Region filter
            if ($request->filled('region')) {
                $query->inRegion($request->input('region'));
            }

            // Coordinates filter
            if ($request->boolean('has_coordinates')) {
                $query->withCoordinates();
            }

            // Population filter
            if ($request->boolean('has_population')) {
                $query->withPopulation();
            }

            // Sorting
            $sortField = $request->input('sort_field', 'postal_code');
            $sortDirection = $request->input('sort_direction', 'asc');
            $query->orderBy($sortField, $sortDirection);

            // Pagination
            $perPage = min($request->input('per_page', 25), 100);
            $data = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Laden der Daten: ' . $e->getMessage()
            ], 500);
        }
    }
}