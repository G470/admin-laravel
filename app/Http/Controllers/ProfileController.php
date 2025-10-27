<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        // Check if this is an admin route by looking at the route name
        $routeName = $request->route()->getName();
        $viewPath = $routeName === 'admin.profile.edit' ? 'content.admin.profile.edit' : 'profile.edit';

        return view($viewPath, [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        // Update basic fields
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Handle email verification
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        // Handle password update if provided
        if (!empty($validated['password'])) {
            $user->password = bcrypt($validated['password']);
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'Profil erfolgreich aktualisiert.');
    }

    public function destroy(Request $request)
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();
        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}