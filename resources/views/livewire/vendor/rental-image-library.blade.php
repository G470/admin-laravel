<div>
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ti ti-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ti ti-x me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <h5 class="card-header">
            <i class="ti ti-photo me-2"></i>Bildverwaltung
            @if(count($currentImages) > 0)
                <span class="badge bg-primary ms-2">{{ count($currentImages) }} Bild(er)</span>
            @endif
        </h5>
        <div class="card-body">
            <!-- Upload Section -->
            <div class="mb-4">
                <label for="imageUpload" class="form-label">
                    <i class="ti ti-upload me-1"></i>Neue Bilder hochladen
                </label>
                <input type="file" class="form-control @error('images.*') is-invalid @enderror" id="imageUpload"
                    wire:model="images" multiple accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                <small class="form-text text-muted">
                    Maximale Dateigröße: 10MB pro Bild. Unterstützte Formate: JPG, PNG, GIF, WebP. Optimal: 785x440px
                </small>
                @error('images.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Upload Button -->
            @if(count($images) > 0)
                <div class="mb-4">
                    <button type="button" wire:click="uploadImages" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>
                        {{ count($images) }} Bild(er) hochladen
                    </button>
                </div>
            @endif

            <!-- Current Images -->
            @if(count($currentImages) > 0)
                <hr>
                <h6 class="mb-3">
                    <i class="ti ti-photo me-1"></i>Vorhandene Bilder ({{ count($currentImages) }})
                    <small class="text-muted ms-2">
                        <i class="ti ti-hand-move"></i>Per Drag & Drop sortieren
                        <i class="ti ti-crop ms-2"></i>Auf 785x440px zuschneiden
                    </small>
                </h6>
                <div class="row" id="sortableImages">
                    @foreach($currentImages as $image)
                        <div class="col-md-3 col-sm-4 col-6 mb-3 sortable-item" wire:key="image-{{ $image['id'] }}"
                            data-id="{{ $image['id'] }}">
                            <div class="card image-card">
                                <div class="position-relative">
                                    <img src="{{ $image['url'] }}" class="card-img-top"
                                        style="height: 200px; object-fit: cover; cursor: grab;" alt="Rental Image"
                                        draggable="false">

                                    <!-- Drag Handle -->
                                    <div class="position-absolute top-0 start-0 p-2">
                                        <div class="drag-handle" title="Ziehen um die Reihenfolge zu ändern">
                                            <i class="ti ti-grip-vertical text-white bg-dark rounded px-1"></i>
                                        </div>
                                    </div>

                                    <!-- Image Controls -->
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <div class="btn-group-vertical">
                                            <button type="button" class="btn btn-sm btn-primary btn-icon mb-1"
                                                onclick="openCropModal('{{ $image['url'] }}', {{ $image['id'] }})"
                                                title="Bild zuschneiden (785x440px)">
                                                <i class="ti ti-crop"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger btn-icon"
                                                wire:click="removeImage({{ $image['id'] }})"
                                                wire:confirm="Möchten Sie dieses Bild wirklich löschen?" title="Bild löschen">
                                                <i class="ti ti-trash"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Order Badge -->
                                    <div class="position-absolute bottom-0 start-0 p-2">
                                        <span class="badge bg-primary order-badge">{{ $image['order'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-3">
                    <small class="text-muted">
                        <i class="ti ti-info-circle me-1"></i>
                        <strong>Funktionen:</strong> Drag & Drop zum Sortieren | <i class="ti ti-crop me-1"></i> Zuschneiden
                        auf 785x440px | Das erste Bild wird als Hauptbild verwendet.
                    </small>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="ti ti-info-circle me-2"></i>
                    <strong>Noch keine Bilder vorhanden.</strong>
                    <br>Laden Sie Bilder hoch, um Ihr Vermietungsobjekt attraktiv zu präsentieren.
                </div>
            @endif
        </div>
    </div>

    <!-- Crop Modal -->
    <div class="modal fade" id="cropModal" tabindex="-1" aria-labelledby="cropModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cropModalLabel">
                        <i class="ti ti-crop me-2"></i>Bild zuschneiden (785x440px)
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <small class="text-muted">Ziehen Sie den Auswahlrahmen, um den gewünschten Bereich zu
                            wählen</small>
                    </div>
                    <div style="max-height: 400px; overflow: hidden;">
                        <img id="cropImage" style="max-width: 100%;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i>Abbrechen
                    </button>
                    <button type="button" class="btn btn-primary" id="cropAndSave">
                        <i class="ti ti-device-floppy me-1"></i>Zuschneiden & Speichern
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Include SortableJS and Cropper.js from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.css">

    <style>
        /* Drag & Drop Styles */
        .sortable-item {
            transition: transform 0.2s ease;
        }

        .sortable-item.sortable-chosen {
            transform: scale(1.05);
            opacity: 0.9;
            z-index: 1000;
        }

        .sortable-item.sortable-ghost {
            opacity: 0.3;
        }

        .image-card {
            cursor: grab;
            transition: box-shadow 0.2s ease;
        }

        .image-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .image-card:active {
            cursor: grabbing;
        }

        .drag-handle {
            cursor: grab;
            opacity: 0.8;
            transition: all 0.2s ease;
            border-radius: 0.25rem;
            padding: 0.25rem;
        }

        .drag-handle:hover {
            opacity: 1;
            background-color: rgba(0, 0, 0, 0.1);
            transform: scale(1.1);
        }

        .drag-handle:active {
            cursor: grabbing;
        }

        .sortable-item:hover .drag-handle {
            opacity: 1;
        }

        .order-badge {
            font-weight: bold;
            font-size: 0.8em;
        }

        /* Sortable placeholder */
        .sortable-placeholder {
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 0.375rem;
            opacity: 0.5;
        }

        /* Image controls styling */
        .btn-group-vertical .btn {
            margin-bottom: 0;
        }

        .btn-group-vertical .btn+.btn {
            margin-top: 2px;
        }
    </style>

    <script>
        let sortableInstance = null;
        let cropperInstance = null;
        let currentImageId = null;

        // Listen for success events from Livewire
        document.addEventListener('livewire:initialized', function () {
            // Initialize sortable after Livewire is ready
            initializeSortable();

            // Listen for custom image order change events
            document.addEventListener('image-order-changed', function (event) {
                @this.call('updateImageOrder', event.detail.orderedIds);
            });

            Livewire.on('imagesUpdated', (event) => {
                if (event && event.message) {
                    // Reinitialize sortable after images update
                    setTimeout(() => {
                        initializeSortable();
                    }, 100);
                }
            });

            // Listen for crop events
            Livewire.on('imageCropped', (event) => {
                if (event && event.message) {
                    // Close crop modal
                    const cropModal = bootstrap.Modal.getInstance(document.getElementById('cropModal'));
                    if (cropModal) {
                        cropModal.hide();
                    }

                    // Reinitialize sortable after crop
                    setTimeout(() => {
                        initializeSortable();
                    }, 100);
                }
            });
        });

        // Crop Modal Functions
        function openCropModal(imageUrl, imageId) {
            currentImageId = imageId;
            const cropImage = document.getElementById('cropImage');
            cropImage.src = imageUrl;

            // Show modal
            const cropModal = new bootstrap.Modal(document.getElementById('cropModal'));
            cropModal.show();

            // Initialize cropper after modal is shown
            document.getElementById('cropModal').addEventListener('shown.bs.modal', function () {
                initializeCropper();
            }, { once: true });
        }

        function initializeCropper() {
            const cropImage = document.getElementById('cropImage');

            // Destroy existing cropper if exists
            if (cropperInstance) {
                cropperInstance.destroy();
            }

            // Initialize new cropper with 785:440 aspect ratio
            cropperInstance = new Cropper(cropImage, {
                aspectRatio: 785 / 440, // Fixed aspect ratio
                viewMode: 1,
                minContainerWidth: 300,
                minContainerHeight: 200,
                background: false,
                autoCropArea: 0.8,
                movable: true,
                scalable: true,
                rotatable: false,
                zoomable: true,
                cropBoxMovable: true,
                cropBoxResizable: true,
                guides: true,
                center: true,
                highlight: true,
                responsive: true,
                restore: true,
                checkOrientation: true
            });
        }

        // Crop and save functionality
        document.getElementById('cropAndSave').addEventListener('click', function () {
            if (!cropperInstance || !currentImageId) {
                return;
            }

            // Get cropped canvas
            const canvas = cropperInstance.getCroppedCanvas({
                width: 785,
                height: 440,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high'
            });

            // Convert to blob and upload
            canvas.toBlob((blob) => {
                if (blob) {
                    // Create form data
                    const formData = new FormData();
                    formData.append('croppedImage', blob, 'cropped-image.jpg');
                    formData.append('imageId', currentImageId);

                    // Call Livewire method
                    @this.call('cropImage', {
                        imageId: currentImageId,
                        croppedData: canvas.toDataURL('image/jpeg', 0.9)
                    });
                }
            }, 'image/jpeg', 0.9);
            // close modal
            const cropModal = bootstrap.Modal.getInstance(document.getElementById('cropModal'));
            if (cropModal) {
                cropModal.hide();
            }
        });

        // Clean up cropper when modal is hidden
        document.getElementById('cropModal').addEventListener('hidden.bs.modal', function () {
            if (cropperInstance) {
                cropperInstance.destroy();
                cropperInstance = null;
            }
            currentImageId = null;
        });

        function initializeSortable() {
            const sortableContainer = document.getElementById('sortableImages');

            if (!sortableContainer) {
                return;
            }

            // Destroy existing instance if it exists
            if (sortableInstance) {
                sortableInstance.destroy();
            }

            // Initialize SortableJS
            sortableInstance = new Sortable(sortableContainer, {
                animation: 200,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                dragClass: 'sortable-drag',
                handle: '.drag-handle', // Only allow dragging via the drag handle
                onEnd: function (evt) {
                    // Get the new order of image IDs
                    const orderedIds = Array.from(sortableContainer.children).map(item => {
                        return parseInt(item.dataset.id);
                    });

                    console.log('New order:', orderedIds);

                    // Send the new order to Livewire
                    // Dispatch custom event to trigger Livewire method
                    document.dispatchEvent(new CustomEvent('image-order-changed', {
                        detail: { orderedIds: orderedIds }
                    }));

                    // Update order badges immediately for better UX
                    updateOrderBadges();
                },
                onStart: function (evt) {
                    // Add visual feedback when dragging starts
                    document.body.style.cursor = 'grabbing';
                },
                onMove: function (evt) {
                    // Additional move logic if needed
                    return true;
                },
                onSort: function (evt) {
                    // Update order badges during sort
                    updateOrderBadges();
                }
            });
        }

        function updateOrderBadges() {
            const sortableContainer = document.getElementById('sortableImages');
            if (sortableContainer) {
                const items = sortableContainer.children;
                Array.from(items).forEach((item, index) => {
                    const badge = item.querySelector('.order-badge');
                    if (badge) {
                        badge.textContent = index + 1;
                    }
                });
            }
        }

        // Loading state for file input
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('imageUpload');
            if (fileInput) {
                fileInput.addEventListener('change', function () {
                    if (this.files.length > 0) {
                        console.log(`${this.files.length} Datei(en) ausgewählt`);
                    }
                });
            }

            // Initialize sortable on page load
            setTimeout(() => {
                initializeSortable();
            }, 100);
        });

        // Reset cursor when drag ends
        document.addEventListener('mouseup', function () {
            document.body.style.cursor = '';
        });
    </script>
</div>