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
use Illuminate\Support\Facades\Hash;

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
        // Validasi dasar
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($request->user()->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        // Validasi username hanya jika user belum punya username
        if (is_null($request->user()->username)) {
            $rules['username'] = ['required', 'string', 'alpha_dash', 'max:255', 'unique:users'];
        }

        // Validasi tambahan berdasarkan role
        if ($request->user()->role === 'supervisor') {
            $rules['nip'] = ['nullable', 'string', 'max:255'];
        }

        $validated = $request->validate($rules);

        $user = $request->user();

        // Update data dasar user
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Update username jika belum ada dan diisi
        if (is_null($user->username) && !empty($validated['username'])) {
            $user->username = $validated['username'];
        }

        // Update password jika diisi
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // Update data tambahan sesuai role
        if ($user->role === 'student' && $user->student) {
            // Update data mahasiswa jika ada field tambahan
            if ($request->has('nim')) {
                $user->student->nim = $request->input('nim');
            }
            if ($request->has('universitas')) {
                $user->student->universitas = $request->input('universitas');
            }
            if ($request->has('direktorat')) {
                $directorate = Directorate::find($request->input('direktorat'));
                if ($directorate) {
                    $user->student->direktorat = $directorate->name;
                }
            }
            $user->student->save();
        } elseif ($user->role === 'supervisor' && $user->supervisor) {
            // Update data supervisor
            if (!empty($validated['nip'])) {
                $user->supervisor->nip = $validated['nip'];
            }

            // Uncomment jika diperlukan
            /*
            if ($request->has('jabatan')) {
                $position = Position::find($request->input('jabatan'));
                if ($position) {
                    $user->supervisor->jabatan = $position->name;
                }
            }
            
            if ($request->has('direktorat')) {
                $directorate = Directorate::find($request->input('direktorat'));
                if ($directorate) {
                    $user->supervisor->direktorat = $directorate->name;
                }
            }
            */

            $user->supervisor->save();
        }

        return Redirect::route('profile.show')->with('success', 'Profil berhasil diperbarui!');
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
