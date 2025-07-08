<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::where('role', '!=', 'admin')
            ->orderBy('name', 'asc')
            ->paginate(10);
            

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // 1. Validasi input (tanpa password)
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        'role' => ['required', 'string', 'in:admin,supervisor,student'],
    ]);

    // 2. Buat password acak 5 digit
    $randomPassword = mt_rand(10000, 99999);

    // 3. Buat user baru dengan password yang di-hash
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'role' => $request->role,
        'password' => Hash::make($randomPassword),
    ]);

    // 4. Redirect dengan pesan sukses yang menyertakan password sementara
    $successMessage = "User '{$user->name}' berhasil dibuat. Password sementara: {$randomPassword}";

    return redirect()->route('admin.users.index')->with('success', $successMessage);
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
public function update(Request $request, User $user)
{
    // 1. Validasi input
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', \Illuminate\Validation\Rule::unique('users')->ignore($user->id)],
        'role' => ['required', 'string', 'in:admin,supervisor,student'],
        'password' => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
    ]);

    // 2. Perbarui properti dari objek $user yang ada
    $user->name = $request->name;
    $user->email = $request->email;
    $user->role = $request->role;

    // 3. HANYA perbarui password jika diisi di form
    if (!empty($request->password)) {
        $user->password = Hash::make($request->password);
    }

    // 4. Simpan perubahan pada user yang ada
    $user->save();

    // 5. Kembali ke halaman daftar user
    return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui!');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
        {
        // Cek agar admin tidak bisa menghapus dirinya sendiri
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Hapus user
        $user->delete();

        // Redirect ke halaman daftar user dengan pesan sukses
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dihapus!');
    }
}
