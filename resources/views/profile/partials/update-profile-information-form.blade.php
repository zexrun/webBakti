<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Informasi Profil
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Perbarui informasi profil dan alamat email akun Anda.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

    @if (is_null($user->username))
        <div class="mb-3">
            <label for="username" class="form-label">Buat Username Anda</label>
            <input id="username" name="username" type="text" class="form-control" required>
            <div class="form-text">Username hanya bisa dibuat satu kali dan tidak bisa diubah.</div>
        </div>
    @else
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input id="username" type="text" class="form-control" value="{{ $user->username }}" disabled readonly>
        </div>
    @endif

        <div class="mb-3">
            <label for="name">Nama Lengkap</label>
            <input id="name" name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required autofocus>
        </div>

        <div class="mb-3">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>

        @if ($user->role === 'student' && $user->student)
            <div class="mb-3">
                <label for="nim">NIM</label>
                <input id="nim" name="nim" type="text" class="form-control" value="{{ old('nim', $user->student->nim) }}">
            </div>
            <div class="mb-3">
                <label for="universitas">Universitas</label>
                <input id="universitas" name="universitas" type="text" class="form-control" value="{{ old('universitas', $user->student->universitas) }}">
            </div>
        @elseif ($user->role === 'supervisor' && $user->supervisor)
            <div class="mb-3">
                <label for="nip">NIP</label>
                <input id="nip" name="nip" type="text" class="form-control" value="{{ old('nip', $user->supervisor->nip) }}">
            </div>
            <div class="mb-3">
                <label for="jabatan">Jabatan</label>
                <input id="jabatan" name="jabatan" type="text" class="form-control" value="{{ old('jabatan', $user->supervisor->jabatan) }}">
            </div>
        @endif

        <div class="d-flex align-items-center gap-4">
            <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
    </form>
</section>