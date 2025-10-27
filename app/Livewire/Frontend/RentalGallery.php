<?php

namespace App\Livewire\Frontend;

use App\Models\Rental;
use Livewire\Component;

class RentalGallery extends Component
{
    public $rental;
    public $images = [];
    public $showThumbnails = true;
    public $autoplay = true;
    public $loop = true;
    
    public function mount(Rental $rental)
    {
        $this->rental = $rental;
        $this->loadImages();
    }
    
    /**
     * Load rental images from the rental-image-library
     */
    private function loadImages()
    {
        // Get images from the rental_images table (rental-image-library)
        $rentalImages = $this->rental->images()->orderBy('order')->get();
        
        if ($rentalImages->count() > 0) {
            // Use images from the rental-image-library
            $this->images = $rentalImages->map(function ($image) {
                return [
                    'url' => asset('storage/' . $image->path),
                    'alt' => $this->rental->title,
                    'order' => $image->order
                ];
            })->toArray();
        } else {
            // Fallback: Check if rental has images array (legacy)
            if (isset($this->rental->images) && is_array($this->rental->images) && count($this->rental->images) > 0) {
                $this->images = collect($this->rental->images)->map(function ($imagePath, $index) {
                    return [
                        'url' => $imagePath,
                        'alt' => $this->rental->title,
                        'order' => $index
                    ];
                })->toArray();
            } else {
                // No images available
                $this->images = [];
            }
        }
        
        // Determine if thumbnails should be shown
        $this->showThumbnails = count($this->images) > 1;
    }
    
    /**
     * Get the primary image for the rental
     */
    public function getPrimaryImageProperty()
    {
        return $this->images[0] ?? null;
    }
    
    /**
     * Check if the rental has images
     */
    public function hasImages()
    {
        return count($this->images) > 0;
    }
    
    public function render()
    {
        return view('livewire.frontend.rental-gallery');
    }
}
