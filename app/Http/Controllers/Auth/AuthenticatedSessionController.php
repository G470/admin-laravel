<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Configuration for Vuexy layout
     *
     * @return array
     */
    private function getPageConfigs()
    {
        return [
            'bodyClass' => 'authentication-bg position-relative',
            'navbarType' => 'hidden',
            'footerFixed' => false,
            'pageHeader' => false,
            'defaultLayout' => 'auth'
        ];
    }

    public function create()
    {
        $pageConfigs = $this->getPageConfigs();
        return view('auth.login')->with('pageConfigs', $pageConfigs);
    }

    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();
        
        $user = Auth::user();
        
        // Check if user has 2FA enabled
        if ($user->hasTwoFactorEnabled()) {
            // Clear 2FA verification from session
            session()->forget('2fa_verified');
            
            return redirect()->route('two-factor.verify');
        }
        
        // Redirect based on user role - checking both role-based and attribute-based permissions
        if ($user->isAdmin() || $user->hasRole('admin')) {
            return redirect()->intended(route('admin.dashboard'));
        } elseif ($user->isVendor() || $user->hasRole('vendor')) {
            return redirect()->intended(route('vendor-dashboard'));
        } else {
            // Regular user/customer
            return redirect()->intended(route('user.dashboard'));
        }
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();
        
        // Clear 2FA verification session
        $request->session()->forget('2fa_verified');
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect(route('home'));
    }
}