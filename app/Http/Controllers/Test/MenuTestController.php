<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class MenuTestController extends Controller
{
    public function test()
    {
        $data = [
            'authenticated' => Auth::check(),
            'user' => Auth::user(),
            'user_email' => Auth::user()?->email,
            'is_admin' => Auth::user()?->is_admin,
            'is_vendor' => Auth::user()?->is_vendor,
            'roles' => Auth::user()?->roles?->pluck('name'),
        ];
        
        return view('test.menu-debug', compact('data'));
    }
    
    public function loginAs($type = 'vendor')
    {
        $users = [
            'admin' => 'admin@inlando.test',
            'vendor' => 'vendor@inlando.test', 
            'user' => 'customer@inlando.test'
        ];
        
        if (!isset($users[$type])) {
            return redirect()->route('test.menu-debug')->with('error', 'Invalid user type');
        }
        
        $user = User::where('email', $users[$type])->first();
        
        if (!$user) {
            return redirect()->route('test.menu-debug')->with('error', 'User not found');
        }
        
        Auth::login($user);
        
        return redirect()->route('test.menu-debug')->with('success', "Logged in as {$user->email}");
    }
    
    public function logout()
    {
        Auth::logout();
        return redirect()->route('test.menu-debug')->with('success', 'Logged out');
    }
}
