<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        $student = auth()->user()->student;
        $universities = [];

        try {
            $jsonPath = public_path('data/universities.json');
            $allUniversityData = json_decode(file_get_contents($jsonPath), true);

            // Ambil hanya kolom 'name' dari setiap objek
            $universities = collect($allUniversityData)->pluck('name')->sort()->values();
            
        } catch (\Exception $e) {
            // Biarkan array kosong jika file tidak ditemukan atau ada error
        }

        // Kirim KEDUA variabel ('student' dan 'universities') ke view
        return view('student.info.edit', compact('user', 'student', 'universities'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nim' => 'required|string|max:25',
            'universitas' => 'required|string|max:100',
            'program_studi' => 'required|string|max:100',
            'semester' => 'required|integer|min:1|max:14',
            'periode_mulai' => 'required|date',
            'periode_selesai' => 'required|date|after_or_equal:periode_mulai',
        ]);

        $student = Auth::user()->student;

        $student->update([
            'nim' => $request->nim,
            'universitas' => $request->universitas,
            'program_studi' => $request->program_studi,
            'semester' => $request->semester,
            'periode_mulai' => $request->periode_mulai,
            'periode_selesai' => $request->periode_selesai,
        ]);

        return redirect()->route('student.info.edit')->with('success', "Data status mahasiswa berhasil diperbarui");
    }
}
