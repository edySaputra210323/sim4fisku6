<?php

namespace App\Exports;

use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\AbsensiHeader;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapJurnalGuruExport implements FromCollection, WithHeadings, WithStyles, WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $guruId;
    protected $kelasId;
    protected $mapelId;

    public function __construct($startDate, $endDate, $guruId = null, $kelasId = null, $mapelId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->guruId = $guruId;
        $this->kelasId = $kelasId;
        $this->mapelId = $mapelId;
    }

    public function collection()
    {
        return AbsensiHeader::with(['guru', 'kelas', 'mapel', 'absensiDetails.riwayatKelas.dataSiswa'])
            ->when($this->guruId, fn ($q) => $q->where('pegawai_id', $this->guruId))
            ->when($this->kelasId, fn ($q) => $q->where('kelas_id', $this->kelasId))
            ->when($this->mapelId, fn ($q) => $q->where('mapel_id', $this->mapelId))
            ->whereBetween('tanggal', [$this->startDate, $this->endDate])
            ->orderBy('tanggal')
            ->get();
    }

    public function headings(): array
    {
        return [
            ['Tanggal', 'Jam Ke', 'Materi', 'Kegiatan', 'Kehadiran Peserta Didik', '', ''],
            ['', '', '', '', 'Sakit', 'Izin', 'Alpa'],
        ];
    }

    public function map($record): array
    {
        // Format nama siswa: huruf pertama kapital di setiap kata
        $formatNama = fn($nama) => ucwords(strtolower($nama));

        $sakit = $record->absensiDetails
            ->where('status', 'sakit')
            ->pluck('riwayatKelas.dataSiswa.nama_siswa')
            ->map($formatNama)
            ->implode("\n");

        $izin = $record->absensiDetails
            ->where('status', 'izin')
            ->pluck('riwayatKelas.dataSiswa.nama_siswa')
            ->map($formatNama)
            ->implode("\n");

        $alpa = $record->absensiDetails
            ->where('status', 'alpa')
            ->pluck('riwayatKelas.dataSiswa.nama_siswa')
            ->map($formatNama)
            ->implode("\n");

        return [
            $record->tanggal->format('d/m/Y'),
            is_array($record->jam_ke) ? implode(' & ', $record->jam_ke) : $record->jam_ke,
            $record->materi,
            $record->kegiatan,
            $sakit,
            $izin,
            $alpa,
        ];
    }

    public function styles(Worksheet $sheet)
{
    $first = $this->collection()->first();

    // ============================================================
    // 🔹 1️⃣ Geser data ke bawah agar header lembaga tidak tertimpa
    // ============================================================
    $sheet->insertNewRowBefore(1, 15);

    // ============================================================
    // 🔹 2️⃣ Ambil data tahun & semester aktif
    // ============================================================
    $tahunAktif = TahunAjaran::where('status', 1)->first();
    $semesterAktif = Semester::where('status', 1)->first();

    $tahunAjaranText = $tahunAktif?->th_ajaran ?? '-';
    $semesterText = strtoupper($semesterAktif?->nm_semester ?? '-');

    // ============================================================
    // 🔹 3️⃣ HEADER LEMBAGA
    // ============================================================
    foreach (range(1, 8) as $row) {
        $sheet->mergeCells("A{$row}:G{$row}");
    }

    $sheet->setCellValue('A1', 'AL-FITYAN SCHOOL KUBU RAYA');
    $sheet->setCellValue('A2', 'No. Dokumen : YYS-F-KUR-0305 / No. Revisi : 00 / Berlaku : 01 Juli 2024');
    $sheet->setCellValue('A4', 'JURNAL MENGAJAR');
    $sheet->setCellValue('A5', "SEMESTER {$semesterText}");
    $sheet->setCellValue('A6', 'SMPIT AL-FITYAN KUBU RAYA');
    $sheet->setCellValue('A7', "TAHUN AJARAN {$tahunAjaranText}");

    $sheet->getStyle('A1:A7')->applyFromArray([
        'font' => [
            'bold' => true,
            'name' => 'Times New Roman',
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER,
        ],
    ]);

    $sheet->getStyle('A1')->getFont()->setSize(12);
    $sheet->getStyle('A2')->getFont()->setSize(12);
    $sheet->getStyle('A4')->getFont()->setSize(18);
    $sheet->getStyle('A5:A7')->getFont()->setSize(14);

    // ============================================================
    // 🔹 3️⃣.1️⃣ TAMBAHKAN LOGO DI SEBELAH KIRI KOP
    // ============================================================
    $logoPath = public_path('images/logoSMPIT.png');
    if (file_exists($logoPath)) {
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setPath($logoPath);
        $drawing->setHeight(85); // tinggi logo dalam pixel
        $drawing->setCoordinates('A1'); // posisinya di cell A1
        $drawing->setOffsetX(25); // geser sedikit ke kanan dari batas sel
        $drawing->setOffsetY(15);  // geser sedikit ke bawah agar sejajar teks
        $drawing->setWorksheet($sheet);
    }

    // ============================================================
    // 🔹 4️⃣ INFO GURU / KELAS / MAPEL
    // ============================================================
    $sheet->setCellValue('A10', 'Kelas          : ' . optional($first?->kelas)->nama_kelas);
    $sheet->setCellValue('A11', 'Mata Pelajaran : ' . optional($first?->mapel)->nama_mapel);
    $sheet->setCellValue('A12', 'Nama Guru      : ' . optional($first?->guru)->nm_pegawai);

    $sheet->getStyle('A10:A12')->applyFromArray([
        'font' => [
            'bold' => true,
            'size' => 11,
            'name' => 'Times New Roman',
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_LEFT,
            'vertical'   => Alignment::VERTICAL_CENTER,
        ],
    ]);

    // ============================================================
    // 🔹 5️⃣ HEADER TABEL UTAMA
    // ============================================================
    $sheet->mergeCells('A16:A17');
    $sheet->mergeCells('B16:B17');
    $sheet->mergeCells('C16:C17');
    $sheet->mergeCells('D16:D17');
    $sheet->mergeCells('E16:G16');

    $sheet->getStyle('A16:G17')->applyFromArray([
        'font' => [
            'bold' => true,
            'name' => 'Times New Roman',
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER,
            'wrapText'   => true,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ]);

    // ============================================================
    // 🔹 6️⃣ Cari baris terakhir data
    // ============================================================
    $lastDataRow = $sheet->getHighestDataRow();
    if (!$lastDataRow || $lastDataRow < 18) {
        $lastDataRow = 18;
    }

    // ============================================================
    // 🔹 7️⃣ FORMAT ISI DATA
    // ============================================================
    $sheet->getStyle("A18:D{$lastDataRow}")->applyFromArray([
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical'   => Alignment::VERTICAL_CENTER,
            'wrapText'   => true,
        ],
        'font' => [
            'name' => 'Times New Roman',
            'size' => 11,
        ],
    ]);

    $sheet->getStyle("E18:G{$lastDataRow}")->applyFromArray([
        'alignment' => [
            'wrapText' => true,
            'vertical' => Alignment::VERTICAL_TOP,
            'horizontal' => Alignment::HORIZONTAL_LEFT,
        ],
        'font' => [
            'name' => 'Times New Roman',
            'size' => 11,
        ],
    ]);

    // ============================================================
    // 🔹 8️⃣ BORDER
    // ============================================================
    $sheet->getStyle("A16:G{$lastDataRow}")->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ]);

    $sheet->getStyle('A17:G17')->applyFromArray([
        'borders' => [
            'bottom' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM,
            ],
        ],
    ]);

    // ============================================================
    // 🔹 9️⃣ AUTO SIZE
    // ============================================================
    foreach (range('A', 'G') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    return [];
}


}
