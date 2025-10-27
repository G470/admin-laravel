<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentalFieldTemplate;
use App\Models\RentalField;
use App\Models\Category;
use App\Helpers\DynamicRentalFields;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RentalFieldTemplateController extends Controller
{
    /**
     * Display a listing of the templates.
     */
    public function index(Request $request): View
    {
        $query = RentalFieldTemplate::with(['categories', 'fields'])
            ->withCount(['fields', 'categories']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $isActive = $request->get('status') === 'active';
            $query->where('is_active', $isActive);
        }

        // Sort by column
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortDirection = $request->get('sort_direction', 'asc');

        if (in_array($sortBy, ['name', 'created_at', 'sort_order'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('sort_order')->orderBy('name');
        }

        $templates = $query->paginate(15);
        $categories = Category::orderBy('name')->get();

        return view('content.admin.rental-field-templates.index', compact('templates', 'categories'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $fieldTypes = DynamicRentalFields::getAvailableFieldTypes();

        return view('content.admin.rental-field-templates.create', compact('categories', 'fieldTypes'));
    }

    /**
     * Store a newly created template in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rental_field_templates,name',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
            'fields' => 'array',
            'fields.*.field_name' => 'required|string|max:255',
            'fields.*.field_label' => 'required|string|max:255',
            'fields.*.field_type' => 'required|in:' . implode(',', array_keys(DynamicRentalFields::getAvailableFieldTypes())),
            'fields.*.is_required' => 'boolean',
            'fields.*.is_filterable' => 'boolean',
            'fields.*.is_searchable' => 'boolean',
            'fields.*.sort_order' => 'integer|min:0',
        ]);

        $template = RentalFieldTemplate::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->integer('sort_order', 0),
            'settings' => $request->settings ?? [],
        ]);

        // Attach categories
        if ($request->filled('categories')) {
            $template->categories()->attach($request->categories);
        }

        // Create fields
        if ($request->filled('fields')) {
            foreach ($request->fields as $fieldData) {
                $fieldErrors = DynamicRentalFields::validateFieldConfiguration($fieldData);
                if (empty($fieldErrors)) {
                    $template->fields()->create([
                        'field_type' => $fieldData['field_type'],
                        'field_name' => $fieldData['field_name'],
                        'field_label' => $fieldData['field_label'],
                        'field_description' => $fieldData['field_description'] ?? null,
                        'options' => $fieldData['options'] ?? null,
                        'validation_rules' => $fieldData['validation_rules'] ?? null,
                        'dependencies' => $fieldData['dependencies'] ?? null,
                        'seo_settings' => $fieldData['seo_settings'] ?? null,
                        'is_required' => $fieldData['is_required'] ?? false,
                        'is_filterable' => $fieldData['is_filterable'] ?? true,
                        'is_searchable' => $fieldData['is_searchable'] ?? true,
                        'sort_order' => $fieldData['sort_order'] ?? 0,
                    ]);
                }
            }
        }

        return redirect()
            ->route('admin.rental-field-templates.show', $template)
            ->with('success', 'Template erfolgreich erstellt!');
    }

    /**
     * Display the specified template.
     */
    public function show(RentalFieldTemplate $rentalFieldTemplate): View
    {
        $rentalFieldTemplate->load([
            'fields' => function ($query) {
                $query->ordered();
            },
            'categories'
        ]);

        $usageStats = $rentalFieldTemplate->id ? DynamicRentalFields::getTemplateUsageStats($rentalFieldTemplate->id) : [];

        return view('content.admin.rental-field-templates.show', compact('rentalFieldTemplate', 'usageStats'));
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(RentalFieldTemplate $rentalFieldTemplate): View
    {
        $rentalFieldTemplate->load([
            'fields' => function ($query) {
                $query->ordered();
            },
            'categories'
        ]);

        $categories = Category::orderBy('name')->get();
        $fieldTypes = DynamicRentalFields::getAvailableFieldTypes();

        return view('content.admin.rental-field-templates.edit', compact('rentalFieldTemplate', 'categories', 'fieldTypes'));
    }

    /**
     * Update the specified template in storage.
     */
    public function update(Request $request, RentalFieldTemplate $rentalFieldTemplate): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:rental_field_templates,name,' . $rentalFieldTemplate->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
        ]);

        $rentalFieldTemplate->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'sort_order' => $request->integer('sort_order', 0),
            'settings' => $request->settings ?? $rentalFieldTemplate->settings,
        ]);

        // Sync categories
        $rentalFieldTemplate->categories()->sync($request->categories ?? []);

        return redirect()
            ->route('admin.rental-field-templates.show', $rentalFieldTemplate)
            ->with('success', 'Template erfolgreich aktualisiert!');
    }

    /**
     * Remove the specified template from storage.
     */
    public function destroy(RentalFieldTemplate $rentalFieldTemplate): RedirectResponse
    {
        if (!$rentalFieldTemplate->canBeDeleted()) {
            return redirect()
                ->route('admin.rental-field-templates.index')
                ->with('error', 'Das Template kann nicht gelöscht werden, da es bereits verwendet wird.');
        }

        $rentalFieldTemplate->delete();

        return redirect()
            ->route('admin.rental-field-templates.index')
            ->with('success', 'Template erfolgreich gelöscht!');
    }

    /**
     * Duplicate a template
     */
    public function duplicate(RentalFieldTemplate $rentalFieldTemplate): RedirectResponse
    {
        $newTemplate = $rentalFieldTemplate->duplicate();

        return redirect()
            ->route('admin.rental-field-templates.edit', $newTemplate)
            ->with('success', 'Template erfolgreich dupliziert!');
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(RentalFieldTemplate $rentalFieldTemplate): JsonResponse
    {
        $rentalFieldTemplate->update([
            'is_active' => !$rentalFieldTemplate->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $rentalFieldTemplate->is_active,
            'message' => $rentalFieldTemplate->is_active ? 'Template aktiviert' : 'Template deaktiviert'
        ]);
    }

    /**
     * Export template data
     */
    public function export(RentalFieldTemplate $rentalFieldTemplate): JsonResponse
    {
        $data = DynamicRentalFields::exportTemplateData($rentalFieldTemplate->id);

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="template-' . $rentalFieldTemplate->id . '.json"');
    }

    /**
     * Import template data
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'template_data' => 'required|json'
        ]);

        $templateData = json_decode($request->template_data, true);
        $template = DynamicRentalFields::importTemplateData($templateData);

        if (!$template) {
            return redirect()
                ->route('admin.rental-field-templates.index')
                ->with('error', 'Import fehlgeschlagen. Bitte überprüfen Sie die Daten.');
        }

        return redirect()
            ->route('admin.rental-field-templates.show', $template)
            ->with('success', 'Template erfolgreich importiert!');
    }

    /**
     * Get templates for specific category (AJAX)
     */
    public function getForCategory(Request $request): JsonResponse
    {
        $categoryId = $request->get('category_id');

        $templates = DynamicRentalFields::getActiveTemplatesForCategory($categoryId);

        return response()->json([
            'templates' => $templates->map(function ($template) {
                return [
                    'id' => $template->id,
                    'name' => $template->name,
                    'description' => $template->description,
                    'field_count' => $template->fields->count(),
                    'fields' => $template->fields->map(function ($field) {
                        return [
                            'id' => $field->id,
                            'field_name' => $field->field_name,
                            'field_label' => $field->field_label,
                            'field_type' => $field->field_type,
                            'is_required' => $field->is_required,
                            'options' => $field->options,
                        ];
                    })
                ];
            })
        ]);
    }

    /**
     * Reorder templates
     */
    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'templates' => 'required|array',
            'templates.*.id' => 'required|exists:rental_field_templates,id',
            'templates.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->templates as $templateData) {
            RentalFieldTemplate::where('id', $templateData['id'])
                ->update(['sort_order' => $templateData['sort_order']]);
        }

        return response()->json(['success' => true, 'message' => 'Reihenfolge erfolgreich aktualisiert!']);
    }

    /**
     * Get usage statistics for a template
     */
    public function getUsageStats(RentalFieldTemplate $rentalFieldTemplate): JsonResponse
    {
        $stats = DynamicRentalFields::getTemplateUsageStats($rentalFieldTemplate->id);

        return response()->json($stats);
    }

    /**
     * Delete a field from a template
     */
    public function deleteField(RentalFieldTemplate $template, $fieldId)
    {
        try {
            $field = $template->fields()->findOrFail($fieldId);
            $field->delete();

            return response()->json([
                'success' => true,
                'message' => 'Feld erfolgreich gelöscht'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Löschen des Feldes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific field for editing
     */
    public function getField(RentalFieldTemplate $template, $fieldId)
    {
        try {
            $field = $template->fields()->findOrFail($fieldId);
            return response()->json($field);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Feld nicht gefunden'
            ], 404);
        }
    }

    /**
     * Store a new field for a template
     */
    public function storeField(Request $request, RentalFieldTemplate $template)
    {
        try {
            $validated = $request->validate([
                'field_name' => 'required|string|max:255|unique:rental_fields,field_name,NULL,id,template_id,' . $template->id,
                'field_label' => 'required|string|max:255',
                'field_description' => 'nullable|string',
                'field_type' => 'required|string|in:text,textarea,number,select,radio,checkbox,date,time,datetime',
                'is_required' => 'boolean',
                'is_filterable' => 'boolean',
                'is_searchable' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
                'options' => 'nullable|array'
            ]);

            $field = $template->fields()->create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Feld erfolgreich hinzugefügt',
                'field' => $field
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Hinzufügen des Feldes: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a specific field
     */
    public function updateField(Request $request, RentalFieldTemplate $template, $fieldId)
    {
        try {
            $field = $template->fields()->findOrFail($fieldId);

            $validated = $request->validate([
                'field_label' => 'required|string|max:255',
                'field_description' => 'nullable|string',
                'is_required' => 'boolean',
                'is_filterable' => 'boolean',
                'is_searchable' => 'boolean',
                'sort_order' => 'nullable|integer|min:0',
                'options' => 'nullable|array'
            ]);

            $field->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Feld erfolgreich aktualisiert'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validierungsfehler',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fehler beim Aktualisieren des Feldes: ' . $e->getMessage()
            ], 500);
        }
    }
}
