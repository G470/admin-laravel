<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LoginBasic extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.authentications.auth-login-basic', ['pageConfigs' => $pageConfigs]);
  }

  /**
   * Get configuration data for authentication pages
   *
   * @return array
   */
  public function getConfig()
  {
    return [
      'myLayout' => 'blank',
      'navbarDetached' => true
    ];
  }
}
