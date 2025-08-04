<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Setting;
use App\Models\Directorate;
use App\Models\Position;
use Illuminate\Http\Request;


class SettingController extends Controller
{
    public function index()
    {
        // Ambil semua direktorat dari database
        $directorates = Directorate::orderBy('name')->get();
        $positions = Position::all();
        // Tampilkan ke view settings
        return view('admin.settings.index', compact('directorates', 'positions'));
    }

    public function storeDirectorate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:directorates,name',
        ]);

        Directorate::create($validated);

        return redirect()->back()->with('success', 'Direktorat berhasil ditambahkan');
    }

    public function deleteDirectorate($id)
    {
        $directorate = Directorate::findOrFail($id);
        $directorate->delete();

        return redirect()->back()->with('success', 'Direktorat berhasil dihapus');
    }



    public function updateDirectorate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:directorates,name,' . $id,
        ]);

        $directorate = Directorate::findOrFail($id);
        $directorate->update(['name' => $request->name]);

        return redirect()->route('admin.settings.index')->with('success', 'Direktorat berhasil diperbarui');
    }

    public function storePosition(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:positions,name']);
        Position::create(['name' => $request->name]);
        return redirect()->back()->with('success_position', 'Jabatan berhasil ditambahkan!');
    }

    public function updatePosition(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|unique:positions,name,' . $id]);
        $position = Position::findOrFail($id);
        $position->name = $request->name;
        $position->save();
        return redirect()->back()->with('success_position', 'Jabatan berhasil diperbarui!');
    }

    public function deletePosition($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();
        return redirect()->back()->with('success_position', 'Jabatan berhasil dihapus!');
    }
}
