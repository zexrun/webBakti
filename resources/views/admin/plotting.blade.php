<!DOCTYPE html>
<html lang="id">
<head>
    <title>Plotting Pembimbing Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Plotting Pembimbing Mahasiswa</h2>
        <p>Pilih dosen pembimbing untuk setiap mahasiswa yang tersedia.</p>
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Nama Mahasiswa</th>
                    <th>NIM</th>
                    <th>Universitas</th>
                    <th>Pembimbing Saat Ini</th>
                    <th>Tugaskan Pembimbing</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>{{ $student->user->name }}</td>
                        <td>{{ $student->nim }}</td>
                        <td>{{ $student->universitas }}</td>
                        <td>
                            {{-- Tampilkan nama pembimbing jika ada, jika tidak tampilkan pesan --}}
                            {{ $student->supervisor->user->name ?? 'Belum Ditugaskan' }}
                        </td>
                        <td style="width: 40%;">
                            <form action="{{ route('admin.plotting.assign') }}" method="POST" class="d-flex">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ $student->id }}">
                                <select name="supervisor_id" class="form-select me-2">
                                    @foreach($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}" @if($student->supervisor_id == $supervisor->id) selected @endif>
                                            {{ $supervisor->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Tidak ada data mahasiswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mt-3">Kembali ke Dashboard</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>