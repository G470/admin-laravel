<?php

namespace App\Livewire\Vendor\Reviews;

use App\Models\Review;
use App\Models\Rental;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewsList extends Component
{
    use WithPagination;

    // Public properties for form data
    public $search = '';
    public $statusFilter = 'all';
    public $ratingFilter = 'all';
    public $isVerifiedFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Pagination
    protected $paginationTheme = 'bootstrap';

    // Query string parameters
    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'ratingFilter' => ['except' => 'all'],
        'isVerifiedFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    /**
     * Mount component with initial data
     */
    public function mount()
    {
        // Component initialization if needed
    }

    /**
     * Update search results when filters change
     */
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedRatingFilter()
    {
        $this->resetPage();
    }

    public function updatedIsVerifiedFilter()
    {
        $this->resetPage();
    }

    /**
     * Sort by column
     */
    public function sortBy($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }

        $this->resetPage();
    }

    /**
     * Clear all filters
     */
    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->ratingFilter = 'all';
        $this->isVerifiedFilter = 'all';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    /**
     * Get reviews for the current vendor
     */
    public function getReviewsProperty()
    {
        $vendorId = Auth::id();

        $query = Review::with(['rental', 'user', 'replies'])
            ->whereHas('rental', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            });

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function($q) {
                $q->where('comment', 'LIKE', '%' . $this->search . '%')
                  ->orWhereHas('user', function($userQuery) {
                      $userQuery->where('first_name', 'LIKE', '%' . $this->search . '%')
                               ->orWhere('last_name', 'LIKE', '%' . $this->search . '%');
                  })
                  ->orWhereHas('rental', function($rentalQuery) {
                      $rentalQuery->where('title', 'LIKE', '%' . $this->search . '%');
                  });
            });
        }

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Apply rating filter
        if ($this->ratingFilter !== 'all') {
            $query->where('rating', $this->ratingFilter);
        }

        // Apply verified filter
        if ($this->isVerifiedFilter !== 'all') {
            $query->where('is_verified', $this->isVerifiedFilter === 'verified');
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(10);
    }

    /**
     * Get review statistics for the vendor
     */
    public function getStatsProperty()
    {
        $vendorId = Auth::id();

        return [
            'total' => Review::whereHas('rental', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->count(),

            'published' => Review::whereHas('rental', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->where('status', 'published')->count(),

            'pending' => Review::whereHas('rental', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->where('status', 'pending')->count(),

            'verified' => Review::whereHas('rental', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->where('is_verified', true)->count(),

            'average_rating' => Review::whereHas('rental', function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId);
            })->where('status', 'published')->avg('rating') ?? 0,
        ];
    }

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.vendor.reviews.reviews-list', [
            'reviews' => $this->reviews,
            'stats' => $this->stats,
        ]);
    }
}
