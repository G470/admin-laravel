<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class VendorEarningsController extends Controller
{
    /**
     * Display vendor earnings overview
     */
    public function index()
    {
        $vendors = User::where('is_vendor', true)
            ->with(['rentals', 'bills'])
            ->paginate(15);
        
        return view('content.admin.vendor-earnings.index', compact('vendors'));
    }

    /**
     * Show detailed earnings for a specific vendor
     */
    public function show($id)
    {
        $vendor = User::where('is_vendor', true)
            ->with(['rentals.bookings', 'bills'])
            ->findOrFail($id);
        
        return view('content.admin.vendor-earnings.show', compact('vendor'));
    }
}
