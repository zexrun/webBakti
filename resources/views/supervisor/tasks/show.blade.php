<!DOCTYPE html>
<html lang="id">
<head>
    <title>Detail Tugas: {{ $task->title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card mb-4">
            <div class="card-header">
                <h3>{{ $task->title }}</h3>
            </div>
            <div class="card-body">
                <h6 class="card-subtitle mb-2 text-muted">
                    Tipe: {{ ucfirst($task->type) }} | Tenggat: {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : 'Tidak ada' }}
                </h6>
                <p class="card-text">{{ $task->description }}</p>
                @if($task->file_path)
                    <a href="{{ asset('storage/' . $task->file_path) }}" class="btn btn-secondary" target="_blank">Unduh Lampiran</a>
                @endif
            </div>
        </div>

        <h4>Submission Mahasiswa</h4>
        <div class="list-group">
            @forelse($task->submissions as $submission)
                <div class="list-group-item">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">{{ $submission->student->user->name }}</h5>
                        <small>Dikumpulkan pada: {{ $submission->created_at->format('d M Y, H:i') }}</small>
                    </div>
                    <p class="mb-1">{{ $submission->content }}</p>
                    @if($submission->file_path)
                        <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank">Lihat file submission</a>
                    @endif
                    <hr>
                    <p><strong>Nilai:</strong> {{ $submission->grade ?? 'Belum dinilai' }}</p>
                    <p><strong>Komentar:</strong> {{ $submission->comments ?? 'Belum ada komentar' }}</p>
                </div>
            @empty
                <div class="list-group-item text-center">Belum ada mahasiswa yang mengumpulkan.</div>
            @endforelse
        </div>
        <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </div>
</body>
</html>