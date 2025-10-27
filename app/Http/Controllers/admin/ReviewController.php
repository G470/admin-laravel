<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews using Livewire component
     */
    public function index()
    {
        return view('content.admin.reviews.index');
    }

    /**
     * Show the form for editing the specified review
     */
    public function edit($id)
    {
        $review = Review::with(['user', 'rental'])->findOrFail($id);
        return view('content.admin.reviews.edit', compact('review'));
    }

    /**
     * Update the specified review
     */
    public function update(Request $request, $id)
    {
        $review = Review::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'admin_note' => 'nullable|string|max:500',
        ]);

        $review->update($validated);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Bewertung erfolgreich aktualisiert.');
    }

    /**
     * Remove the specified review
     */
    public function destroy($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Bewertung erfolgreich gelöscht.');
    }
}
