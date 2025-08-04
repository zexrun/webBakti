<div class="overflow-x-auto rounded-md shadow mb-6">
    <table class="min-w-full bg-white border border-gray-200 text-sm text-left">
        <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
            <tr>
                <th class="px-6 py-3 border-b">No</th>
                <th class="px-6 py-3 border-b">Nama</th>
                <th class="px-6 py-3 border-b">Username</th>
                <th class="px-6 py-3 border-b">Email</th>
                <th class="px-6 py-3 border-b">Direktorat</th>
                <th class="px-6 py-3 border-b">Jabatan</th>
                <th class="px-6 py-3 border-b">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($users as $user)
                <tr>
                    <td class="px-6 py-4">{{ $loop->iteration + $users->firstItem() - 1 }}</td>
                    <td class="px-6 py-4">{{ $user->name }}</td>
                    <td class="px-6 py-4">{{ $user->username }}</td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4">{{ $user->supervisor?->direktorat ?? $user->student?->direktorat ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $user->supervisor?->jabatan ??  '-' }}</td>
                    <td class="px-6 py-4 flex space-x-2">
                        @if(is_null($user->email_verified_at))
                            <form action="{{ route('admin.users.resend_activation', $user->id) }}" method="POST">
                                @csrf
                                <button class="px-3 py-1 bg-green-600 text-white rounded text-xs hover:bg-green-700">
                                    Kirim Ulang Aktivasi
                                </button>
                            </form>
                        @else
                            <a href="{{ route('admin.users.edit', $user->id) }}"
                               class="px-3 py-1 bg-yellow-400 text-white rounded text-xs hover:bg-yellow-500">
                                Edit
                            </a>
                        @endif
                        <form action="{{ route('admin.users.destroy', $user->id) }}"
                              method="POST"
                              onsubmit="return confirm('Anda yakin ingin menghapus user ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="px-3 py-1 bg-red-600 text-white rounded text-xs hover:bg-red-700">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data pengguna.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
