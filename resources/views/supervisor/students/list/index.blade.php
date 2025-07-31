@extends('layouts.app') @section('title', 'Dashboard') @section('content')

<div class="max-w-6xl mx-auto px-4 py-10">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">
        Daftar Mahasiswa Bimbingan Anda
    </h2>

    <div class="overflow-x-full bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold">
                        Nama Mahasiswa
                    </th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">
                        NIM
                    </th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">
                        Email
                    </th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">
                        Universitas
                    </th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">
                        Status Dokumen
                    </th>
                    <th class="px-6 py-3 text-left text-sm font-semibold">
                        Aksi
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($students as $student)
                <tr class="hover:bg-gray-50 transition">
                    {{-- Kolom Nama Mahasiswa --}}
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $student->user->name }}
                    </td>

                    {{-- Kolom NIM --}}
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $student->nim ?? 'Belum diisi' }}
                    </td>

                    {{-- Kolom Email --}}
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $student->user->email }}
                    </td>

                    {{-- Kolom Universitas --}}
                    <td class="px-6 py-4 text-sm text-gray-700">
                        {{ $student->universitas ?? 'Belum diisi' }}
                    </td>

                                @php
                                    // Cek kelengkapan dokumen
                                    $hasProposal = $student->documents->where('type', 'proposal')->isNotEmpty();
                                    $hasLaporanAkhir = $student->documents->where('type', 'laporan_akhir')->isNotEmpty();
                                    $documentsComplete = $hasProposal && $hasLaporanAkhir;
                                    
                                    // Cek apakah ada final assessment
                                    $hasFinalAssessment = $student->finalAssessment !== null;
                                    
                                    // Cek apakah sertifikat sudah di-generate
                                    $certificateGenerated = $student->finalAssessment && $student->finalAssessment->certificate_generated_at;
                                @endphp

                    {{-- Kolom Status Laporan Akhir --}}
                    <td class="px-6 py-4 text-sm">
                        @if($documentsComplete)
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800"
                        >
                            Sudah Lengkap
                        </span>
                        @else
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800"
                        >
                            Belum Lengkap
                        </span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-sm font-semibold">
                        <div
                            x-data="{ open: false }"
                            class="relative inline-block text-left"
                        >
                            <button
                                @click="open = !open"
                                type="button"
                                class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none"
                                id="certificate-dropdown-button-{{ $student->id }}"
                                aria-expanded="true"
                                aria-haspopup="true"
                            >
                                Aksi
                                <svg
                                    class="-mr-1 ml-2 h-5 w-5"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    aria-hidden="true"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 9l-7 7-7-7"
                                    />
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                @click.away="open = false"
                                class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                                x-cloak
                            >
                                <div
                                    class="py-1"
                                    role="menu"
                                    aria-orientation="vertical"
                                >
                                    {{-- Lihat Dokumen --}}
                                    <a
                                        href="{{ route('supervisor.students.documents', $student->id) }}"
                                        class="block px-4 py-2 text-sm text-indigo-700 hover:bg-indigo-50 hover:text-indigo-900"
                                        role="menuitem"
                                    >
                                        Lihat Dokumen
                                    </a>

                                    {{-- Status Dokumen --}}
                                    <div
                                        class="px-4 py-2 text-xs text-gray-500 border-b"
                                    >
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <span class="flex items-center">
                                                <i
                                                    class="fas fa-file-text mr-1 {{
                                                        $hasProposal
                                                            ? 'text-green-500'
                                                            : 'text-red-500'
                                                    }}"
                                                ></i>
                                                Proposal:
                                                {{
                                                    $hasProposal
                                                        ? "Ada"
                                                        : "Belum"
                                                }}
                                            </span>
                                        </div>
                                        <div
                                            class="flex items-center space-x-2 mt-1"
                                        >
                                            <span class="flex items-center">
                                                <i
                                                    class="fas fa-file-alt mr-1 {{
                                                        $hasLaporanAkhir
                                                            ? 'text-green-500'
                                                            : 'text-red-500'
                                                    }}"
                                                ></i>
                                                Laporan:
                                                {{
                                                    $hasLaporanAkhir
                                                        ? "Ada"
                                                        : "Belum"
                                                }}
                                            </span>
                                        </div>
                                        <div
                                            class="flex items-center space-x-2 mt-1"
                                        >
                                            <span class="flex items-center">
                                                <i
                                                    class="fas fa-star mr-1 {{
                                                        $hasFinalAssessment
                                                            ? 'text-green-500'
                                                            : 'text-red-500'
                                                    }}"
                                                ></i>
                                                Penilaian:
                                                {{
                                                    $hasFinalAssessment
                                                        ? "Sudah"
                                                        : "Belum"
                                                }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Generate Sertifikat --}}
                                    @if($documentsComplete && !$certificateGenerated)
                                    <a
                                        href="{{ route('supervisor.pdf.certificate.generate', $student->id) }}"
                                        class="block px-4 py-2 text-sm text-green-700 hover:bg-green-50 hover:text-green-900"
                                        role="menuitem"
                                    >
                                        <i class="fas fa-download mr-2"></i>
                                        Generate Certificate
                                    </a>
                                    @else
                                    <div
                                        class="px-4 py-2 text-sm text-gray-400 cursor-not-allowed"
                                    >
                                        <i class="fas fa-certificate mr-2"></i>
                                        Generate Sertifikat
                                        @if(!$documentsComplete)
                                        <div class="text-xs text-red-500 mt-1">
                                            Dokumen tidak lengkap
                                        </div>
                                        @elseif(!$hasFinalAssessment)
                                        <div class="text-xs text-red-500 mt-1">
                                            Belum dinilai
                                        </div>
                                        @elseif($certificateGenerated)
                                        <div
                                            class="text-xs text-green-500 mt-1"
                                        >
                                            Sudah di-generate
                                        </div>
                                        @endif
                                    </div>
                                    @endif

                                    {{-- Download Sertifikat --}}
                                    @if($certificateGenerated)
                                    <a
                                        href="{{ route('supervisor.pdf.certificate.generate', $student->id) }}"
                                        class="block px-4 py-2 text-sm text-green-700 hover:bg-green-50 hover:text-green-900"
                                        role="menuitem"
                                        target="_blank"
                                    >
                                        <i class="fas fa-download mr-2"></i>
                                        Download Sertifikat
                                    </a>
                                    @else
                                    <div
                                        class="px-4 py-2 text-sm text-gray-400 cursor-not-allowed"
                                    >
                                        <i class="fas fa-download mr-2"></i>
                                        Download Sertifikat

                                    </div>
                                    @endif
                                    {{-- Penilaian Akhir --}}

                                    @if($hasFinalAssessment)
                                        {{-- Tambahkan link ini --}}
                                        <a 
                                            href="{{ route('supervisor.students.assessment.edit', $student->id) }}"
                                            class="block px-4 py-2 text-sm text-orange-700 hover:bg-orange-50 hover:text-orange-900"
                                            role="menuitem"
                                        >
                                            <i class="fas fa-star mr-2"></i>
                                            Edit Penilaian
                                        </a>
                                    @else
                                        <a
                                            href="{{ route('supervisor.students.assessment.create', $student->id) }}"
                                            class="block px-4 py-2 text-sm text-orange-700 hover:bg-orange-50 hover:text-orange-900"
                                            role="menuitem"
                                        >
                                            <i class="fas fa-star mr-2"></i>
                                            Berikan Penilaian
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        Anda belum memiliki mahasiswa bimbingan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6 flex justify-center">
        {{ $students->links() }}
    </div>
</div>

@endsection
