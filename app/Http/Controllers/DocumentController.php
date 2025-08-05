<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;



class DocumentController extends Controller
{
    /**
     * Menampilkan daftar dokumen milik mahasiswa.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // Jika supervisor, butuh student_id
        if ($user->role === 'supervisor') {
            $studentId = $request->query('student_id');
            $student = Student::findOrFail($studentId);

            // Validasi bahwa supervisor-nya benar
            if ($student->supervisor_id !== $user->id) {
                abort(403, 'Tidak berhak mengakses dokumen ini');
            }

            $documents = $student->documents;
        } else {
            // Student hanya melihat dokumen miliknya
            $student = $user->student;
            $documents = $student->documents;
        }

        return view('student.documents.index', compact('documents', 'student'));
    }

    /**
     * Menyimpan dokumen baru.
     */
    public function create()
    {
        return view('student.documents.create');
    }

    public function store(Request $request)
    {
        // Debug untuk melihat data yang diterima
        // dd($request->all()); // Uncomment untuk debug

        $student = Auth::user()->student;

        // Validasi sesuai dengan field di form
        $request->validate([
            'document_name' => 'required|string|max:255',
            'type' => 'required|string|in:proposal,laporan_akhir,lainnya',
            'file' => 'required|file|mimes:pdf,pptx,doc,docx,jpg,jpeg,png,rar,zip|max:10240', // 10MB
        ]);

        // Handle file upload
        $file = $request->file('file');

        // Generate nama file yang unik
        $fileName = time() . '_' . $file->getClientOriginalName();

        // Simpan file ke storage/app/public/documents
        $filePath = $file->storeAs('documents', $fileName, 'public');

        // Simpan ke database
        Document::create([
            'student_id' => $student->id,
            'document_name' => $request->document_name,
            'type' => $request->type,
            'file_path' => $filePath,
            // 'status' => 'pending', // Jika ada kolom status
        ]);

        return redirect()->route('student.documents.index')->with('success', 'Dokumen berhasil diunggah.');
    }

    /**
     * Menghapus dokumen milik mahasiswa.
     */
    public function destroy($id)
    {
        $student = Auth::user()->student;
        $document = $student->documents()->findOrFail($id);

        // Hapus file dari storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        // Hapus dari database
        $document->delete();

        return redirect()->route('student.documents.index')->with('success', 'Dokumen berhasil dihapus.');
    }
    /* 
    public function showStudentDocuments($studentId)
    {
        $student = Student::with('documents')
                    ->where('supervisor_id', Auth::id()) // pastikan hanya mahasiswa bimbingannya
                    ->findOrFail($studentId);

        return view('supervisor.students.documents.index', [
            'student' => $student,
            'documents' => $student->documents,
        ]);
    } */
}
