<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);
        
        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => $this->faker->sentence(),
            'parent_id' => null,
            'status' => $this->faker->randomElement(['online', 'offline']),
            'order' => $this->faker->numberBetween(1, 1000),
        ];
    }

    /**
     * Indicate that the category is online.
     */
    public function online(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'online',
        ]);
    }

    /**
     * Indicate that the category is a child category.
     */
    public function child($parentId): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
        ]);
    }

    /**
     * Create a main category with consistent ordering
     */
    public function mainCategory($order = null): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => null,
            'order' => $order ?? $this->faker->numberBetween(100, 900),
            'status' => 'online',
        ]);
    }

    /**
     * Create a subcategory with appropriate ordering
     */
    public function subcategory($parentId, $order = null): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
            'order' => $order ?? $this->faker->numberBetween(10, 90),
            'status' => 'online',
        ]);
    }

    /**
     * Create a sub-subcategory with appropriate ordering
     */
    public function subSubcategory($parentId, $order = null): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parentId,
            'order' => $order ?? $this->faker->numberBetween(10, 90),
            'status' => 'online',
        ]);
    }

    /**
     * Create a category with German rental-specific names
     */
    public function german(): static
    {
        $germanNames = [
            'Baumaschinen', 'Gartenwerkzeuge', 'Eventmöbel', 'Lichttechnik', 'Küchengeräte',
            'Transportmittel', 'Werkzeuge', 'Dekorationen', 'Sicherheitsausrüstung', 'Reinigungsgeräte',
            'Beleuchtung', 'Möbelvermietung', 'Geschirr', 'Textilien', 'Heizung & Kühlung',
            'Stromversorgung', 'Absperrungen', 'Bühnentechnik', 'Catering Equipment', 'Winterdienst'
        ];
        
        $name = $this->faker->randomElement($germanNames) . ' ' . $this->faker->numberBetween(1, 999);
        
        return $this->state(fn (array $attributes) => [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => 'Hochwertige ' . strtolower(explode(' ', $name)[0]) . ' für Ihre Veranstaltung oder Ihr Projekt.',
        ]);
    }

    /**
     * Create a category hierarchy with multiple levels
     */
    public static function createHierarchy(int $mainCategories = 3, int $subcategoriesPerMain = 3, int $subSubcategoriesPerSub = 2): array
    {
        $hierarchies = [];
        
        for ($i = 1; $i <= $mainCategories; $i++) {
            $mainCategory = Category::factory()
                ->mainCategory($i * 100)
                ->german()
                ->create();
            
            $hierarchies[$mainCategory->id] = ['main' => $mainCategory, 'subs' => []];
            
            for ($j = 1; $j <= $subcategoriesPerMain; $j++) {
                $subCategory = Category::factory()
                    ->subcategory($mainCategory->id, $j * 10)
                    ->german()
                    ->create();
                
                $hierarchies[$mainCategory->id]['subs'][$subCategory->id] = ['sub' => $subCategory, 'subSubs' => []];
                
                for ($k = 1; $k <= $subSubcategoriesPerSub; $k++) {
                    $subSubCategory = Category::factory()
                        ->subSubcategory($subCategory->id, $k * 10)
                        ->german()
                        ->create();
                    
                    $hierarchies[$mainCategory->id]['subs'][$subCategory->id]['subSubs'][] = $subSubCategory;
                }
            }
        }
        
        return $hierarchies;
    }
}
