<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Directorate;
use App\Models\Position;
use App\Models\University;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        // Get active tab from request, default to 'directorates'
        $activeTab = $request->get('tab', 'directorates');
        
        // Search parameters
        $searchDirectorate = $request->get('search_directorate');
        $searchPosition = $request->get('search_position');
        $searchUniversity = $request->get('search_university');
        
        // Directorates with pagination and search
        $directoratesQuery = Directorate::query();
        if ($searchDirectorate) {
            $directoratesQuery->where('name', 'like', '%' . $searchDirectorate . '%');
        }
        $directorates = $directoratesQuery->orderBy('name')->paginate(10, ['*'], 'directorates_page');
        
        // Positions with pagination and search
        $positionsQuery = Position::query();
        if ($searchPosition) {
            $positionsQuery->where('name', 'like', '%' . $searchPosition . '%');
        }
        $positions = $positionsQuery->orderBy('name')->paginate(10, ['*'], 'positions_page');
        
        // Universities with pagination and search
        $universitiesQuery = University::query();
        if ($searchUniversity) {
            $universitiesQuery->where('name', 'like', '%' . $searchUniversity . '%')
                             ->orWhere('domain', 'like', '%' . $searchUniversity . '%');
        }
        $universities = $universitiesQuery->orderBy('name')->paginate(15, ['*'], 'universities_page');
        
        // Preserve search parameters in pagination links
        $directorates->appends(['tab' => 'directorates', 'search_directorate' => $searchDirectorate]);
        $positions->appends(['tab' => 'positions', 'search_position' => $searchPosition]);
        $universities->appends(['tab' => 'universities', 'search_university' => $searchUniversity]);
        
        return view('admin.settings.index', compact(
            'directorates', 
            'positions', 
            'universities', 
            'activeTab',
            'searchDirectorate',
            'searchPosition', 
            'searchUniversity'
        ));
    }

    // Existing methods remain the same...
    public function storeDirectorate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:directorates,name',
        ]);

        Directorate::create($validated);
        return redirect()->route('admin.settings.index', ['tab' => 'directorates'])
                        ->with('success', 'Direktorat berhasil ditambahkan');
    }

    public function deleteDirectorate($id)
    {
        $directorate = Directorate::findOrFail($id);
        $directorate->delete();
        return redirect()->route('admin.settings.index', ['tab' => 'directorates'])
                        ->with('success', 'Direktorat berhasil dihapus');
    }

    public function updateDirectorate(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:directorates,name,' . $id,
        ]);

        $directorate = Directorate::findOrFail($id);
        $directorate->update(['name' => $request->name]);
        return redirect()->route('admin.settings.index', ['tab' => 'directorates'])
                        ->with('success', 'Direktorat berhasil diperbarui');
    }

    public function storePosition(Request $request)
    {
        $request->validate(['name' => 'required|string|unique:positions,name']);
        Position::create(['name' => $request->name]);
        return redirect()->route('admin.settings.index', ['tab' => 'positions'])
                        ->with('success_position', 'Jabatan berhasil ditambahkan!');
    }

    public function updatePosition(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|unique:positions,name,' . $id]);
        $position = Position::findOrFail($id);
        $position->name = $request->name;
        $position->save();
        return redirect()->route('admin.settings.index', ['tab' => 'positions'])
                        ->with('success_position', 'Jabatan berhasil diperbarui!');
    }

    public function deletePosition($id)
    {
        $position = Position::findOrFail($id);
        $position->delete();
        return redirect()->route('admin.settings.index', ['tab' => 'positions'])
                        ->with('success_position', 'Jabatan berhasil dihapus!');
    }

    public function storeUniversity(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:universities,name',
            'domain' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        University::create([
            'name' => $request->name,
            'domain' => $request->domain,
            'website' => $request->website,
        ]);

        return redirect()->route('admin.settings.index', ['tab' => 'universities'])
                        ->with('success_university', 'Universitas berhasil ditambahkan!');
    }

    public function updateUniversity(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:universities,name,' . $id,
            'domain' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
        ]);

        $university = University::findOrFail($id);
        $university->update([
            'name' => $request->name,
            'domain' => $request->domain,
            'website' => $request->website,
        ]);

        return redirect()->route('admin.settings.index', ['tab' => 'universities'])
                        ->with('success_university', 'Universitas berhasil diperbarui!');
    }

    public function deleteUniversity($id)
    {
        $university = University::findOrFail($id);
        $university->delete();
        return redirect()->route('admin.settings.index', ['tab' => 'universities'])
                        ->with('success_university', 'Universitas berhasil dihapus!');
    }
}