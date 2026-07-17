<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Student;
use App\Models\Supervisor;

use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $query = Supervisor::with(['user', 'students.user', 'students.submissions']);

        // Search by supervisor or student name
        $search = $request->get('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$search%"))
                  ->orWhereHas('students.user', fn($u) => $u->where('name', 'like', "%$search%"));
            });
        }

        // Filter by directorat
        $directorat = $request->get('directorat');
        if ($directorat) {
            $query->where('direktorat', $directorat);
        }

        // Filter by position
        $position = $request->get('position');
        if ($position) {
            $query->where('jabatan', $position);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');

        if ($sortBy === 'name') {
            $query->join('users', 'supervisors.user_id', '=', 'users.id')
                  ->select('supervisors.*')
                  ->orderBy('users.name', $sortOrder);
        } elseif ($sortBy === 'students') {
            $supervisors = $query->get()
                ->sortBy(fn($s) => $s->students->count(), SORT_REGULAR, $sortOrder === 'desc')
                ->values();
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Pagination
        if ($sortBy !== 'students') {
            $supervisors = $query->paginate(10);
        } else {
            $supervisors = collect($supervisors)->forPage($request->get('page', 1), 10);
        }

        // Get unique directorates and positions
        $directorates = Supervisor::whereNotNull('direktorat')
            ->distinct()
            ->pluck('direktorat');

        $positions = Supervisor::whereNotNull('jabatan')
            ->distinct()
            ->pluck('jabatan');

        // Calculate statistics
        $stats = [
            'total_supervisors' => Supervisor::count(),
            'total_students' => Student::count(),
            'avg_students_per_supervisor' => Supervisor::count() > 0
                ? round(Student::count() / Supervisor::count(), 2)
                : 0,
            'active_submissions' => Student::whereHas('submissions', fn($q) => $q->whereNull('grade'))->count(),
        ];

        return view('admin.monitoring.index', compact(
            'supervisors',
            'directorates',
            'positions',
            'search',
            'directorat',
            'position',
            'sortBy',
            'sortOrder',
            'stats'
        ));
    }
    
    public function showSupervisor(Supervisor $supervisor)
    {
        $supervisor->load(['user', 'students.user']);
        
        return view('admin.monitoring.supervisor-detail', compact('supervisor'));
    }
    
    public function showStudent(Student $student)
    {
        $student->load(['user', 'supervisor.user']);

        return view('admin.monitoring.student-detail', compact('student'));
    }

    public function exportCsv(Request $request)
    {
        $query = Supervisor::with(['user', 'students.user', 'students.submissions']);

        // Apply same filters as index
        $search = $request->get('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$search%"))
                  ->orWhereHas('students.user', fn($u) => $u->where('name', 'like', "%$search%"));
            });
        }

        $directorat = $request->get('directorat');
        if ($directorat) {
            $query->where('direktorat', $directorat);
        }

        $position = $request->get('position');
        if ($position) {
            $query->where('jabatan', $position);
        }

        $supervisors = $query->orderBy('created_at', 'desc')->get();

        $filename = 'monitoring-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Supervisor', 'Direktorat', 'Jabatan', 'Email', 'Jumlah Mahasiswa', 'Submissions Pending'];

        $callback = function() use ($supervisors, $columns) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($file, $columns);

            foreach ($supervisors as $supervisor) {
                $pendingSubmissions = $supervisor->students
                    ->flatMap(fn($s) => $s->submissions)
                    ->where('grade', null)
                    ->count();

                fputcsv($file, [
                    $supervisor->user->name,
                    $supervisor->direktorat ?? '-',
                    $supervisor->jabatan ?? '-',
                    $supervisor->user->email,
                    $supervisor->students->count(),
                    $pendingSubmissions
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
