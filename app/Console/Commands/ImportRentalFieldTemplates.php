<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RentalFieldTemplate;
use App\Models\RentalField;
use App\Models\AdminCategory;

class ImportRentalFieldTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rental-fields:import {file : Path to JSON file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import rental field templates from JSON file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');

        if (!file_exists($filePath)) {
            $this->error("File not found: {$filePath}");
            return 1;
        }

        try {
            $data = json_decode(file_get_contents($filePath), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->error('Invalid JSON file');
                return 1;
            }

            $this->info('Starting import...');

            foreach ($data['templates'] ?? [] as $templateData) {
                $this->importTemplate($templateData);
            }

            $this->info('Import completed successfully!');
            return 0;

        } catch (\Exception $e) {
            $this->error("Import failed: {$e->getMessage()}");
            return 1;
        }
    }

    private function importTemplate($templateData)
    {
        $this->info("Importing template: {$templateData['name']}");

        // Create or update template
        $template = RentalFieldTemplate::updateOrCreate(
            ['name' => $templateData['name']],
            [
                'description' => $templateData['description'] ?? '',
                'is_active' => $templateData['is_active'] ?? true,
                'sort_order' => $templateData['sort_order'] ?? 0,
            ]
        );

        // Import fields
        foreach ($templateData['fields'] ?? [] as $fieldData) {
            $field = RentalField::updateOrCreate(
                [
                    'template_id' => $template->id,
                    'field_name' => $fieldData['name']
                ],
                [
                    'field_label' => $fieldData['label'],
                    'field_type' => $fieldData['type'],
                    'options' => $fieldData['options'] ?? null,
                    'is_required' => $fieldData['is_required'] ?? false,
                    'is_filterable' => $fieldData['is_filterable'] ?? false,
                    'field_description' => $fieldData['help_text'] ?? null,
                    'sort_order' => $fieldData['sort_order'] ?? 0,
                ]
            );
        }

        // Assign categories
        if (isset($templateData['categories'])) {
            $categoryIds = [];
            foreach ($templateData['categories'] as $categoryName) {
                $category = AdminCategory::where('name', $categoryName)->first();
                if ($category) {
                    $categoryIds[] = $category->id;
                }
            }
            $template->categories()->sync($categoryIds);
        }

        $this->info("✓ Template '{$templateData['name']}' imported successfully");
    }
}
