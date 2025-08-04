<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Directorate;
use App\Models\Position;
use App\Models\Supervisor;
use App\Notifications\SendAccountActivationEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $queryBuilder = fn($role, $relasi = null) =>
        User::with($relasi ? [$relasi] : [])
            ->where('role', $role)
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->orderBy('created_at', 'asc')
            ->paginate(10, ['*'], $role);

        $admins = $queryBuilder('admin');
        $supervisors = $queryBuilder('supervisor', 'supervisor');
        $students = $queryBuilder('student', 'student');

        return view('admin.users.index', compact('admins', 'supervisors', 'students', 'search'));
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
        } elseif ($request->role === 'supervisor') {
            $supervisor = new Supervisor();
            $supervisor->user_id = $user->id;
            $supervisor->nip = 'NIP-' . $user->id;
            $supervisor->jabatan = 'Supervisor';
            $supervisor->save();
        }

        $user->notify(new SendAccountActivationEmail($token));

        return redirect()->route('admin.users.index')->with('success', 'Undangan aktivasi berhasil dikirim ke ' . $user->email);
    }

    public function show(string $id) {}

    public function edit(User $user)
    {
        $directorates = Directorate::all();
        $positions = Position::all();
        return view('admin.users.edit', compact('user', 'directorates', 'positions'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(['admin', 'supervisor', 'student'])],
            'direktorat' => [
                Rule::requiredIf(in_array($request->role, ['supervisor', 'student'])),
                'exists:directorates,id'
            ],
            'jabatan' => [
                Rule::requiredIf($request->role === 'supervisor'),
                'nullable',
                'exists:positions,id'
            ],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);


        $user->fill($request->only(['name', 'email', 'role']));

        // Update student
        if ($user->role === 'student' && $user->student) {
            $user->student->fill($request->only([
                'nim',
                'universitas',
                'program_studi',
                'semester'
            ]));

            if ($request->filled('direktorat')) {
                $directorateName = Directorate::find($request->input('direktorat'))?->name;
                $user->student->direktorat = $directorateName;
            }

            $user->student->save();
        }

        // Update supervisor
        elseif ($user->role === 'supervisor' && $user->supervisor) {
            $user->supervisor->fill($request->only(['nip']));

            if ($request->filled('direktorat')) {
                $directorateName = Directorate::find($request->input('direktorat'))?->name;
                $user->supervisor->direktorat = $directorateName;
            }

            if ($request->filled('jabatan')) {
                $positionName = Position::find($request->input('jabatan'))?->name;
                $user->supervisor->jabatan = $positionName;
            }

            $user->supervisor->save();
        }

        // Update password jika diisi
        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui!');
    }


    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('danger', "User {$user->name} berhasil dihapus!");
    }

    public function resendActivation(User $user)
    {
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
