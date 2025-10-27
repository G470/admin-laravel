<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Review;

class Reviews extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortBy = $field;
    }

    public function approveReview($reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $review->update(['status' => 'approved']);
        
        session()->flash('message', 'Bewertung erfolgreich genehmigt.');
    }

    public function rejectReview($reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $review->update(['status' => 'rejected']);
        
        session()->flash('message', 'Bewertung erfolgreich abgelehnt.');
    }

    public function deleteReview($reviewId)
    {
        $review = Review::findOrFail($reviewId);
        $review->delete();
        
        session()->flash('message', 'Bewertung erfolgreich gelöscht.');
    }

    public function render()
    {
        $reviews = Review::with(['user', 'rental'])
            ->when($this->search, function ($query) {
                $query->where('comment', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('rental', function ($q) {
                        $q->where('title', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        return view('livewire.admin.reviews', [
            'reviews' => $reviews
        ]);
    }
}
