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
        <label class="form-label">Tugaskan Kepada</label>
        <div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="assignment_type" id="type_general" value="general" checked>
                <label class="form-check-label" for="type_general">Semua Mahasiswa Bimbingan</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="assignment_type" id="type_specific" value="specific">
                <label class="form-check-label" for="type_specific">Mahasiswa Spesifik</label>
            </div>
        </div>
    </div>

        <div class="mb-3" id="specific_student_select" style="display: none;">
            <label for="student_id" class="form-label">Pilih Mahasiswa</label>
            <select class="form-select" id="student_id" name="student_id">
                <option value="">-- Pilih Mahasiswa --</option>
                {{-- Loop melalui data $students yang sudah dikirim dari controller --}}
                @foreach($students as $student) 
                    <option value="{{ $student->id }}">{{ $student->user->name }}</option>
                @endforeach
            </select>
        </div>
                
            <button type="submit" class="btn btn-primary">Simpan Tugas</button>
            <a href="{{-- route('supervisor.dashboard') --}}" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>

<script>
    document.querySelectorAll('input[name="assignment_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const studentSelect = document.getElementById('specific_student_select');
            if (this.value === 'specific') {
                studentSelect.style.display = 'block';
            } else {
                studentSelect.style.display = 'none';
            }
        });
    });
</script>