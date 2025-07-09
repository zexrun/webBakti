<!DOCTYPE html>
<html lang="id">
<head>
    <title>Daftar Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Daftar Tugas yang Diberikan</h2>
            <a href="{{ route('supervisor.tasks.create') }}" class="btn btn-primary">Buat Tugas Baru</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Judul Tugas</th>
                    <th>Nama Mahasiswa</th>
                    <th>Tipe</th>
                    <th>Tanggal Dibuat</th>
                    <th>Tenggat Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                    <tr>
                        <td>{{ $task->title }}</td>
                        <td>
                            <ul>
                                @forelse($task->students as $student)
                                    <span>{{ $student->user->name }}</span>
                                @empty
                                    <li>Belum ada mahasiswa yang ditugaskan.</li>
                                @endforelse
                            </ul>
                        </td>
                        <td><span class="badge bg-info">{{ ucfirst($task->type) }}</span></td>
                        <td>{{ $task->created_at->format('d M Y') }}</td>
                        <td>{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : '-' }}</td>
                        <td>
                            <a href="#" class="btn btn-secondary btn-sm">Lihat Submission</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Anda belum membuat tugas apapun.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $tasks->links() }}
        </div>
    </div>
</body>
</html>