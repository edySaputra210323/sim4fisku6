<?php

namespace App\Exports;

use App\Models\AbsensiHeader;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Illuminate\Support\Carbon;

class RekapAbsensiExport implements FromCollection, WithHeadings, WithStyles
{
    protected $start_date, $end_date, $guru_id, $kelas_id, $mapel_id, $tahun_ajaran_id, $semester_id;

    protected $kelas_nama = '-';
    protected $bulan_nama = '-';
    protected $semester_nama = '-';
    protected $tahun_ajaran_nama = '-';

    public function __construct($start_date, $end_date, $guru_id = null, $kelas_id = null, $mapel_id = null, $tahun_ajaran_id = null, $semester_id = null)
    {
        $this->start_date = Carbon::parse($start_date);
        $this->end_date = Carbon::parse($end_date);
        $this->guru_id = $guru_id;
        $this->kelas_id = $kelas_id;
        $this->mapel_id = $mapel_id;
        $this->tahun_ajaran_id = $tahun_ajaran_id;
        $this->semester_id = $semester_id;

        // Nama bulan otomatis
        $this->bulan_nama = $this->start_date->translatedFormat('F Y'); // contoh: Oktober 2025
    }

    public function collection()
    {
        $data = AbsensiHeader::with(['guru', 'kelas', 'semester', 'tahunAjaran'])
            ->when($this->guru_id, fn($q) => $q->where('pegawai_id', $this->guru_id))
            ->when($this->kelas_id, fn($q) => $q->where('kelas_id', $this->kelas_id))
            ->when($this->mapel_id, fn($q) => $q->where('mapel_id', $this->mapel_id))
            ->when($this->tahun_ajaran_id, fn($q) => $q->where('tahun_ajaran_id', $this->tahun_ajaran_id))
            ->when($this->semester_id, fn($q) => $q->where('semester_id', $this->semester_id))
            ->whereBetween('tanggal', [$this->start_date, $this->end_date])
            ->get();

        // Ambil data pertama untuk info tambahan
        if ($first = $data->first()) {
            $this->kelas_nama = $first->kelas?->nama_kelas ?? '-';
            $this->semester_nama = strtoupper($first->semester?->nm_semester ?? '-');
            $this->tahun_ajaran_nama = $first->tahunAjaran?->th_ajaran ?? '-';
        }

        // Kalau user pilih filter semester / tahun ajaran secara eksplisit, gunakan itu
        if ($this->semester_id) {
            $this->semester_nama = strtoupper(Semester::find($this->semester_id)?->nm_semester ?? $this->semester_nama);
        }
        if ($this->tahun_ajaran_id) {
            $this->tahun_ajaran_nama = TahunAjaran::find($this->tahun_ajaran_id)?->th_ajaran ?? $this->tahun_ajaran_nama;
        }

        return $data->map(function ($row) {
            return [
                'Tanggal' => $row->tanggal->format('d-m-Y'),
                'Guru' => $row->guru?->nm_pegawai,
                'Kelas' => $row->kelas?->nama_kelas,
                'Semester' => $row->semester?->nm_semester,
                'Tahun Ajaran' => $row->tahunAjaran?->th_ajaran,
                'Jumlah Hadir' => $row->jumlah_hadir,
                'Jumlah Tidak Hadir' => $row->jumlah_tidak_hadir,
            ];
        });
    }

    public function headings(): array
    {
        return ['Tanggal', 'Guru', 'Kelas', 'Semester', 'Tahun Ajaran', 'Jumlah Hadir', 'Jumlah Tidak Hadir'];
    }

    public function styles(Worksheet $sheet)
    {
        // Tambah ruang kosong untuk kop
        $sheet->insertNewRowBefore(1, 12);

        // Merge area kop
        $sheet->mergeCells('B1:H1');
        $sheet->mergeCells('B2:H2');
        $sheet->mergeCells('B3:H3');
        $sheet->mergeCells('B4:H4');
        $sheet->mergeCells('B5:H5');

        // === Isi kop lembaga ===
        $sheet->setCellValue('B1', 'AL-FITYAN SCHOOL KUBU RAYA');
        $sheet->setCellValue('B2', 'No. Dokumen : YYS-F-KUR-0306 / No. Revisi : 00 / Berlaku : 01 Juli 2024');
        $sheet->setCellValue('B3', 'DAFTAR HADIR PESERTA DIDIK');
        $sheet->setCellValue('B4', 'SEMESTER ' . $this->semester_nama . ' SMPIT AL-FITYAN KUBU RAYA');
        $sheet->setCellValue('B5', 'TAHUN AJARAN ' . $this->tahun_ajaran_nama);

        // === Styling kop ===
        $sheet->getStyle('B1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 20, 'name' => 'Times New Roman'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('B2')->applyFromArray([
            'font' => ['size' => 12, 'name' => 'Times New Roman'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('B3')->applyFromArray([
            'font' => ['bold' => true, 'size' => 18, 'name' => 'Times New Roman'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('B4')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'name' => 'Times New Roman'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('B5')->applyFromArray([
            'font' => ['size' => 16, 'name' => 'Times New Roman'],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        // === Logo Sekolah ===
        $logoPath = public_path('images/logoSMPIT.png');
        if (file_exists($logoPath)) {
            $drawing = new Drawing();
            $drawing->setName('Logo Sekolah');
            $drawing->setDescription('Logo SMPIT Al-Fityan');
            $drawing->setPath($logoPath);
            $drawing->setCoordinates('A1');
            $drawing->setHeight(90);
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
        }

        // === Baris kelas & bulan ===
        $sheet->mergeCells('A7:C7');
        $sheet->mergeCells('E7:H7');
        $sheet->setCellValue('A7', 'Kelas : ' . $this->kelas_nama);
        $sheet->setCellValue('E7', 'Bulan : ' . $this->bulan_nama);

        $sheet->getStyle('A7:H7')->applyFromArray([
            'font' => ['italic' => true, 'size' => 11, 'name' => 'Times New Roman'],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // === Garis pembatas bawah kop ===
        $sheet->getStyle('A8:H8')->applyFromArray([
            'borders' => [
                'bottom' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM],
            ],
        ]);

        // === Format tabel ===
        $lastRow = $sheet->getHighestDataRow();
        $lastCol = $sheet->getHighestDataColumn();

        $sheet->getStyle("A9:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
            ],
            'alignment' => [
                'wrapText' => true,
                'vertical' => Alignment::VERTICAL_CENTER,
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
            'font' => ['name' => 'Times New Roman', 'size' => 11],
        ]);

        // Lebar kolom otomatis
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Atur tinggi baris kop
        foreach ([1,2,3,4,5,7] as $row) {
            $sheet->getRowDimension($row)->setRowHeight(25);
        }

        return [];
    }
}
