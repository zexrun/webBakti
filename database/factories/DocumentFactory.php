<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Document>
 */
class DocumentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'document_name' => 'Dokumen Magang',
            'file_path' => 'documents/placeholder.pdf',
            'type' => 'lainnya',
            'mime_type' => 'application/pdf',
            'file_size' => fake()->numberBetween(100_000, 2_000_000),
            'original_filename' => 'dokumen.pdf',
        ];
    }

    public function proposal(): static
    {
        return $this->state(fn () => [
            'document_name' => 'Proposal Magang',
            'file_path' => 'documents/proposal-placeholder.pdf',
            'type' => 'proposal',
            'original_filename' => 'proposal-magang.pdf',
        ]);
    }

    public function laporanAkhir(): static
    {
        return $this->state(fn () => [
            'document_name' => 'Laporan Akhir Magang',
            'file_path' => 'documents/laporan-akhir-placeholder.pdf',
            'type' => 'laporan_akhir',
            'original_filename' => 'laporan-akhir-magang.pdf',
        ]);
    }
}
