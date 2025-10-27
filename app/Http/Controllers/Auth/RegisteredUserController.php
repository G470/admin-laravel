<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
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
        return view('auth.register')->with('pageConfigs', $pageConfigs);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign default role
        $user->assignRole('user');

        event(new Registered($user));

        Auth::login($user);

        // Redirect new users to their appropriate dashboard
        if ($user->isAdmin() || $user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isVendor() || $user->hasRole('vendor')) {
            return redirect()->route('vendor-dashboard');
        } else {
            // Regular user/customer
            return redirect()->route('user.dashboard');
        }
    }
}