<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsenUser;
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
            'password' => null,
            'activation_token' => hash('sha256', $token),
        ]);

        if ($request->role === 'student') {
            Student::create([
                'user_id' => $user->id,
            ]);
        } elseif ($request->role === 'supervisor') {
            Supervisor::create([
                'user_id' => $user->id,
                'nip' => null,
                'jabatan' => null,
            ]);
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
        // Validasi dasar untuk semua role
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', 'string', Rule::in(['admin', 'supervisor', 'student'])],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];

        // Validasi tambahan berdasarkan role
        if (in_array($request->role, ['supervisor', 'student'])) {
            $rules['direktorat'] = ['required', 'exists:directorates,id'];
        }

        if ($request->role === 'supervisor') {
            $rules['jabatan'] = ['nullable', 'exists:positions,id'];
            $rules['nip'] = ['nullable', 'string', 'max:255'];
        }

        if ($request->role === 'student') {
            $rules['nim'] = ['nullable', 'string', 'max:255'];
            $rules['universitas'] = ['nullable', 'string', 'max:255'];
            $rules['program_studi'] = ['nullable', 'string', 'max:255'];
            $rules['semester'] = ['nullable', 'integer', 'min:1', 'max:14'];
        }

        $request->validate($rules);

        // Update data dasar user
        $user->fill($request->only(['name', 'email', 'role']));

        // Update password jika diisi
        if (!empty($request->password)) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Handle role-specific updates
        $this->handleRoleSpecificUpdates($request, $user);

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui!');
    }

    private function handleRoleSpecificUpdates(Request $request, User $user)
    {
        // Hapus relasi lama jika role berubah
        if ($user->wasChanged('role')) {
            $this->cleanupOldRoleRelations($user);
        }

        // Update berdasarkan role baru
        switch ($user->role) {
            case 'student':
                $this->updateStudentData($request, $user);
                break;
            case 'supervisor':
                $this->updateSupervisorData($request, $user);
                break;
            case 'admin':
                // Admin tidak memerlukan data tambahan
                break;
        }
    }

    private function cleanupOldRoleRelations(User $user)
    {
        $originalRole = $user->getOriginal('role');
        
        if ($originalRole === 'student' && $user->student) {
            $user->student->delete();
        } elseif ($originalRole === 'supervisor' && $user->supervisor) {
            $user->supervisor->delete();
        }
    }

    private function updateStudentData(Request $request, User $user)
    {
        // Buat atau update student record
        $student = $user->student ?: new Student(['user_id' => $user->id]);
        
        $student->fill($request->only([
            'nim',
            'universitas',
            'program_studi',
            'semester'
        ]));

        // Update direktorat
        if ($request->filled('direktorat')) {
            $directorate = Directorate::find($request->input('direktorat'));
            if ($directorate) {
                $student->direktorat = $directorate->name;
            }
        }

        $student->save();
        
        // Pastikan relasi tersimpan
        if (!$user->student) {
            $user->student()->save($student);
        }
    }

    private function updateSupervisorData(Request $request, User $user)
    {
        // Buat atau update supervisor record
        $supervisor = $user->supervisor ?: new Supervisor(['user_id' => $user->id]);
        
        $supervisor->fill($request->only(['nip']));

        // Update direktorat
        if ($request->filled('direktorat')) {
            $directorate = Directorate::find($request->input('direktorat'));
            if ($directorate) {
                $supervisor->direktorat = $directorate->name;
            }
        }

        // Update jabatan
        if ($request->filled('jabatan')) {
            $position = Position::find($request->input('jabatan'));
            if ($position) {
                $supervisor->jabatan = $position->name;
            }
        }

        $supervisor->save();
        
        // Pastikan relasi tersimpan
        if (!$user->supervisor) {
            $user->supervisor()->save($supervisor);
        }
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
