<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use finfo;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;



class DocumentController extends Controller
{
    /**
     * Menampilkan daftar dokumen milik mahasiswa.
     */
    public function index(Request $request): InertiaResponse
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

        return Inertia::render('Student/Documents/Index', compact('documents', 'student'));
    }

    /**
     * Menyimpan dokumen baru.
     */
    public function create(): InertiaResponse
    {
        return Inertia::render('Student/Documents/Create');
    }

    public function store(Request $request)
    {
        $student = Auth::user()->student;

        $request->validate([
            'document_name' => 'required|string|max:255',
            'type' => 'required|string|in:proposal,laporan_akhir,lainnya',
            'file' => 'required|file|mimes:pdf,pptx,doc,docx,jpg,jpeg,png,zip|max:10240',
        ]);

        $file = $request->file('file');

        // Validate MIME type using finfo (magic bytes)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file->getRealPath());
        finfo_close($finfo);

        $allowedMimeTypes = [
            'application/pdf',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg',
            'image/png',
            'application/zip',
        ];

        if (!in_array($mimeType, $allowedMimeTypes)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'File type tidak diizinkan. Gunakan PDF, Word, PowerPoint, Image, atau ZIP.');
        }

        // Generate secure random filename with extension
        $extension = $file->extension();
        $secureFilename = Str::uuid() . '.' . $extension;

        // Store in private disk (storage/app/private)
        $storagePath = 'documents/' . date('Y/m/d');
        $filePath = $file->storeAs($storagePath, $secureFilename, 'private');

        Document::create([
            'student_id' => $student->id,
            'document_name' => $request->document_name,
            'type' => $request->type,
            'file_path' => $filePath,
            'mime_type' => $mimeType,
            'file_size' => $file->getSize(),
            'original_filename' => $file->getClientOriginalName(),
        ]);

        return redirect()->route('student.documents.index')->with('success', 'Dokumen berhasil diunggah.');
    }

    /**
     * Menyajikan file dokumen dari private disk (bukan public storage,
     * karena file diunggah dengan Storage::disk('private')).
     */
    public function download($id): Response
    {
        $student = Auth::user()->student;
        $document = $student->documents()->findOrFail($id);

        if (!Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        return Storage::disk('private')->response(
            $document->file_path,
            $document->original_filename,
        );
    }

    public function destroy($id)
    {
        $student = Auth::user()->student;
        $document = $student->documents()->findOrFail($id);

        try {
            // Delete from private disk
            if (Storage::disk('private')->exists($document->file_path)) {
                Storage::disk('private')->delete($document->file_path);
            }

            $document->delete();
            return redirect()->route('student.documents.index')
                ->with('success', 'Dokumen berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus dokumen. Silahkan coba lagi.');
        }
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
