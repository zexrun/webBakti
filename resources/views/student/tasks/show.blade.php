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
                <h6 class="text-muted">Diberikan oleh: {{ $task->supervisor->user->name }}</h6>
            </div>
            <div class="card-body">
                <p class="card-text">{{ $task->description }}</p>
                @if($task->file_path)
                    <a href="{{ asset('storage/' . $task->file_path) }}" class="btn btn-secondary" target="_blank">Unduh Lampiran</a>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4>Submission Anda</h4>
            </div>
            <div class="card-body">
                @if($submission)
                {{-- Jika sudah submit, tampilkan detail submission --}}
                <h5>Anda sudah mengumpulkan tugas ini pada {{ $submission->created_at->format('d M Y') }}.</h5>
                
                <p><strong>Laporan Anda:</strong><br>
                    {{ $submission->content ?? '-' }}
                </p>

                @if($submission->file_path)
                    <p>
                        <strong>File Terlampir:</strong> 
                        <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank">Lihat file</a>
                    </p>
                @endif

                    <p><strong>Nilai:</strong> {{ $submission->grade ?? 'Belum dinilai' }}</p>
                    <p><strong>Komentar Pembimbing:</strong> {{ $submission->comments ?? 'Belum ada komentar' }}</p>
                
                @else
                    {{-- Jika belum, tampilkan form submission --}}
                    <form action="{{ route('student.tasks.submit', $task->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="content" class="form-label">Laporan Teks (jika ada)</label>
                            <textarea class="form-control" name="content" id="content" rows="5"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="file" class="form-label">Unggah File (docx, pptx, pdf, zip, dll)</label>
                            <input type="file" class="form-control" name="file" id="file">
                        </div>
                        <button type="submit" class="btn btn-primary">Kumpulkan Tugas</button>
                    </form>
                @endif
            </div>
        </div>
        <a href="{{ route('student.tasks.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </div>
</body>
</html>