<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RentalFieldTemplate;

class ExportRentalFieldTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rental-fields:export {file : Path to output JSON file} {--template= : Export specific template by name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export rental field templates to JSON file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filePath = $this->argument('file');
        $templateName = $this->option('template');

        try {
            $query = RentalFieldTemplate::select('*')->with(['fields', 'categories']);

            if ($templateName) {
                $query->where('name', $templateName);
            }

            $templates = $query->get();

            if ($templates->isEmpty()) {
                $this->error('No templates found');
                return 1;
            }

            $exportData = [
                'exported_at' => now()->toISOString(),
                'templates' => []
            ];

            foreach ($templates as $template) {
                $templateData = [
                    'name' => $template->name,
                    'description' => $template->description ?? '',
                    'is_active' => $template->is_active,
                    'sort_order' => $template->sort_order ?? 0,
                    'fields' => [],
                    'categories' => $template->categories->pluck('name')->toArray()
                ];

                foreach ($template->fields as $field) {
                    $templateData['fields'][] = [
                        'name' => $field->name,
                        'label' => $field->label,
                        'type' => $field->type,
                        'options' => $field->options,
                        'default_value' => $field->default_value,
                        'is_required' => $field->is_required,
                        'is_filterable' => $field->is_filterable,
                        'placeholder' => $field->placeholder,
                        'help_text' => $field->help_text,
                        'sort_order' => $field->sort_order,
                    ];
                }

                $exportData['templates'][] = $templateData;
            }

            $jsonContent = json_encode($exportData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            if (file_put_contents($filePath, $jsonContent) === false) {
                $this->error("Failed to write to file: {$filePath}");
                return 1;
            }

            $this->info("Export completed successfully!");
            $this->info("Exported {$templates->count()} template(s) to: {$filePath}");
            return 0;

        } catch (\Exception $e) {
            $this->error("Export failed: {$e->getMessage()}");
            return 1;
        }
    }
}
