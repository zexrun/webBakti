<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Supervisor;
use App\Notifications\SendAccountActivationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;


class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('role', 'asc')
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'role' => ['required', 'string', 'in:admin,supervisor,student'],
    ]);

    $token = Str::random(60);

    $user = User::create([
        'name' => 'Pengguna Baru',
        'email' => $request->email,
        'role' => $request->role,
        'activation_token' => hash('sha256', $token),
        'password' => null,
    ]);

    if ($request->role === 'student') {
        Student::create([
            'user_id' => $user->id,
        ]);
    }elseif ($request->role === 'supervisor') {
        $supervisor = new Supervisor();
        $supervisor->user_id = $user->id;
        $supervisor->nip = 'NIP-' . $user->id;
        $supervisor->jabatan = 'Supervisor';     
        $supervisor->save(); 
    }

    $user->notify(new SendAccountActivationEmail($token));

    return redirect()->route('admin.users.index')->with('success', 'Undangan aktivasi berhasil dikirim ke ' . $user->email);
}

    public function show(string $id)
    {
        
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', 'in:admin,supervisor,student'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(User $user)
        {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('danger', "User {$user->name} berhasil dihapus!");
    }

    public function resendActivation(User $user) {
        if ($user->email_verified_at) {
            return redirect()->route('admin.users.index')->with('error', 'User ini sudah aktif');
        }

        $token = Str::random(60);
        $user->activation_token = hash('sha256', $token);
        $user->save();

        $user->notify(new SendAccountActivationEmail($token));

        return redirect()->route('admin.users.index')->with('success', 'Link aktivasi berhasil dikirim ke ' . $user->email);
    }
}
