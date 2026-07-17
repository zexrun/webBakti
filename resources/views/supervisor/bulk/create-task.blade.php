@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Buat Tugas Massal</h1>
                <p class="text-gray-600">Buat multiple tugas sekaligus untuk mahasiswa</p>
            </div>
            <a href="{{ route('supervisor.tasks.index') }}" class="px-4 py-2 text-blue-600 hover:text-blue-800 font-medium">
                ← Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <h3 class="font-semibold text-red-800 mb-2">Terjadi Kesalahan:</h3>
            <ul class="list-disc list-inside text-red-700 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('supervisor.bulk.store-task') }}" method="POST" id="bulkTaskForm">
        @csrf

        <!-- Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-blue-800">
                <strong>Panduan:</strong> Tambahkan tugas di bawah, pilih mahasiswa untuk setiap tugas, kemudian submit sekaligus.
            </p>
        </div>

        <!-- Tasks Container -->
        <div id="tasksContainer" class="space-y-6 mb-8">
            <!-- Task Template (will be cloned) -->
            <template id="taskTemplate">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 task-item" data-task-index="0">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">Tugas <span class="task-number">1</span></h3>
                        <button type="button" class="text-red-600 hover:text-red-800 font-medium remove-task">
                            Hapus
                        </button>
                    </div>

                    <!-- Title -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Tugas</label>
                        <input type="text" name="tasks[0][title]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900" placeholder="Contoh: Membuat dokumentasi API" required>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (Opsional)</label>
                        <textarea name="tasks[0][description]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900" rows="3" placeholder="Masukkan deskripsi tugas..."></textarea>
                    </div>

                    <!-- Due Date -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Deadline</label>
                        <input type="date" name="tasks[0][due_date]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900" required>
                    </div>

                    <!-- Student Selection -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Mahasiswa (minimal 1)</label>
                        <div class="max-h-64 overflow-y-auto border border-gray-300 rounded-lg p-3 bg-gray-50">
                            @forelse($students as $student)
                                <label class="flex items-center py-2 cursor-pointer hover:bg-gray-100 px-2 rounded">
                                    <input type="checkbox" name="tasks[0][student_ids][]" value="{{ $student->id }}" class="rounded">
                                    <span class="ml-3 text-sm text-gray-700">
                                        {{ $student->user->name }} ({{ $student->nim }})
                                    </span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-600">Belum ada mahasiswa</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </template>

            <!-- First Task (initial) -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 task-item" data-task-index="0">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Tugas <span class="task-number">1</span></h3>
                    <button type="button" class="text-red-600 hover:text-red-800 font-medium remove-task" style="display: none;">
                        Hapus
                    </button>
                </div>

                <!-- Title -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Judul Tugas</label>
                    <input type="text" name="tasks[0][title]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900" placeholder="Contoh: Membuat dokumentasi API" required>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (Opsional)</label>
                    <textarea name="tasks[0][description]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900" rows="3" placeholder="Masukkan deskripsi tugas..."></textarea>
                </div>

                <!-- Due Date -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Deadline</label>
                    <input type="date" name="tasks[0][due_date]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-gray-900" required>
                </div>

                <!-- Student Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Mahasiswa (minimal 1)</label>
                    <div class="max-h-64 overflow-y-auto border border-gray-300 rounded-lg p-3 bg-gray-50">
                        @forelse($students as $student)
                            <label class="flex items-center py-2 cursor-pointer hover:bg-gray-100 px-2 rounded">
                                <input type="checkbox" name="tasks[0][student_ids][]" value="{{ $student->id }}" class="rounded">
                                <span class="ml-3 text-sm text-gray-700">
                                    {{ $student->user->name }} ({{ $student->nim }})
                                </span>
                            </label>
                        @empty
                            <p class="text-sm text-gray-600">Belum ada mahasiswa</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Task Button -->
        <button type="button" id="addTaskBtn" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 mb-8 font-medium">
            + Tambah Tugas Lagi
        </button>

        <!-- Submit -->
        <div class="flex gap-3">
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                Buat Tugas Massal
            </button>
            <a href="{{ route('supervisor.tasks.index') }}" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium">
                Batal
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const template = document.getElementById('taskTemplate');
    const container = document.getElementById('tasksContainer');
    const addBtn = document.getElementById('addTaskBtn');
    let taskCount = 1;

    addBtn.addEventListener('click', function() {
        const newTask = template.content.cloneNode(true);
        const taskElement = newTask.querySelector('.task-item');

        // Update indices
        taskElement.setAttribute('data-task-index', taskCount);
        newTask.querySelector('.task-number').textContent = taskCount + 1;

        // Update name attributes
        const inputs = newTask.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            if (name) {
                const newName = name.replace(/\[\d+\]/g, '[' + taskCount + ']');
                input.setAttribute('name', newName);
            }
        });

        // Show remove button
        newTask.querySelector('.remove-task').style.display = 'block';
        newTask.querySelector('.remove-task').addEventListener('click', function(e) {
            e.preventDefault();
            taskElement.remove();
            updateTaskNumbers();
        });

        container.appendChild(taskElement);
        taskCount++;
        updateRemoveButtonVisibility();
    });

    // Handle initial remove buttons
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-task')) {
            e.preventDefault();
            e.target.closest('.task-item').remove();
            updateTaskNumbers();
            updateRemoveButtonVisibility();
        }
    });

    function updateTaskNumbers() {
        const tasks = container.querySelectorAll('.task-item');
        tasks.forEach((task, index) => {
            task.querySelector('.task-number').textContent = index + 1;
        });
    }

    function updateRemoveButtonVisibility() {
        const tasks = container.querySelectorAll('.task-item');
        tasks.forEach(task => {
            const removeBtn = task.querySelector('.remove-task');
            removeBtn.style.display = tasks.length > 1 ? 'block' : 'none';
        });
    }

    updateRemoveButtonVisibility();
});
</script>
@endpush
@endsection
