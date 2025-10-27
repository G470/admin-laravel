<?php

namespace App\Http\Controllers\Inlando;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FavoritesController extends Controller
{
    public function index()
    {
        return view('inlando.favorites');
    }
}
