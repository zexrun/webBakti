@extends('layouts.app') @section('title', 'List Mahasiswa') @section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-10">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">
            Manajemen Pengguna (Mahasiswa & Pembimbing)
        </h2>
    </div>

    @if (session('danger'))
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
        {{ session("danger") }}
    </div>
    @endif @if(session('success'))
    <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-md">
        {{ session("success") }}
    </div>
    @endif

    <div class="overflow-x-auto rounded-md shadow">
        <table
            class="min-w-full bg-white border border-gray-200 text-sm text-left"
        >
            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-6 py-3 border-b">No</th>
                    <th class="px-6 py-3 border-b">Nama</th>
                    <th class="px-6 py-3 border-b">Username</th>
                    <th class="px-6 py-3 border-b">Email</th>
                    <th class="px-6 py-3 border-b">Peran (Role)</th>
                    <th class="px-6 py-3 border-b">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                <tr>
                    <td class="px-6 py-4">
                        {{ $loop->iteration + $users->firstItem() - 1 }}
                    </td>
                    <td class="px-6 py-4">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->username }}</td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4">
                        <span
                            class="inline-block px-2 py-1 text-xs font-medium bg-gray-200 text-gray-800 rounded"
                        >
                        @if ($user->role == 'admin')
                            Administrator
                        @elseif ($user->role == 'supervisor')
                            Pembimbing
                        @elseif ($user->role == 'student')
                            Mahasiswa
                        @else
                            {{ ucfirst($user->role) }}
                        @endif
                        </span>
                    </td>
                    <td class="px-6 py-4 flex space-x-2">
                        @if(is_null($user->email_verified_at))
                        <form
                            action="{{ route('admin.users.resend_activation', $user->id) }}"
                            method="POST"
                            class="d-inline"
                        >
                            @csrf
                            <button
                                type="submit"
                                class="px-3 py-1 bg-green-600 text-white rounded hover:bg-red-700 text-xs font-semibold"
                            >
                                Kirim Ulang Aktivasi
                            </button>
                        </form>
                        @else
                        <a
                            href="{{ route('admin.users.edit', $user->id) }}"
                            class="px-3 py-1 bg-yellow-400 text-white rounded hover:bg-red-700 text-xs font-semibold"
                            >Edit</a
                        >
                        @endif
                        <form
                            action="{{ route('admin.users.destroy', $user->id) }}"
                            method="POST"
                            onsubmit="return confirm('Anda yakin ingin menghapus user ini?')"
                        >
                            @csrf @method('DELETE')
                            <button
                                type="submit"
                                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs font-semibold"
                            >
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Tidak ada data pengguna.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex justify-center">
        {{ $users->links() }}
    </div>
</div>

@endsection
