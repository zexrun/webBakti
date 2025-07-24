@extends('layouts.app')
@section('title', 'List Mahasiswa')
@section('content')
<div class="container mt-5">
	<h2>Edit User: {{ $user->name }}</h2>

	@if ($errors->any())
		<div class="alert alert-danger">
			<ul>
				@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
	@endif

	<form action="{{ route('admin.users.update', $user->id) }}" method="POST">
		@csrf
		@method('PUT') {{-- Method spoofing untuk request UPDATE --}}

		<div class="mb-3">
			<label for="name" class="form-label">Nama Lengkap</label>
			<input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
		</div>
		<div class="mb-3">
			<label for="email" class="form-label">Alamat Email</label>
			<input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
		</div>
		<div class="mb-3">
			<label for="role" class="form-label">Peran (Role)</label>
                <select class="form-select" id="role" name="role" required>
                <option value="admin" @selected(old('role', $user->role) == 'admin')>Admin</option>
                <option value="supervisor" @selected(old('role', $user->role) == 'supervisor')>Supervisor</option>
                <option value="student" @selected(old('role', $user->role) == 'student')>Student</option>			</select>
		</div>
		<hr>
		<p class="text-muted">Isi password hanya jika Anda ingin mengubahnya.</p>
		<div class="mb-3">
			<label for="password" class="form-label">Password Baru</label>
			<input type="password" class="form-control" id="password" name="password">
		</div>
		<div class="mb-3">
			<label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
			<input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
		</div>
		<button type="submit" class="btn btn-primary">Simpan Perubahan</button>
		<a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Batal</a>
	</form>
</div>