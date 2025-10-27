<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rental;
use Illuminate\Support\Facades\Storage; // Added for file storage
use App\Helpers\DynamicRentalFields;

class VendorController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        return view('content.vendor.dashboard');
    }

    // Rentals (Vermietungsobjekte) Liste
    public function rentals()
    {
        $rentals = Rental::with(['category', 'location', 'additionalLocations', 'images'])
            ->where('vendor_id', auth()->id())
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($rental) {
                // Bild-URL und Bildanzahl
                $image = $rental->images && $rental->images->count() > 0
                    ? $rental->images->sortBy('order')->first()
                    : null;
                $image_url = $image ? asset('storage/' . $image->path) : asset('assets/img/backgrounds/4.jpg');
                $image_count = $rental->images ? $rental->images->count() : 0;
                // Artikelnummer (z.B. führende Nullen)
                $number = str_pad($rental->id, 6, '0', STR_PAD_LEFT);
                // Standorte
                $locations = collect([$rental->location])->merge($rental->additionalLocations);
                $locations_label = $locations->pluck('name')->unique()->implode(', ');
                $has_multiple_locations = $locations->count() > 1;
                // Preis-Label
                if ($rental->price_range_hour) {
                    $price_label = number_format($rental->price_range_hour, 2, ',', '.') . ' ' . ($rental->currency ?? 'EUR') . ' pro Stunde';
                } elseif ($rental->price_range_day) {
                    $price_label = number_format($rental->price_range_day, 2, ',', '.') . ' ' . ($rental->currency ?? 'EUR') . ' pro Tag';
                } elseif ($rental->price_range_once) {
                    $price_label = number_format($rental->price_range_once, 2, ',', '.') . ' ' . ($rental->currency ?? 'EUR') . ' pro Auftritt';
                } else {
                    $price_label = 'Preis auf Anfrage';
                }
                return (object) array_merge($rental->toArray(), [
                    'image_url' => $image_url,
                    'image_count' => $image_count,
                    'number' => $number,
                    'locations_label' => $locations_label,
                    'has_multiple_locations' => $has_multiple_locations,
                    'price_label' => $price_label,
                    'created_at' => $rental->created_at, // Preserve Carbon object
                    'updated_at' => $rental->updated_at, // Preserve Carbon object
                ]);
            });
        return view('content.vendor.rentals.index', compact('rentals'));
    }

    // Rental bearbeiten/erstellen
    public function rental($id = null)
    {
        if ($id) {
            // Bestehendes Rental laden
            $rental = Rental::with(['category', 'location', 'additionalLocations', 'images', 'documents'])
                ->where('vendor_id', auth()->id())
                ->findOrFail($id);

            return view('content.vendor.rentals.edit', compact('rental', 'id'));
        } else {
            // Neues Rental erstellen
            return view('content.vendor.rentals.create');
        }
    }

    // Kategorie auswählen
    public function rentalCategory($id = null)
    {
        return view('content.vendor.rentals.category', ['id' => $id]);
    }

    // Rental Vorschau
    public function rentalPreview($id)
    {
        return view('content.vendor.rentals.preview', ['id' => $id]);
    }

    // Statistiken
    public function statistics()
    {
        return view('content.vendor.statistics.index');
    }

    // Nachrichten
    public function messages()
    {
        return view('content.vendor.messages.index');
    }

    // Persönliche Daten
    public function profile()
    {
        return view('content.vendor.profile.index');
    }

    // Rechnungen
    public function bills()
    {
        return view('content.vendor.bills.index');
    }

    // Guthaben
    public function credits()
    {
        return view('content.vendor.credits.index');
    }

    // Rental speichern
    public function saveRental(Request $request, $id = null)
    {
        try {

            // Validierung der Eingabedaten
            $validated = $request->validate([
                'rental_title' => 'required|string|max:255',
                'rental_description' => 'required|string',
                'location_id' => 'required', // Allow both string and array
                'category_id' => 'required|exists:categories,id',
                'price_ranges_id' => 'required|exists:price_ranges,id',
                'price_range_hour' => 'required_if:price_ranges_id,1|numeric|min:0',
                'price_range_day' => 'required_if:price_ranges_id,2|numeric|min:0',
                'price_range_once' => 'required_if:price_ranges_id,3|numeric|min:0',
                'service_fee' => 'nullable|numeric|min:0',

                'rental_terms_condition' => 'nullable|file|mimes:pdf|max:10240',
                'rental_specifications' => 'nullable|file|mimes:pdf|max:10240',
                'rental_owner_info' => 'nullable|file|mimes:pdf|max:10240',
                'rental_contract' => 'nullable|file|mimes:pdf|max:10240',
                'rental_directions' => 'nullable|file|mimes:pdf|max:10240',
                'rental_catalog' => 'nullable|file|mimes:pdf|max:10240',
                'rental_floor_plan' => 'nullable|file|mimes:pdf|max:10240',
                'rental_prices_seasons' => 'nullable|file|mimes:pdf|max:10240',
                'rental_safety_info' => 'nullable|file|mimes:pdf|max:10240',
                'rental_additional_info' => 'nullable|file|mimes:pdf|max:10240',
            ]);

            // Daten für das Rental vorbereiten
            $locationInput = $validated['location_id'];
            // Handle different location input formats
            if (is_array($locationInput)) {
                // If it's already an array of IDs
                $locationIds = array_map('intval', $locationInput);
            } elseif (is_string($locationInput)) {
                // If it's a comma-separated string
                $locationIds = array_map('intval', array_filter(explode(',', $locationInput)));
            } else {
                // Single ID
                $locationIds = [(int) $locationInput];
            }

            // Get the primary location ID (first one)
            $primaryLocationId = !empty($locationIds) ? $locationIds[0] : null;

            if (!$primaryLocationId || !is_numeric($primaryLocationId)) {
                throw new \Exception('Ungültige Standort-ID: ' . json_encode($locationInput));
            }

            // Ensure it's an integer
            $primaryLocationId = (int) $primaryLocationId;


            $rentalData = [
                'title' => $validated['rental_title'],
                'description' => $validated['rental_description'],
                'location_id' => $primaryLocationId, // Ersten Standort als Hauptstandort verwenden
                'category_id' => $validated['category_id'],
                'price_ranges_id' => $validated['price_ranges_id'],
                'price_range_hour' => $validated['price_range_hour'] ?? null,
                'price_range_day' => $validated['price_range_day'] ?? null,
                'price_range_once' => $validated['price_range_once'] ?? null,
                'service_fee' => $validated['service_fee'] ?? 0,
                'vendor_id' => auth()->id(),
                'status' => 'draft'
            ];

            // Debug: Log the prepared rental data
//            \Log::info('Prepared rental data:', $rentalData);

            // Rental erstellen oder aktualisieren
            $rental = $id ? Rental::findOrFail($id) : new Rental();
            $rental->fill($rentalData);
            $rental->save();

            // Zusätzliche Standorte speichern (if more than one location selected)
            if (count($locationIds) > 1) {
                $additionalLocationIds = array_slice($locationIds, 1); // Skip first location (primary)
                $additionalLocationIds = array_map('intval', $additionalLocationIds); // Ensure integers
                $rental->additionalLocations()->sync($additionalLocationIds);
            } else {
                // Clear any existing additional locations if only one location is selected
                $rental->additionalLocations()->sync([]);
            }



            // Dokumente speichern
            $documentTypes = [
                'rental_terms_condition' => 'terms_condition',
                'rental_specifications' => 'specifications',
                'rental_owner_info' => 'owner_info',
                'rental_contract' => 'contract',
                'rental_directions' => 'directions',
                'rental_catalog' => 'catalog',
                'rental_floor_plan' => 'floor_plan',
                'rental_prices_seasons' => 'prices_seasons',
                'rental_safety_info' => 'safety_info',
                'rental_additional_info' => 'additional_info'
            ];

            foreach ($documentTypes as $requestKey => $type) {
                if ($request->hasFile($requestKey)) {
                    $file = $request->file($requestKey);
                    $path = $file->store('rentals/' . $rental->id . '/documents', 'public');
                    $rental->documents()->create([
                        'type' => $type,
                        'path' => $path,
                        'original_name' => $file->getClientOriginalName()
                    ]);
                }
            }

            // Save dynamic field values if present in request or session
            $dynamicFieldValues = [];

            // Collect dynamic field values from request
            if ($rental->category_id) {
                $templates = DynamicRentalFields::getActiveTemplatesForCategory($rental->category_id);
                foreach ($templates as $template) {
                    foreach ($template->fields as $field) {
                        $fieldName = 'fieldValues.' . $field->field_name;
                        if ($request->has($fieldName)) {
                            $dynamicFieldValues[$field->field_name] = $request->input($fieldName);
                        }
                    }
                }
            }

            // Also check session for pending values (from Livewire component)
            if (session()->has('pending_field_values')) {
                $dynamicFieldValues = array_merge($dynamicFieldValues, session('pending_field_values'));
                session()->forget('pending_field_values');
            }

            // Save dynamic field values if any were provided
            if (!empty($dynamicFieldValues)) {
                DynamicRentalFields::saveFieldValues($rental->id, $dynamicFieldValues);
            }

            // Determine redirect route based on whether we're creating or editing
            if ($id) {
                // Editing: redirect back to edit view with success message
                return redirect()->route('vendor-rental-edit', ['id' => $rental->id])
                    ->with('success', 'Vermietungsobjekt erfolgreich aktualisiert.');
            } else {
                // Creating: redirect to edit view of newly created rental
                return redirect()->route('vendor-rental-edit', ['id' => $rental->id])
                    ->with('success', 'Vermietungsobjekt erfolgreich erstellt.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // For validation errors, redirect back with errors and input
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();

        } catch (\Exception $e) {
            // For other errors, redirect back with error message
            return redirect()->back()
                ->with('error', 'Fehler beim Speichern: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Bulk Actions für Rentals
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:rentals,id'
        ]);

        $vendorId = auth()->id();
        $action = $request->action;
        $rentalIds = $request->ids;

        // Ensure all rentals belong to the current vendor
        $rentals = Rental::whereIn('id', $rentalIds)
            ->where('vendor_id', $vendorId)
            ->get();

        if ($rentals->count() !== count($rentalIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Einige Vermietungsobjekte gehören nicht zu Ihrem Account.'
            ], 403);
        }

        try {
            switch ($action) {
                case 'activate':
                    Rental::whereIn('id', $rentalIds)
                        ->where('vendor_id', $vendorId)
                        ->update(['status' => 'active']);
                    $message = count($rentalIds) . ' Objekt(e) erfolgreich aktiviert.';
                    break;

                case 'deactivate':
                    Rental::whereIn('id', $rentalIds)
                        ->where('vendor_id', $vendorId)
                        ->update(['status' => 'inactive']);
                    $message = count($rentalIds) . ' Objekt(e) erfolgreich deaktiviert.';
                    break;

                case 'delete':
                    // Soft delete
                    Rental::whereIn('id', $rentalIds)
                        ->where('vendor_id', $vendorId)
                        ->delete();
                    $message = count($rentalIds) . ' Objekt(e) erfolgreich gelöscht.';
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler bei der Ausführung: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Duplicate a rental
     */
    public function duplicateRental($id)
    {
        try {
            $vendorId = auth()->id();

            // Find the original rental
            $originalRental = Rental::where('id', $id)
                ->where('vendor_id', $vendorId)
                ->with(['images', 'category', 'additionalLocations'])
                ->firstOrFail();

            // Create new rental with duplicated data
            $duplicatedData = $originalRental->toArray();

            // Remove unique fields and modify title
            unset($duplicatedData['id'], $duplicatedData['created_at'], $duplicatedData['updated_at']);
            $duplicatedData['title'] = 'Kopie von ' . $originalRental->title;
            $duplicatedData['status'] = 'draft'; // Set as draft

            // Create the duplicated rental
            $newRental = Rental::create($duplicatedData);

            // Duplicate images if they exist
            if ($originalRental->images->count() > 0) {
                foreach ($originalRental->images as $image) {
                    // Copy the image file
                    $originalPath = $image->path;
                    $newPath = str_replace(
                        'rentals/' . $originalRental->id . '/',
                        'rentals/' . $newRental->id . '/',
                        $originalPath
                    );

                    // Create directory if it doesn't exist
                    $directory = dirname(storage_path('app/public/' . $newPath));
                    if (!file_exists($directory)) {
                        mkdir($directory, 0755, true);
                    }

                    // Copy file if original exists
                    if (Storage::disk('public')->exists($originalPath)) {
                        Storage::disk('public')->copy($originalPath, $newPath);

                        // Create new image record
                        $newRental->images()->create([
                            'path' => $newPath,
                            'order' => $image->order
                        ]);
                    }
                }
            }

            // Duplicate location relationships
            if ($originalRental->additionalLocations->count() > 0) {
                $locationIds = $originalRental->additionalLocations->pluck('id')->toArray();
                $newRental->additionalLocations()->sync($locationIds);
            }

            return response()->json([
                'success' => true,
                'message' => 'Vermietungsobjekt erfolgreich dupliziert.',
                'redirect' => route('vendor-rental-edit', ['id' => $newRental->id])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Duplizieren: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle rental status (active/inactive)
     */
    public function toggleRentalStatus($id)
    {
        try {
            $vendorId = auth()->id();

            $rental = Rental::where('id', $id)
                ->where('vendor_id', $vendorId)
                ->firstOrFail();

            // Toggle status
            $newStatus = $rental->status === 'active' ? 'inactive' : 'active';
            $rental->update(['status' => $newStatus]);

            $statusText = $newStatus === 'active' ? 'aktiviert' : 'deaktiviert';

            return response()->json([
                'success' => true,
                'message' => "Vermietungsobjekt wurde erfolgreich {$statusText}.",
                'new_status' => $newStatus
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Ändern des Status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a single rental
     */
    public function deleteRental($id)
    {
        try {
            $vendorId = auth()->id();

            $rental = Rental::where('id', $id)
                ->where('vendor_id', $vendorId)
                ->firstOrFail();

            // Soft delete
            $rental->delete();

            return response()->json([
                'success' => true,
                'message' => 'Vermietungsobjekt erfolgreich gelöscht.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Löschen: ' . $e->getMessage()
            ], 500);
        }
    }

    // Load dynamic fields for category via AJAX
    public function loadDynamicFields($categoryId)
    {
        try {
            $category = \App\Models\Category::findOrFail($categoryId);

            // Render the Livewire component directly
            $html = \Livewire\Livewire::mount(\App\Livewire\Vendor\DynamicRentalForm::class, [
                'categoryId' => $categoryId,
                'rental' => null
            ]);

            return response($html, 200, [
                'Content-Type' => 'text/html'
            ]);

        } catch (\Exception $e) {
            \Log::error('Dynamic fields loading error:', [
                'categoryId' => $categoryId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $errorHtml = '
                <div class="alert alert-danger">
                    <i class="ti ti-alert-triangle me-2"></i>
                    <strong>Fehler beim Laden der Felder</strong><br>
                    ' . $e->getMessage() . '<br>
                    <small class="text-muted">Bitte versuchen Sie es erneut oder wählen Sie eine andere Kategorie.</small>
                </div>
            ';

            return response($errorHtml, 500, [
                'Content-Type' => 'text/html'
            ]);
        }
    }
}
