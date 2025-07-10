<!DOCTYPE html>
<html lang="id">
<head>
    <title>Daftar Tugas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Daftar Tugas Anda</h2>

        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Judul Tugas</th>
                    <th>Tipe</th>
                    <th>Diberikan oleh</th>
                    <th>Tenggat Waktu</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                    <tr>
                        <td>{{ $task->title }}</td>
                        <td><span class="badge bg-primary">{{ ucfirst($task->type) }}</span></td>
                        <td>{{ $task->supervisor->user->name }}</td>
                        <td>{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y H:i') : '-' }}</td>
                        <td>
                            <a href="{{ route('student.tasks.show', $task->id) }}" class="btn btn-info btn-sm">Lihat Detail & Submit</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Belum ada tugas yang diberikan kepada Anda.</td>
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