<?php

namespace App\Exports;

use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\JurnalGuru;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RekapJurnalKelasExport implements FromCollection, WithHeadings, WithStyles, WithMapping
{
    protected $startDate;
    protected $endDate;
    protected $kelasId;

    public function __construct($startDate, $endDate, $kelasId)
    {
        $this->startDate = $startDate;
        $this->endDate   = $endDate;
        $this->kelasId   = $kelasId;
    }

    public function collection()
    {
        return JurnalGuru::with(['guru', 'kelas', 'mapel', 'absensi.riwayatKelas.dataSiswa'])
            ->where('kelas_id', $this->kelasId)
            ->whereBetween('tanggal', [$this->startDate, $this->endDate])
            ->orderBy('tanggal')
            ->get();
    }

    # ============================================================
    # 1. HAPUS KOLOM TANGGAL PADA HEADER
    # ============================================================
    public function headings(): array
    {
        return [
            [
                'Jam Ke', 'Mapel', 'Nama Guru', 'Materi',
                'Kegiatan', 'Paraf Guru', 'Ketidakhadiran Peserta Didik', '', ''
            ],
            ['', '', '', '', '', '', 'Sakit', 'Izin', 'Alpa'],
        ];
    }

    # ============================================================
    # 2. HAPUS KOLOM TANGGAL PADA DATA
    # ============================================================
    public function map($record): array
    {
        $formatNama = fn($nama) => ucwords(strtolower($nama));

        $sakit = $record->absensi
            ->where('status', 'sakit')
            ->pluck('riwayatKelas.dataSiswa.nama_siswa')
            ->map($formatNama)
            ->implode("\n");

        $izin = $record->absensi
            ->where('status', 'izin')
            ->pluck('riwayatKelas.dataSiswa.nama_siswa')
            ->map($formatNama)
            ->implode("\n");

        $alpa = $record->absensi
            ->where('status', 'alpa')
            ->pluck('riwayatKelas.dataSiswa.nama_siswa')
            ->map($formatNama)
            ->implode("\n");

        $jamKe = $record->jam?->pluck('jam_ke')->sort()->join(' & ') ?? '-';

        return [
            $jamKe,
            $record->mapel?->nama_mapel,
            $record->guru?->nm_pegawai,
            $record->materi,
            $record->kegiatan,
            '',
            $sakit,
            $izin,
            $alpa,
        ];
    }

    # ============================================================
    # 3. PERBAIKAN MERGE, BORDER, DAN RANGE KOLOM A–I (BUKAN A–J)
    # ============================================================
    public function styles(Worksheet $sheet)
    {
        $first = $this->collection()->first();

        // sisipkan ruang 14 baris untuk kop
        $sheet->insertNewRowBefore(1, 14);

        // Tahun ajaran & semester
        $tahunAktif = TahunAjaran::where('status', 1)->first();
        $semesterAktif = Semester::where('status', 1)->first();

        $tahunAjaranText = $tahunAktif?->th_ajaran ?? '-';
        $semesterText    = strtoupper($semesterAktif?->nm_semester ?? '-');

        // Merge kop: A1 sampai I (karena tabel hanya sampai I)
        foreach (range(1, 7) as $row) {
            $sheet->mergeCells("A{$row}:I{$row}");
        }

        // Isi kop
        $sheet->setCellValue('A1', 'AL-FITYAN SCHOOL KUBU RAYA');
        $sheet->setCellValue('A2', 'No. Dokumen : YYS-F-KUR-0305 / Revisi : 00 / Berlaku : 01 Juli 2024');
        $sheet->setCellValue('A4', 'JURNAL KELAS');
        $sheet->setCellValue('A5', "SEMESTER {$semesterText} SMPIT AL-FITYAN KUBU RAYA");
        $sheet->setCellValue('A6', "TAHUN AJARAN {$tahunAjaranText}");

        $sheet->getStyle('A1:A6')->applyFromArray([
            'font' => ['bold' => true, 'name' => 'Times New Roman'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $sheet->getStyle('A4')->getFont()->setSize(18);

        // Logo
        $logoPath = public_path('images/logoSMPIT.png');
        if (file_exists($logoPath)) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setPath($logoPath);
            $drawing->setHeight(80);
            $drawing->setCoordinates('A3');
            $drawing->setWorksheet($sheet);
        }

        // Info kelas & tanggal
        $kelasNama = optional($first?->kelas)->nama_kelas;

        $start = Carbon::parse($this->startDate)->format('j/n/Y');
        $end   = Carbon::parse($this->endDate)->format('j/n/Y');
        $tanggal = ($this->startDate === $this->endDate)
            ? $start
            : "{$start} - {$end}";
        $sheet->setCellValue('A10', "Kelas : {$kelasNama}");
        $sheet->setCellValue('A11', "Tanggal : {$tanggal}");
        // HEADER TABEL BARU TANPA TANGGAL
        $sheet->mergeCells('A12:A13'); // Jam Ke
        $sheet->mergeCells('B12:B13'); // Mapel
        $sheet->mergeCells('C12:C13'); // Nama Guru
        $sheet->mergeCells('D12:D13'); // Materi
        $sheet->mergeCells('E12:E13'); // Kegiatan
        $sheet->mergeCells('F12:F13'); // Paraf Guru
        // Kolom ketidakhadiran
        $sheet->mergeCells('G12:I12'); // Sakit | Izin | Alpa
        $sheet->getStyle('A12:I13')->applyFromArray([ 
            'font' => ['bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ]);

        // border data
        $last = $sheet->getHighestDataRow();
        if ($last < 15) $last = 15;
        $sheet->getStyle("A14:I{$last}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => 'thin']],
        ]);
        // auto width A–I
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        return [];
    }
}
