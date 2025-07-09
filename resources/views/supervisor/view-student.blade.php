<!DOCTYPE html>
<html lang="id">
<head>
    <title>Mahasiswa Bimbingan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Daftar Mahasiswa Bimbingan Anda</h2>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Nama Mahasiswa</th>
                    <th>NIM</th>
                    <th>Email</th>
                    <th>Universitas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>{{ $student->user->name }}</td>
                        <td>{{ $student->nim ?? 'Belum diisi' }}</td>
                        <td>{{ $student->user->email }}</td>
                        <td>{{ $student->universitas ?? 'Belum diisi' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Anda belum memiliki mahasiswa bimbingan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="d-flex justify-content-center">
            {{ $students->links() }}
        </div>
        
        <div class="mt-4">
            <a href="{{-- route('supervisor.dashboard') --}}" class="btn btn-secondary">Kembali ke Dashboard</a>
        </div>
    </div>
</body>
</html>