<?php

namespace App\Http\Controllers\vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Rental;
use Illuminate\Support\Facades\Auth;

class ReviewsController extends Controller
{
    public function index()
    {
        // Get vendor ID (assuming vendor ID is stored in the user's ID when they have a vendor role)
        $vendorId = Auth::id();

        // Get statistics for the vendor's reviews
        $stats = [
            'average_rating' => Review::whereHas('rental', function($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })->where('status', 'published')->avg('rating') ?? 0,

            'total_reviews' => Review::whereHas('rental', function($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })->count(),

            'pending_reviews' => Review::whereHas('rental', function($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })->where('status', 'pending')->count(),

            'verified_reviews' => Review::whereHas('rental', function($query) use ($vendorId) {
                $query->where('vendor_id', $vendorId);
            })->where('is_verified', true)->count()
        ];

        return view('content.vendor.reviews.index', [
            'stats' => $stats,
            'pageConfigs' => ['pageHeader' => false]
        ]);
    }

    public function show($id)
    {
        $review = Review::with(['rental', 'user', 'replies.user'])->findOrFail($id);

        // Check if the review belongs to one of the vendor's rentals
        $vendorId = Auth::id();
        if ($review->rental->vendor_id != $vendorId) {
            return abort(403);
        }

        return view('content.vendor.reviews.show', [
            'review' => $review,
            'pageConfigs' => ['pageHeader' => false]
        ]);
    }
}
