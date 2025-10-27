<?php

namespace App\Http\Controllers\layouts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NavbarFullSidebar extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'navbar-full-sidebar'];
    return view('content.layouts-example.layouts-navbar-full-sidebar', ['pageConfigs' => $pageConfigs]);
  }
}
