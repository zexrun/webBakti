<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        if ($user->role === 'student') {
            $user->load('student');
        } elseif ($user->role === 'supervisor') {
            $user->load('supervisor');
        }

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {

        $request->validate([
            'name' => ['required', 'string', 'max:256'],
            'email' => ['required', 'string', 'email', 'max:256', Rule::unique('users')->ignore($request->user()->id)],
        ]);

        $user = $request->user();

        $user->name = $request->name;
        $user->email = $request->email;

        if (is_null($user->username) && $request->filled('username')) {
            $request->validate([
                'username' => ['required', 'string', 'alpha_dash', 'max:256', 'unique:users'],
            ]);
            $user->username = $request->username;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
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
