<?php

namespace App\Livewire\Frontend;

use Livewire\Component;
use App\Models\Category;

class Breadcrumb extends Component
{
    public $items = [];
    public $separator = '/';
    public $showHome = true;
    public $homeText = 'Home';
    public $homeUrl = '/';
    public $maxItems = 5;
    public $truncateText = '...';
    public $category = null;
    public $autoGenerateFromCategory = false;

    public function mount($items = [], $separator = '/', $showHome = true, $homeText = 'Home', $homeUrl = '/', $maxItems = 5, $category = null, $autoGenerateFromCategory = false)
    {
        $this->items = $items;
        $this->separator = $separator;
        $this->showHome = $showHome;
        $this->homeText = $homeText;
        $this->homeUrl = $homeUrl;
        $this->maxItems = $maxItems;
        $this->category = $category;
        $this->autoGenerateFromCategory = $autoGenerateFromCategory;

        // Automatisch Breadcrumb aus Kategorie generieren
        if ($this->autoGenerateFromCategory && $this->category) {
            $this->items = $this->generateBreadcrumbFromCategory($this->category);
        }
    }

    public function getProcessedItems()
    {
        $processedItems = [];

        // Home-Link hinzufügen
        if ($this->showHome) {
            $processedItems[] = [
                'text' => $this->homeText,
                'url' => $this->homeUrl,
                'active' => false,
                'icon' => 'ti ti-home',
                'color' => 'primary'
            ];
        }

        // Items verarbeiten
        foreach ($this->items as $index => $item) {
            $isLast = $index === count($this->items) - 1;

            $processedItems[] = [
                'text' => is_string($item) ? $item : ($item['text'] ?? ''),
                'url' => is_string($item) ? null : ($item['url'] ?? null),
                'active' => is_string($item) ? $isLast : ($item['active'] ?? $isLast),
                'icon' => is_string($item) ? null : ($item['icon'] ?? null),
                'color' => is_string($item) ? null : ($item['color'] ?? null)
            ];
        }

        // Items bei Bedarf kürzen
        if (count($processedItems) > $this->maxItems) {
            $processedItems = $this->truncateItems($processedItems);
        }

        return $processedItems;
    }

    private function truncateItems($items)
    {
        $totalItems = count($items);
        $visibleItems = $this->maxItems;

        // Immer Home und letztes Item anzeigen
        $homeItem = array_shift($items);
        $lastItem = array_pop($items);

        // Mittlere Items kürzen
        $middleItems = [];
        if (count($items) > 0) {
            $middleItems[] = [
                'text' => $this->truncateText,
                'url' => null,
                'active' => false,
                'icon' => null,
                'color' => 'secondary'
            ];
        }

        return array_merge([$homeItem], $middleItems, [$lastItem]);
    }

    /**
     * Generiert Breadcrumb-Items aus der Kategorie-Hierarchie
     */
    public function generateBreadcrumbFromCategory($category)
    {
        $breadcrumbItems = [];

        // Hierarchie von Root zu aktueller Kategorie aufbauen
        $path = $this->buildCategoryPath($category);

        foreach ($path as $index => $pathCategory) {
            $isLast = $index === count($path) - 1;

            $breadcrumbItems[] = [
                'text' => $pathCategory->name,
                'url' => $isLast ? null : $this->getCategoryUrl($pathCategory),
                'active' => $isLast,
                'icon' => $this->getCategoryIcon($pathCategory),
                'color' => $isLast ? 'primary' : null
            ];
        }

        return $breadcrumbItems;
    }

    /**
     * Baut den Pfad von Root zur aktuellen Kategorie auf
     */
    private function buildCategoryPath($category)
    {
        $path = [];
        $current = $category;

        // Von der aktuellen Kategorie zur Root-Kategorie gehen
        while ($current) {
            array_unshift($path, $current);
            $current = $current->parent;
        }

        return $path;
    }

    /**
     * Generiert die URL für eine Kategorie
     */
    private function getCategoryUrl($category)
    {
        // Wenn die Kategorie einen Slug hat, verwenden wir die category.show Route
        if ($category->slug) {
            return route('category.show', $category->slug);
        }

        // Fallback: Wenn es eine Root-Kategorie ist (parent_id = null)
        if (!$category->parent_id) {
            return route('categories.type', $category->type ?? 'all');
        }

        // Für Unterkategorien ohne Slug verwenden wir die Type-Route
        return route('categories.type', $category->type ?? 'all');
    }

    /**
     * Bestimmt das passende Icon für eine Kategorie
     */
    private function getCategoryIcon($category)
    {
        // Standard-Icon für Kategorien
        $defaultIcon = 'ti ti-category';

        // Spezielle Icons basierend auf Kategorie-Name oder Type
        $iconMap = [
            'events' => 'ti ti-calendar-event',
            'fahrzeuge' => 'ti ti-car',
            'baumaschinen' => 'ti ti-truck',
            'garten' => 'ti ti-plant',
            'elektronik' => 'ti ti-device-laptop',
            'sport' => 'ti ti-bike',
            'hochzeiten' => 'ti ti-heart',
            'geburtstage' => 'ti ti-cake',
            'firmenfeiern' => 'ti ti-building',
            'toiletten' => 'ti ti-toilet-paper',
            'duschcontainer' => 'ti ti-droplet',
            'zelte' => 'ti ti-tent',
            'tische' => 'ti ti-table',
            'stühle' => 'ti ti-chair',
        ];

        $categoryName = strtolower($category->name);
        $categoryType = strtolower($category->type ?? '');

        // Prüfe zuerst den Kategorie-Namen
        foreach ($iconMap as $key => $icon) {
            if (str_contains($categoryName, $key)) {
                return $icon;
            }
        }

        // Prüfe dann den Kategorie-Type
        foreach ($iconMap as $key => $icon) {
            if (str_contains($categoryType, $key)) {
                return $icon;
            }
        }

        return $defaultIcon;
    }

    public function render()
    {
        return view('livewire.frontend.breadcrumb', [
            'breadcrumbItems' => $this->getProcessedItems()
        ]);
    }
}