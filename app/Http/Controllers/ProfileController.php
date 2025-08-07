<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Directorate;
use App\Models\Position;
use App\Models\AbsenUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil pengguna.
     */
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        return view('profile.show', compact('user'));
    }

    /**
     * Tampilkan halaman edit profil pengguna.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // Load relasi berdasarkan role user
        if ($user->role === 'student') {
            $user->load('student');
        } elseif ($user->role === 'supervisor') {
            $user->load('supervisor');
        }

        $directorates = Directorate::orderBy('name')->get();
        $positions = Position::orderBy('name')->get();

        return view('profile.edit', compact('user', 'directorates', 'positions'));
    }

    /**
     * Update informasi profil pengguna.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:256'],
            'email' => ['required', 'string', 'email', 'max:256', Rule::unique('users')->ignore($request->user()->id)],
            'direktorat' => ['nullable', 'exists:directorates,id'],
            'jabatan' => ['nullable', 'exists:positions,id'],
        ]);

        $user = $request->user();
        $user->name = $request->name;
        $user->email = $request->email;

        // Buat username jika belum ada
        if (is_null($user->username) && $request->filled('username')) {
            $request->validate([
                'username' => ['required', 'string', 'alpha_dash', 'max:256', 'unique:users'],
            ]);
            $user->username = $request->username;
        }

        $user->save();

        if (!is_null($user->username)) {
        $absenUser = AbsenUser::where('username', $user->username)->first();

        if ($absenUser) {
            $absenUser->update([
                'name' => $user->name,
                'email' => $user->email,
            ]);
        }
    }

        // Update data tambahan sesuai role
        if ($user->role === 'student' && $user->student) {
            $user->student->nim = $request->input('nim');
            $user->student->universitas = $request->input('universitas');

            $directorate = Directorate::find($request->input('direktorat'));
            if ($directorate) {
                $user->student->direktorat = $directorate->name;
            }

            $user->student->save();

        } elseif ($user->role === 'supervisor' && $user->supervisor) {
            $user->supervisor->nip = $request->input('nip');
/* 
            $position = Position::find($request->input('jabatan'));
            if ($position) {
                $user->supervisor->jabatan = $position->name;
            }

            $directorate = Directorate::find($request->input('direktorat'));
            if ($directorate) {
                $user->supervisor->direktorat = $directorate->name;
            } */

            $user->supervisor->save();
        }

        return Redirect::route('profile.show')->with('status', 'profile-updated');
    }

    /**
     * Hapus akun pengguna.
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
