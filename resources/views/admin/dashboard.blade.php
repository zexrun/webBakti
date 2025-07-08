<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h4>Dashboard Admin</h4>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger btn-sm">Logout</button>
            </form>
        </div>
        <div class="card-body">
            <h5 class="card-title">Selamat Datang, {{ Auth::user()->name }}!</h5>
            <p class="card-text">Anda login sebagai admin. Gunakan menu di bawah ini untuk mengelola sistem.</p>
            
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <hr>

            <h6>Menu Utama</h6>
            <div class="list-group">
                <a href="{{ route('admin.users.index') }}" class="list-group-item list-group-item-action">
                    Kelola Pengguna (Mahasiswa & Pembimbing)
                </a>
                <a href="{{ route('admin.plotting') }}" class="list-group-item list-group-item-action">
                    Plotting Pembimbing Mahasiswa
                </a>
                {{-- Tambahkan link fitur admin lainnya di sini --}}
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>