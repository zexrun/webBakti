@extends('layouts.app')

@section('title', 'Student')

@section('content')
@php
    use Illuminate\Support\Str;
@endphp

<div class="min-h-screen flex items-start justify-center p-4 sm:p-6 lg:p-8 bg-gray-500">

    <!-- Container -->
    <div
        class="w-full bg-white rounded-2xl shadow-md overflow-hidden border border-gray-100"
    >
        <!-- Body -->
        <div class="px-8 py-8">
            <h5 class="text-2xl font-bold text-gray-800 mb-2">
                Halo, {{ Auth::user()->name }}! 👋
            </h5>
            <p class="text-gray-600 mb-8 text-sm">
                Kamu login sebagai Mahasiswa. Silakan pilih menu di bawah ini
                untuk mengakses fitur.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Card: Tugas -->
                <a
                    href="{{ route('student.tasks.index') }}"
                    class="block bg-white border border-blue-200 rounded-xl p-5 transition duration-300 ease-in-out hover:shadow-lg hover:border-blue-300 hover:scale-[1.02]"
                >
                    <div class="text-3xl mb-3">📝</div>
                    <div class="text-lg font-semibold text-blue-700">
                        Tugas Saya
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        Lihat dan kerjakan tugas yang diberikan.
                    </p>
                </a>
                <!-- Card: Laporan Harian -->
                <a
                    href="{{ route('student.logbooks.index') }}"
                    class="block bg-white border border-green-200 rounded-xl p-5 transition duration-300 ease-in-out hover:shadow-lg hover:border-green-300 hover:scale-[1.02]"
                >
                    <div class="text-3xl mb-3">📅</div>
                    <div class="text-lg font-semibold text-green-700">
                        Laporan Harian
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        Kelola laporan kegiatan harian kamu.
                    </p>
                </a>
                <!-- Card: Info Akun -->
                <a
                    href="{{ route('student.info.edit') }}"
                    class="block bg-white border border-purple-200 rounded-xl p-5 transition duration-300 ease-in-out hover:shadow-lg hover:border-purple-300 hover:scale-[1.02]"
                >
                    <div class="text-3xl mb-3">👤</div>
                    <div class="text-lg font-semibold text-purple-700">
                        Informasi Akun
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        Edit profil dan informasi akunmu.
                    </p>
                </a>
            </div>

            
            <!-- Success Alert -->
            @if(session('success'))
            <div
                class="p-4 bg-green-100 text-green-800 border border-green-300 rounded-md text-sm"
            >
                ✅ {{ session("success") }}
            </div>
            @endif

{{-- Letakkan ini di bawah div grid-cols-1 md:grid-cols-3 --}}

<hr class="my-8 border-gray-200" />

<div x-data="{ 
    filter: 'all', 
    tasks: {{ $tasks->toJson() }},
    get filteredTasks() {
        const today = new Date().setHours(0,0,0,0);
        const nextWeek = new Date(today);
        nextWeek.setDate(nextWeek.getDate() + 7);

        if (this.filter === 'all') return this.tasks;
        return this.tasks.filter(task => {
            if (!task.due_date) return false; // Abaikan tugas tanpa tenggat
            const dueDate = new Date(task.due_date).setHours(0,0,0,0);
            if (this.filter === 'overdue') return dueDate < today;
            if (this.filter === 'today') return dueDate === today;
            if (this.filter === 'week') return dueDate > today && dueDate <= nextWeek;
        });
    }
}">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Tugas Mendatang</h3>

    <div class="flex space-x-2 border-b border-gray-200 mb-4">
        <button @click="filter = 'all'" :class="{ 'border-indigo-600 text-indigo-600': filter === 'all' }" class="px-3 py-2 text-sm font-medium border-b-2 border-transparent">Semua</button>
        <button @click="filter = 'overdue'" :class="{ 'border-red-600 text-red-600': filter === 'overdue' }" class="px-3 py-2 text-sm font-medium border-b-2 border-transparent">Terlambat</button>
        <button @click="filter = 'today'" :class="{ 'border-blue-600 text-blue-600': filter === 'today' }" class="px-3 py-2 text-sm font-medium border-b-2 border-transparent">Hari Ini</button>
        <button @click="filter = 'week'" :class="{ 'border-green-600 text-green-600': filter === 'week' }" class="px-3 py-2 text-sm font-medium border-b-2 border-transparent">Minggu Ini</button>
    </div>

    <div class="space-y-4">
        <template x-for="task in filteredTasks" :key="task.id">
            <div class="p-4 border rounded-lg flex justify-between items-center">
                <div>
                    <p class="font-semibold text-gray-800" x-text="task.title"></p>
                    <p class="text-sm text-gray-500">
                        Tenggat: <span x-text="new Date(task.due_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></span>
                    </p>
                </div>
                <a :href="`/student/tasks/${task.id}`" class="text-indigo-600 hover:underline text-sm font-semibold">Lihat Detail</a>
            </div>
        </template>
        <template x-if="filteredTasks.length === 0">
            <p class="text-center text-gray-500 py-6 italic">Tidak ada tugas dalam kategori ini.</p>
        </template>
    </div>
</div>

            <hr class="mt-10 border-gray-200" />
        </div>
    </div>
</div>
@endsection
