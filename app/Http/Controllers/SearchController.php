<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Task;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SearchController extends Controller
{
    public function globalSearch(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'all');

        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];

        if ($type === 'all' || $type === 'students') {
            $results['students'] = Student::with('user', 'supervisor.user')
                ->where(function ($q) use ($query) {
                    $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$query%"))
                      ->orWhere('nim', 'like', "%$query%");
                })
                ->limit(5)
                ->get()
                ->map(fn($s) => [
                    'id' => $s->id,
                    'type' => 'student',
                    'title' => $s->user->name,
                    'subtitle' => $s->nim,
                    'icon' => '👤',
                    'url' => route('supervisor.students.index'),
                ]);
        }

        if ($type === 'all' || $type === 'supervisors') {
            $results['supervisors'] = Supervisor::with('user')
                ->whereHas('user', fn($q) => $q->where('name', 'like', "%$query%"))
                ->limit(5)
                ->get()
                ->map(fn($s) => [
                    'id' => $s->id,
                    'type' => 'supervisor',
                    'title' => $s->user->name,
                    'subtitle' => $s->direktorat ?? 'No department',
                    'icon' => '👨‍💼',
                    'url' => '#',
                ]);
        }

        if (Auth::check() && Auth::user()->role === 'supervisor') {
            $supervisor = Auth::user()->supervisor;

            if ($type === 'all' || $type === 'tasks') {
                $results['tasks'] = $supervisor->tasks()
                    ->where('title', 'like', "%$query%")
                    ->limit(5)
                    ->get()
                    ->map(fn($t) => [
                        'id' => $t->id,
                        'type' => 'task',
                        'title' => $t->title,
                        'subtitle' => 'Due: ' . $t->due_date->format('d M Y'),
                        'icon' => '📋',
                        'url' => route('supervisor.tasks.show', $t->id),
                    ]);
            }

            if ($type === 'all' || $type === 'submissions') {
                $results['submissions'] = $supervisor->tasks()
                    ->with('submissions.student.user')
                    ->get()
                    ->flatMap(fn($t) => $t->submissions)
                    ->filter(fn($s) => stripos($s->student->user->name, $query) !== false)
                    ->slice(0, 5)
                    ->values()
                    ->map(fn($s) => [
                        'id' => $s->id,
                        'type' => 'submission',
                        'title' => $s->student->user->name . ' - ' . $s->task->title,
                        'subtitle' => $s->grade ? "Grade: {$s->grade}" : 'Pending',
                        'icon' => '📝',
                        'url' => route('supervisor.submissions.edit', $s->id),
                    ]);
            }
        }

        return response()->json([
            'results' => array_filter($results, fn($r) => count($r) > 0),
            'query' => $query,
        ]);
    }

    public function advancedSearch(Request $request)
    {
        $role = Auth::user()->role;

        if ($role === 'admin') {
            return $this->advancedSearchAdmin($request);
        } elseif ($role === 'supervisor') {
            return $this->advancedSearchSupervisor($request);
        } elseif ($role === 'student') {
            return $this->advancedSearchStudent($request);
        }

        abort(403);
    }

    private function advancedSearchAdmin(Request $request)
    {
        $query = Student::query();

        // Name/NIM search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('name', 'like', "%$search%"))
                  ->orWhere('nim', 'like', "%$search%");
            });
        }

        // Filter by directorate
        if ($request->filled('direktorat')) {
            $query->where('direktorat', $request->get('direktorat'));
        }

        // Filter by supervisor
        if ($request->filled('supervisor_id')) {
            $query->where('supervisor_id', $request->get('supervisor_id'));
        }

        // Filter by university
        if ($request->filled('universitas')) {
            $query->where('universitas', $request->get('universitas'));
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');

        if ($sortBy === 'name') {
            $query->join('users', 'users.id', '=', 'students.user_id')
                  ->orderBy('users.name', $sortDir)
                  ->select('students.*');
        } else {
            $query->orderBy($sortBy, $sortDir);
        }

        $students = $query->with('user', 'supervisor.user')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Search/AdvancedAdmin', [
            'students' => $students,
            'supervisors' => Supervisor::with('user')->get(),
            'directorates' => \App\Models\Directorate::all(),
            'universities' => \App\Models\University::all(),
            'filters' => [
                'search' => $request->get('search'),
                'direktorat' => $request->get('direktorat'),
                'supervisor_id' => $request->get('supervisor_id'),
                'universitas' => $request->get('universitas'),
                'sort_by' => $sortBy,
                'sort_dir' => $sortDir,
            ],
        ]);
    }

    private function advancedSearchSupervisor(Request $request)
    {
        $supervisor = Auth::user()->supervisor;

        // Task search
        $taskQuery = $supervisor->tasks();

        if ($request->filled('task_search')) {
            $taskQuery->where('title', 'like', '%' . $request->get('task_search') . '%');
        }

        if ($request->filled('task_status')) {
            $status = $request->get('task_status');
            if ($status === 'completed') {
                $taskQuery->whereHas('submissions', fn($q) => $q->whereNotNull('grade'));
            } elseif ($status === 'pending') {
                $taskQuery->doesntHave('submissions');
            }
        }

        if ($request->filled('due_date_from')) {
            $taskQuery->whereDate('due_date', '>=', $request->get('due_date_from'));
        }

        if ($request->filled('due_date_to')) {
            $taskQuery->whereDate('due_date', '<=', $request->get('due_date_to'));
        }

        $tasks = $taskQuery->with('submissions')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Search/AdvancedSupervisor', [
            'tasks' => $tasks,
            'filters' => [
                'task_search' => $request->get('task_search'),
                'task_status' => $request->get('task_status'),
                'due_date_from' => $request->get('due_date_from'),
                'due_date_to' => $request->get('due_date_to'),
            ],
        ]);
    }

    private function advancedSearchStudent(Request $request)
    {
        $student = Auth::user()->student;

        $taskQuery = $student->tasks();

        if ($request->filled('task_search')) {
            $taskQuery->where('title', 'like', '%' . $request->get('task_search') . '%');
        }

        if ($request->filled('status')) {
            $status = $request->get('status');
            if ($status === 'submitted') {
                $taskQuery->whereHas('submissions', fn($q) => $q->where('student_id', $student->id));
            } elseif ($status === 'pending') {
                $taskQuery->doesntHave('submissions');
            } elseif ($status === 'graded') {
                $taskQuery->whereHas('submissions', fn($q) => $q->where('student_id', $student->id)->whereNotNull('grade'));
            }
        }

        if ($request->filled('due_date_from')) {
            $taskQuery->whereDate('due_date', '>=', $request->get('due_date_from'));
        }

        if ($request->filled('due_date_to')) {
            $taskQuery->whereDate('due_date', '<=', $request->get('due_date_to'));
        }

        $tasks = $taskQuery->with('submissions')->paginate(20)->withQueryString();

        return Inertia::render('Search/AdvancedStudent', [
            'tasks' => $tasks,
            'studentId' => $student->id,
            'filters' => [
                'task_search' => $request->get('task_search'),
                'status' => $request->get('status'),
                'due_date_from' => $request->get('due_date_from'),
                'due_date_to' => $request->get('due_date_to'),
            ],
        ]);
    }

    public function saveSearch(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'filters' => 'required|json',
        ]);

        \App\Models\SavedSearch::create([
            'user_id' => Auth::id(),
            'name' => $request->get('name'),
            'filters' => $request->get('filters'),
        ]);

        return redirect()->back()->with('success', 'Search saved successfully');
    }

    public function getSavedSearches()
    {
        $searches = \App\Models\SavedSearch::where('user_id', Auth::id())
            ->orderBy('name')
            ->get();

        return view('search.saved-searches', compact('searches'));
    }

    public function applySavedSearch($id)
    {
        $search = \App\Models\SavedSearch::where('user_id', Auth::id())
            ->findOrFail($id);

        $filters = json_decode($search->filters, true);

        return redirect()->route('search.advanced', $filters);
    }

    public function deleteSavedSearch($id)
    {
        $search = \App\Models\SavedSearch::where('user_id', Auth::id())
            ->findOrFail($id);

        $search->delete();

        return redirect()->back()->with('success', 'Search deleted');
    }
}
