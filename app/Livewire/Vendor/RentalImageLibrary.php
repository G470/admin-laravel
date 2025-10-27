<?php

namespace App\Livewire\Vendor;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Rule;
use App\Models\Rental;
use App\Models\RentalImage;

class RentalImageLibrary extends Component
{
    use WithFileUploads;

    // Temporary files for upload
    #[Rule(['images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:10240'])] // 10MB max per image
    public array $images = [];

    // The rental being edited
    public ?Rental $rental = null;

    // Rental ID
    public ?int $rentalId = null;

    // Current images
    public $currentImages = [];

    public function mount(?int $rentalId = null): void
    {
        $this->rentalId = $rentalId;

        if ($rentalId) {
            // Load existing rental
            $this->rental = Rental::where('id', $rentalId)
                ->where('vendor_id', auth()->id())
                ->firstOrFail();

            // Load existing images
            $this->loadCurrentImages();
        }
    }

    public function loadCurrentImages(): void
    {
        if ($this->rental) {
            $this->currentImages = $this->rental->images()
                ->orderBy('order')
                ->get()
                ->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => Storage::url($image->path),
                        'order' => $image->order,
                        'path' => $image->path
                    ];
                })->toArray();
        }
    }

    public function uploadImages(): void
    {
        // Validate images
        $this->validate();

        if (!$this->rental) {
            session()->flash('error', 'Bitte speichern Sie zuerst das Vermietungsobjekt.');
            return;
        }

        if (empty($this->images)) {
            session()->flash('error', 'Bitte wählen Sie mindestens ein Bild aus.');
            return;
        }

        try {
            $uploadedCount = 0;
            $currentMaxOrder = $this->rental->images()->max('order') ?? 0;

            foreach ($this->images as $image) {
                // Store image
                $path = $image->store('rentals/' . $this->rental->id . '/images', 'public');

                // Create database entry
                $this->rental->images()->create([
                    'path' => $path,
                    'order' => $currentMaxOrder + $uploadedCount + 1
                ]);

                $uploadedCount++;
            }

            // Reset uploaded images
            $this->images = [];

            // Reload current images
            $this->loadCurrentImages();

            // Success message
            session()->flash('success', "$uploadedCount Bild(er) erfolgreich hochgeladen.");

            // Emit event
            $this->dispatch('imagesUpdated', [
                'message' => "$uploadedCount Bild(er) erfolgreich hochgeladen.",
                'count' => count($this->currentImages)
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Hochladen der Bilder: ' . $e->getMessage());
        }
    }

    public function removeImage($imageId): void
    {
        try {
            $image = RentalImage::where('id', $imageId)
                ->whereHas('rental', function ($query) {
                    $query->where('vendor_id', auth()->id());
                })
                ->firstOrFail();

            // Delete file from storage
            if ($image->path && Storage::disk('public')->exists($image->path)) {
                Storage::disk('public')->delete($image->path);
            }

            // Delete database record
            $image->delete();

            // Reload current images
            $this->loadCurrentImages();

            session()->flash('success', 'Bild erfolgreich gelöscht.');

            $this->dispatch('imagesUpdated', [
                'message' => 'Bild erfolgreich gelöscht.',
                'count' => count($this->currentImages)
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Löschen des Bildes: ' . $e->getMessage());
        }
    }

    public function cropImage($data): void
    {
        try {
            $imageId = $data['imageId'];
            $croppedData = $data['croppedData'];

            // Find the image
            $image = RentalImage::where('id', $imageId)
                ->whereHas('rental', function ($query) {
                    $query->where('vendor_id', auth()->id());
                })
                ->firstOrFail();

            // Store old path for cleanup
            $oldPath = $image->path;

            // Decode base64 image data
            $base64Data = explode(',', $croppedData)[1];
            $imageData = base64_decode($base64Data);

            if (!$imageData) {
                throw new \Exception('Ungültige Bilddaten');
            }

            // Generate new filename for cropped image
            $extension = pathinfo($oldPath, PATHINFO_EXTENSION) ?: 'jpg';
            $filename = 'rental-' . $this->rentalId . '-' . time() . '-cropped.' . $extension;
            $filePath = 'rentals/' . $this->rentalId . '/images/' . $filename;

            // Save cropped image
            if (!Storage::disk('public')->put($filePath, $imageData)) {
                throw new \Exception('Fehler beim Speichern des zugeschnittenen Bildes');
            }

            // Delete old image file if it exists
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            // Update database record with new cropped image path
            $image->update([
                'path' => $filePath,
                'updated_at' => now()
            ]);

            // Reload current images
            $this->loadCurrentImages();

            session()->flash('success', 'Bild erfolgreich zugeschnitten (785x440px).');

            $this->dispatch('imageCropped', [
                'message' => 'Bild erfolgreich zugeschnitten.',
                'count' => count($this->currentImages)
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Zuschneiden des Bildes: ' . $e->getMessage());
        }
    }

    public function updateImageOrder($orderedIds): void
    {
        try {
            if (!is_array($orderedIds) || empty($orderedIds)) {
                return;
            }

            foreach ($orderedIds as $index => $imageId) {
                RentalImage::where('id', $imageId)
                    ->whereHas('rental', function ($query) {
                        $query->where('vendor_id', auth()->id());
                    })
                    ->update(['order' => $index + 1]);
            }

            $this->loadCurrentImages();

            session()->flash('success', 'Bildreihenfolge erfolgreich aktualisiert.');

            $this->dispatch('imagesUpdated', [
                'message' => 'Bildreihenfolge aktualisiert.',
                'count' => count($this->currentImages)
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'Fehler beim Aktualisieren der Bildreihenfolge: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.vendor.rental-image-library');

    }
}
