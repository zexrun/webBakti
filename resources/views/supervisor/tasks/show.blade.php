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
                <h6 class="card-subtitle mb-2 text-muted">
                    Pembimbing: {{ $task->supervisor->name }}
                </h6>
                <p class="card-text">{{ $task->description }}</p>
                @if($task->file_path)
                    <a href="{{ asset('storage/' . $task->file_path) }}" class="btn btn-secondary" target="_blank">Unduh Lampiran</a>
                @endif
            </div>
        </div>

        <h4 class="mt-5">Status Submission Mahasiswa</h4>
        <div class="list-group">
            @forelse($assignedStudents as $student)
                @php
                    // Cek apakah ada submission dari mahasiswa ini di koleksi $submissions
                    $submission = $submissions->get($student->id);
                @endphp

                <div class="list-group-item list-group-item-action flex-column align-items-start mb-3 border rounded">
                    <div class="d-flex w-100 justify-content-between">
                        <h5 class="mb-1">{{ $student->user->name }}</h5>

                        @if($submission && is_null($submission->grade))
                            <span class="d-flex justify-content-center align-items-center badge bg-warning text-dark">Menunggu Penilaian</span>
                        @elseif($submission)
                            <span class="d-flex justify-content-center align-items-center badge bg-success">Sudah Mengumpulkan</span>
                        @else
                            <span class="d-flex justify-content-center align-items-center badge bg-danger text-light">Belum Mengumpulkan</span>
                        @endif
                    </div>
                    
                    <hr>

                    @if($submission)
                        {{-- JIKA MAHASISWA SUDAH MENGUMPULKAN --}}
                        <p class="mb-1"><strong>Laporan Teks:</strong> {{ $submission->content ?? '-' }}</p>
                        @if($submission->file_path)
                            <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank">Unduh File Submission</a>
                        @else
                            <p class="text-muted">Tidak ada file yang diunggah.</p>
                        @endif

                        <div class="mt-3 p-3 bg-light rounded">
                            @if($submission->grade)
                                {{-- JIKA SUDAH DINILAI, tampilkan nilai dan tombol edit --}}
                                <h6>Penilaian</h6>
                                <p class="mb-1"><strong>Nilai:</strong> {{ $submission->grade }}</p>
                                <p><strong>Komentar:</strong> {{ $submission->comments ?? '-' }}</p>
                                <button class="btn btn-secondary btn-sm mt-2" onclick="toggleForm('form-nilai-{{ $submission->id }}')">Edit Nilai</button>
                            @else

                             <button class="btn btn-primary btn-sm mt-2" onclick="toggleForm('form-nilai-{{ $submission->id }}')">Beri Nilai</button>
                            @endif
                            
                            <form id="form-nilai-{{ $submission->id }}" action="{{ route('supervisor.submissions.grade', $submission->id) }}" method="POST" class="mt-2" style="display: none;">
                                @csrf
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <select name="grade" class="form-select form-select-sm" required>
                                            <option value="">-- Beri Nilai --</option>
                                            <option value="A" @if($submission->grade == 'A') selected @endif>A</option>
                                            <option value="B" @if($submission->grade == 'B') selected @endif>B</option>
                                            <option value="C" @if($submission->grade == 'C') selected @endif>C</option>
                                            <option value="D" @if($submission->grade == 'D') selected @endif>D</option>
                                        </select>
                                    </div>
                                    <div class="col-md-7">
                                        <input type="text" name="comments" class="form-control form-control-sm" placeholder="Beri Komentar (opsional)" value="{{ $submission->comments }}">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-success btn-sm w-100">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @else
                        {{-- JIKA MAHASISWA BELUM MENGUMPULKAN --}}
                        <p class="text-muted">Menunggu mahasiswa untuk mengumpulkan tugas.</p>
                    @endif
                </div>
            @empty
                <div class="list-group-item text-center">Tidak ada mahasiswa yang ditugaskan untuk tugas ini.</div>
            @endforelse
        </div> 

        <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-secondary mt-3">Kembali</a>
    </div>
    <script>
    function toggleForm(formId) {
        const form = document.getElementById(formId);
        if (form.style.display === "none") {
            form.style.display = "block";
        } else {
            form.style.display = "none";
        }
    }
</script>
</body>
</html>