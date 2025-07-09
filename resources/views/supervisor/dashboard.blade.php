<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pembimbing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h4>Dashboard Pembimbing</h4>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Logout</button>
            </form>
        </div>
        <div class="card-body">
            <h5 class="card-title">Selamat Datang, {{ Auth::user()->name }}!</h5>
            <p class="card-text">Anda login sebagai Pembimbing. Gunakan menu di bawah ini untuk mengelola sistem.</p>

            <a href="{{ route('supervisor.tasks.index') }}" class="btn btn-primary mb-3">Lihat Penugasan</a>
            <a href="{{ route('supervisor.tasks.create') }}" class="btn btn-primary mb-3">Buat Tugas</a>
            <a href="{{ route('supervisor.students.index') }}" class="btn btn-primary mb-3">Lihat Mahasiswa</a>

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <hr>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>