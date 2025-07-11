<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    // Method untuk menampilkan halaman form upload
    public function index()
    {
        return view('upload');
    }

    // Method untuk memproses file yang di-upload
    public function store(Request $request)
    {
        // 1. Validasi file
        $request->validate([
            'file' => 'required|file|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Simpan file
        $file = $request->file('file');
        $nama_file = time()."_".$file->getClientOriginalName();
        
        // Isi dengan nama folder tempat kemana file diupload
        $tujuan_upload = 'data_file';
        $file->move($tujuan_upload, $nama_file);

        // 3. Kembali ke halaman upload dengan pesan sukses
        return redirect()->back()->with('success', 'File berhasil di-upload!')->with('file', $nama_file);
    }
}