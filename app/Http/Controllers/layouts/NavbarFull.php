<?php

namespace App\Http\Controllers\layouts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NavbarFull extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'navbar-full'];
    return view('content.layouts-example.layouts-navbar-full', ['pageConfigs' => $pageConfigs]);
  }
}
