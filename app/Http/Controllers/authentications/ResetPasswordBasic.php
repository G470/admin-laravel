<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResetPasswordBasic extends Controller
{
  public function index()
  {
    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.authentications.auth-reset-password-basic', ['pageConfigs' => $pageConfigs]);
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
