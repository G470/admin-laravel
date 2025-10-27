<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    public function getFavorites(Request $request)
    {
        $ids = $request->input('ids', []);
        $rentals = Rental::with(['location', 'category'])->whereIn('id', $ids)->get();
        return response()->json($rentals);
    }
}
