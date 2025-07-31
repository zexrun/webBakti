<body style="font-family: sans-serif; font-size: 14px; color: #333; padding: 30px;">
    <div style="max-width: 700px; margin: 0 auto;">
        <h2 style="text-align: center; font-size: 24px; font-weight: bold; margin-bottom: 10px;">
            Rekap Nilai Magang
        </h2>
        <hr style="border: none; border-top: 2px solid #888; margin-bottom: 20px;">

        <div style="margin-bottom: 30px; line-height: 1.6;">
            <p><strong>Nama Mahasiswa:</strong> {{ $student->user->name }}</p>
            <p><strong>NIM:</strong> {{ $student->nim }}</p>
            <p><strong>Universitas:</strong> {{ $student->universitas }}</p>
            <p><strong>Pembimbing:</strong> {{ $supervisor->name }}</p>
            <p><strong>Tanggal Cetak:</strong> {{ $date }}</p>
        </div>

        <table width="100%" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">
            <thead>
                <tr style="background-color: #f0f0f0;">
                    <th style="border: 1px solid #999; text-align: left;">Nama Tugas</th>
                    <th style="border: 1px solid #999; text-align: left;">Nilai</th>
                    <th style="border: 1px solid #999; text-align: left;">Komentar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $submission)
                    <tr>
                        <td style="border: 1px solid #ccc;">{{ $submission->task->title }}</td>
                        <td style="border: 1px solid #ccc;">{{ $submission->grade }}</td>
                        <td style="border: 1px solid #ccc;">{{ $submission->comments ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" style="text-align: center; padding: 15px; color: #666; font-style: italic;">
                            Belum ada nilai yang diberikan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
