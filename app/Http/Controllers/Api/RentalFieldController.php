<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RentalFieldTemplate;
use App\Models\RentalField;
use App\Models\RentalFieldValue;
use App\Helpers\DynamicRentalFields;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RentalFieldController extends Controller
{
    /**
     * Get all active templates
     */
    public function getTemplates(): JsonResponse
    {
        $templates = RentalFieldTemplate::with(['fields', 'categories'])
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $templates
        ]);
    }

    /**
     * Get templates by category
     */
    public function getTemplatesByCategory($categoryId): JsonResponse
    {
        $templates = RentalFieldTemplate::with(['fields'])
            ->whereHas('categories', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })
            ->where('is_active', true)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $templates
        ]);
    }

    /**
     * Get fields for a specific template
     */
    public function getFields($templateId): JsonResponse
    {
        $fields = RentalField::where('template_id', $templateId)
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $fields
        ]);
    }

    /**
     * Get field values for a rental
     */
    public function getValues($rentalId): JsonResponse
    {
        $values = DynamicRentalFields::getFieldValuesForRental($rentalId);

        return response()->json([
            'success' => true,
            'data' => $values
        ]);
    }

    /**
     * Save field values for a rental
     */
    public function saveValues(Request $request, $rentalId): JsonResponse
    {
        $request->validate([
            'values' => 'required|array',
            'values.*' => 'string'
        ]);

        try {
            DynamicRentalFields::saveFieldValues($rentalId, $request->values);

            return response()->json([
                'success' => true,
                'message' => 'Field values saved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save field values',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get filterable fields for a category
     */
    public function getFilterableFields($categoryId = null): JsonResponse
    {
        $fields = DynamicRentalFields::getFilterableFields($categoryId);

        return response()->json([
            'success' => true,
            'data' => $fields
        ]);
    }
}
