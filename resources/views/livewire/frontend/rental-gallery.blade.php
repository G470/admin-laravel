<div class="rental-gallery mb-4">
    @if($this->hasImages())
        <!-- Swiper Main Gallery -->
        <div class="swiper rental-swiper" wire:ignore>
            <div class="swiper-wrapper">
                @foreach($images as $image)
                    <div class="swiper-slide">
                        <div class="rental-image-container">
                            <img src="{{ $image['url'] }}" 
                                 alt="{{ $image['alt'] }}" 
                                 class="rental-image"
                                 loading="lazy">
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Navigation buttons -->
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
            
            <!-- Pagination -->
            <div class="swiper-pagination"></div>
        </div>

        <!-- Thumbnail Swiper (if more than 1 image) -->
        @if($showThumbnails)
            <div class="swiper rental-thumbnail-swiper mt-3" wire:ignore>
                <div class="swiper-wrapper">
                    @foreach($images as $image)
                        <div class="swiper-slide">
                            <div class="thumbnail-container">
                                <img src="{{ $image['url'] }}" 
                                     alt="{{ $image['alt'] }}" 
                                     class="thumbnail-image"
                                     loading="lazy">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @else
        <!-- Fallback when no images available -->
        <div class="rental-gallery-placeholder">
            <div class="placeholder-content">
                <i class="ti ti-package ti-xl mb-3 text-muted"></i>
                <h5 class="text-muted mb-2">Produktbild</h5>
                <p class="text-muted small mb-0">Bilder werden vom Vermieter zur Verfügung gestellt</p>
            </div>
        </div>
    @endif

<style>
    /* Rental Gallery Styles */
    .rental-gallery {
        border-radius: 12px;
        overflow: hidden;
        background: #f8f9fa;
    }
    
    .rental-swiper {
        width: 100%;
        height: 400px;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .rental-image-container {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
    }
    
    .rental-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 12px;
    }
    
    .rental-thumbnail-swiper {
        height: 80px;
        box-shadow: none;
        border-radius: 8px;
    }
    
    .rental-thumbnail-swiper .swiper-slide {
        width: 80px;
        height: 80px;
        opacity: 0.7;
        cursor: pointer;
        border-radius: 8px;
        overflow: hidden;
        transition: opacity 0.3s ease;
    }
    
    .rental-thumbnail-swiper .swiper-slide-thumb-active {
        opacity: 1;
        border: 2px solid var(--bs-primary);
    }
    
    .thumbnail-container {
        width: 100%;
        height: 100%;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .thumbnail-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .rental-gallery-placeholder {
        height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        border-radius: 12px;
        border: 2px dashed #dee2e6;
    }
    
    .placeholder-content {
        text-align: center;
        max-width: 300px;
        padding: 2rem;
    }
    
    /* Swiper Navigation Customization */
    .rental-swiper .swiper-button-next,
    .rental-swiper .swiper-button-prev {
        background: rgba(255, 255, 255, 0.9);
        width: 44px;
        height: 44px;
        border-radius: 50%;
        color: var(--bs-primary);
        margin-top: -22px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }
    
    .rental-swiper .swiper-button-next::after,
    .rental-swiper .swiper-button-prev::after {
        font-size: 18px;
        font-weight: bold;
    }
    
    .rental-swiper .swiper-button-next:hover,
    .rental-swiper .swiper-button-prev:hover {
        background: white;
        transform: scale(1.1);
    }
    
    .rental-swiper .swiper-pagination-bullet {
        background: rgba(255, 255, 255, 0.7);
        opacity: 1;
    }
    
    .rental-swiper .swiper-pagination-bullet-active {
        background: var(--bs-primary);
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .rental-swiper {
            height: 300px;
        }
        
        .rental-gallery-placeholder {
            height: 300px;
        }
        
        .rental-swiper .swiper-button-next,
        .rental-swiper .swiper-button-prev {
            width: 36px;
            height: 36px;
            margin-top: -18px;
        }
        
        .rental-swiper .swiper-button-next::after,
        .rental-swiper .swiper-button-prev::after {
            font-size: 14px;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeRentalGallery();
});

function initializeRentalGallery() {
    // Configuration from Livewire component
    const loopEnabled = {!! json_encode($loop) !!};
    const autoplayEnabled = {!! json_encode($autoplay) !!};
    
    // Initialize thumbnail swiper first
    const thumbnailSwiper = new Swiper('.rental-thumbnail-swiper', {
        spaceBetween: 10,
        slidesPerView: 'auto',
        freeMode: true,
        watchSlidesProgress: true,
        breakpoints: {
            320: {
                slidesPerView: 4,
                spaceBetween: 8
            },
            640: {
                slidesPerView: 6,
                spaceBetween: 10
            },
            768: {
                slidesPerView: 8,
                spaceBetween: 12
            },
            1024: {
                slidesPerView: 10,
                spaceBetween: 15
            }
        }
    });

    // Initialize main swiper
    const mainSwiper = new Swiper('.rental-swiper', {
        spaceBetween: 10,
        loop: loopEnabled,
        autoplay: autoplayEnabled ? {
            delay: 5000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true
        } : false,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
        thumbs: {
            swiper: thumbnailSwiper,
        },
        keyboard: {
            enabled: true,
            onlyInViewport: true
        },
        mousewheel: {
            forceToAxis: true
        },
        effect: 'slide',
        speed: 600,
        on: {
            slideChange: function() {
                // Optional: Add analytics or tracking here
                console.log('Slide changed to:', this.activeIndex);
            }
        }
    });

    // Add click handlers for thumbnails
    document.querySelectorAll('.rental-thumbnail-swiper .swiper-slide').forEach((thumb, index) => {
        thumb.addEventListener('click', () => {
            mainSwiper.slideTo(index);
        });
    });

    // Pause autoplay on hover
    const galleryContainer = document.querySelector('.rental-gallery');
    if (galleryContainer && autoplayEnabled) {
        galleryContainer.addEventListener('mouseenter', () => {
            mainSwiper.autoplay.stop();
        });
        
        galleryContainer.addEventListener('mouseleave', () => {
            mainSwiper.autoplay.start();
        });
    }

    // Handle fullscreen on image double-click
    document.querySelectorAll('.rental-image').forEach(img => {
        img.addEventListener('dblclick', function() {
            if (this.requestFullscreen) {
                this.requestFullscreen();
            } else if (this.webkitRequestFullscreen) {
                this.webkitRequestFullscreen();
            } else if (this.msRequestFullscreen) {
                this.msRequestFullscreen();
            }
        });
    });
}

// Reinitialize on Livewire updates
document.addEventListener('livewire:navigated', function() {
    setTimeout(() => {
        initializeRentalGallery();
    }, 100);
});
</script>
</div>
