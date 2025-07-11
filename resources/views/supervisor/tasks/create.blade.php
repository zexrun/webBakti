<!DOCTYPE html>
<html lang="id">
<head>
    <title>Buat Tugas Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Buat Tugas Baru</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('supervisor.tasks.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="title" class="form-label">Judul Tugas</label>
                <input type="text" class="form-control" id="title" name="title" value="{{ old('title') }}" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Deskripsi Tugas</label>
                <textarea class="form-control" id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="type" class="form-label">Tipe Tugas</label>
                    <select class="form-select" id="type" name="type" required>
                        <option value="harian">Laporan Harian</option>
                        <option value="akhir">Laporan Akhir</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="due_date" class="form-label">Tanggal Tenggat (Opsional)</label>
                    <input type="date" class="form-control" id="due_date" name="due_date" value="{{ old('due_date') }}">
                </div>
            </div>
            <div class="mb-3">
                <label for="file" class="form-label">Lampirkan File (Opsional)</label>
                <input class="form-control" type="file" id="file" name="file">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Tugaskan Kepada</label>
                <div class="card p-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="select_all_students">
                        <label class="form-check-label" for="select_all_students">
                            <strong>Pilih Semua Mahasiswa</strong>
                        </label>
                    </div>
                    <hr>
                    @forelse($students as $student)
                        <div class="form-check">
                            <input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" value="{{ $student->id }}">
                            <label class="form-check-label" for="student_{{ $student->id }}">
                                {{ $student->user->name }}
                            </label>
                        </div>
                    @empty
                        <p class="text-muted">Anda belum memiliki mahasiswa bimbingan.</p>
                    @endforelse
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan Tugas</button>
            <a href="{{-- route('supervisor.dashboard') --}}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>

<script>
    document.getElementById('select_all_students').addEventListener('change', function(e) {
        document.querySelectorAll('.student-checkbox').forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
    });
</script>