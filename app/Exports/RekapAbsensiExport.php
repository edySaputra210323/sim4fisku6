<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\RiwayatKelas;
use App\Models\AbsensiHeader;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RekapAbsensiExport implements FromCollection, WithHeadings, WithStyles
{
    protected $start_date;
    protected $end_date;
    protected $guru_id;
    protected $kelas_id;
    protected $mapel_id;
    protected $cachedCollection;

    public function __construct($start_date, $end_date, $guru_id = null, $kelas_id = null, $mapel_id = null)
    {
        $this->start_date = \Carbon\Carbon::parse($start_date);
        $this->end_date = \Carbon\Carbon::parse($end_date);
        $this->guru_id = $guru_id;
        $this->kelas_id = $kelas_id;
        $this->mapel_id = $mapel_id;
    }

    public function collection(): Collection
    {
        if ($this->cachedCollection) {
            return $this->cachedCollection;
        }

        $headers = AbsensiHeader::with([
            'absensiDetails.riwayatKelas.dataSiswa',
        ])
            ->whereBetween('tanggal', [$this->start_date, $this->end_date])
            ->when($this->kelas_id, fn($q) => $q->where('kelas_id', $this->kelas_id))
            ->when($this->guru_id, fn($q) => $q->where('pegawai_id', $this->guru_id))
            ->when($this->mapel_id, fn($q) => $q->where('mapel_id', $this->mapel_id))
            ->orderBy('tanggal')
            ->get();

        $riwayat = RiwayatKelas::with('dataSiswa')
            ->where('kelas_id', $this->kelas_id)
            ->where('status_aktif', true)
            ->get();

        $rows = [];
        $no = 1;

        foreach ($riwayat as $item) {
            $siswa = $item->dataSiswa;
            if (!$siswa) continue;

            $row = [
                'No' => $no++,
                'NIS' => $siswa->nis,
                'Nama Siswa' => $siswa->nama_siswa,
            ];

            $tanggal = $this->start_date->copy();
            while ($tanggal->lte($this->end_date)) {
                $absen = null;

                foreach ($headers as $header) {
                    if ($header->tanggal->isSameDay($tanggal)) {
                        $absen = $header->absensiDetails
                            ->firstWhere('riwayat_kelas_id', $item->id);
                        break;
                    }
                }

                $status = match (strtolower($absen?->status ?? '')) {
                    'hadir' => 'H',
                    'sakit' => 'S',
                    'izin'  => 'I',
                    'alpa'  => 'A',
                    default => '-',
                };

                $row[$tanggal->format('j')] = $status;
                $tanggal->addDay();
            }

            $row['Sakit'] = collect($row)->filter(fn($v) => strtoupper($v) === 'S')->count();
            $row['Izin']  = collect($row)->filter(fn($v) => strtoupper($v) === 'I')->count();
            $row['Alpa']  = collect($row)->filter(fn($v) => strtoupper($v) === 'A')->count();
            $row['Jumlah S+I+A'] = $row['Sakit'] + $row['Izin'] + $row['Alpa'];
            $row['Keterangan'] = '';

            $rows[] = $row;
        }

        return $this->cachedCollection = collect($rows);
    }

    private function getPercentage($key): float
    {
        $collection = $this->collection();

        // Hanya ambil baris siswa (bukan baris rekap)
        $numericRows = $collection->filter(fn($row) => isset($row[$key]) && is_numeric($row[$key]));

        $jumlahSiswa = $numericRows->count();
        $jumlahHari = $this->start_date->diffInDays($this->end_date) + 1;

        $total = $numericRows->sum($key);

        if ($jumlahSiswa === 0 || $jumlahHari === 0) {
            return 0;
        }

        return round(($total / ($jumlahSiswa * $jumlahHari)) * 100, 2);
    }

    public function headings(): array
    {
        $dates = [];
        $tanggal = $this->start_date->copy();
        while ($tanggal->lte($this->end_date)) {
            $dates[] = $tanggal->format('j');
            $tanggal->addDay();
        }

        return array_merge(
            ['No', 'NIS', 'Nama Siswa', 'Hari/Tanggal'],
            $dates,
            ['Sakit', 'Izin', 'Alpa', 'Jumlah S+I+A', 'Keterangan']
        );
    }

    public function styles(Worksheet $sheet)
    {
        // Tambahkan ruang di atas
        $sheet->insertNewRowBefore(1, 14);

        // === LOGO ===
        $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo Sekolah');
        $drawing->setDescription('Logo SMPIT');
        $drawing->setPath(public_path('images/logoSMPIT.png'));
        $drawing->setHeight(100);
        $drawing->setCoordinates('B1');
        $drawing->setOffsetX(30);
        $drawing->setOffsetY(5);
        $drawing->setWorksheet($sheet);

        // === KOP SEKOLAH ===
        $sheet->mergeCells('E1:X1');
        $sheet->mergeCells('E2:X2');
        $sheet->mergeCells('E3:X3');
        $sheet->mergeCells('E4:X4');
        $sheet->mergeCells('E5:X5');

        $sheet->setCellValue('E1', 'AL-FITYAN SCHOOL KUBU RAYA');
        $sheet->setCellValue('E2', 'No. Dokumen : YYS-F-KUR-0306 / No. Revisi : 00 / Berlaku : 01 Juli 2024');
        $sheet->setCellValue('E3', 'DAFTAR HADIR PESERTA DIDIK');
        $sheet->setCellValue('E4', 'SEMESTER GANJIL SMPIT AL-FITYAN KUBU RAYA');
        $sheet->setCellValue('E5', 'TAHUN AJARAN 2024-2025');

        $sheet->getStyle('E1')->getFont()->setBold(true)->setSize(20);
        $sheet->getStyle('E3')->getFont()->setBold(true)->setSize(18);
        $sheet->getStyle('E4:E5')->getFont()->setBold(true)->setSize(14);

        $sheet->getStyle('E1:E5')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        // === INFORMASI KELAS & BULAN ===
        $sheet->mergeCells('A7:H7');
        $sheet->mergeCells('I7:P7');
        $kelas = \App\Models\Kelas::find($this->kelas_id)?->nama_kelas ?? '-';
        $bulan = $this->start_date->translatedFormat('F Y');
        $sheet->setCellValue('A7', 'Kelas : ' . $kelas);
        $sheet->setCellValue('I7', 'Bulan : ' . $bulan);
        $sheet->getStyle('A7:P7')->getFont()->setBold(true)->setSize(12);

        // === HEADER TABEL ===
        $sheet->getStyle('A14:AM15')->getFont()->setBold(true);
        $sheet->getStyle('A14:AM15')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);

        $sheet->mergeCells("A14:A15");
        $sheet->mergeCells("B14:B15");
        $sheet->mergeCells("C14:C15");
        $sheet->mergeCells("D14:AH14");
        $sheet->mergeCells("AI14:AI15");
        $sheet->mergeCells("AJ14:AJ15");
        $sheet->mergeCells("AK14:AK15");
        $sheet->mergeCells("AL14:AL15");
        $sheet->mergeCells("AM14:AM15");

        $sheet->setCellValue('A14', 'No');
        $sheet->setCellValue('B14', 'NIS');
        $sheet->setCellValue('C14', 'Nama Siswa');
        $sheet->setCellValue('D14', 'Hari / Tanggal');

        $colIndex = 4;
        for ($i = 1; $i <= 31; $i++) {
            $colLetter = Coordinate::stringFromColumnIndex($colIndex);
            $sheet->setCellValue($colLetter . '15', $i);
            $colIndex++;
        }

        $sheet->setCellValue('AI14', 'Sakit');
        $sheet->setCellValue('AJ14', 'Izin');
        $sheet->setCellValue('AK14', 'Alpa');
        $sheet->setCellValue('AL14', 'Jumlah S+I+A');
        $sheet->setCellValue('AM14', 'Keterangan');

        $sheet->getStyle("A14:AM15")->applyFromArray([
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ]);

        // Lebar kolom
        for ($i = 1; $i <= 39; $i++) {
            $colLetter = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($colLetter)->setWidth(4);
        }
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('AM')->setWidth(15);

        // === REKAP PERSENTASE ===
        $lastDataRow = $sheet->getHighestRow();
        $startRow = $lastDataRow + 2;

        // === BAGIAN TTD DI SEBELAH KIRI ===
        $tanggal = now()->translatedFormat('d F Y');

        // Baris awal TTD
        $ttdRow = $startRow;

        // Tanggal di atas kepala sekolah
        $sheet->mergeCells("F{$ttdRow}:M{$ttdRow}");
        $sheet->setCellValue("F{$ttdRow}", "Kubu Raya, {$tanggal}");
        $sheet->getStyle("F{$ttdRow}")
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        // Label jabatan
        $sheet->mergeCells("B" . ($ttdRow + 1) . ":D" . ($ttdRow + 1));
        $sheet->mergeCells("F" . ($ttdRow + 1) . ":K" . ($ttdRow + 1));
        $sheet->setCellValue("B" . ($ttdRow + 1), "Wali Kelas");
        $sheet->setCellValue("F" . ($ttdRow + 1), "Kepala Sekolah");

        $sheet->getStyle("B" . ($ttdRow + 1) . ":K" . ($ttdRow + 1))
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        // Ruang kosong tanda tangan
        $sheet->mergeCells("B" . ($ttdRow + 4) . ":D" . ($ttdRow + 4));
        $sheet->mergeCells("F" . ($ttdRow + 4) . ":K" . ($ttdRow + 4));
        $sheet->setCellValue("B" . ($ttdRow + 4), "(_____________________)");
        $sheet->setCellValue("F" . ($ttdRow + 4), "(_____________________)");

        $sheet->getStyle("B" . ($ttdRow + 4) . ":K" . ($ttdRow + 4))
            ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        // Merge kolom AC–AI untuk label persentase
        for ($i = 0; $i <= 5; $i++) {
            $rowNum = $startRow + $i;
            $sheet->mergeCells("AC{$rowNum}:AI{$rowNum}");
        }

    // Isi data persentase
    $sheet->setCellValue("AC{$startRow}", 'S (Sakit):');
    $sheet->setCellValue("AJ{$startRow}", $this->getPercentage('Sakit') . ' %');

    $sheet->setCellValue("AC" . ($startRow + 1), 'I (Izin):');
    $sheet->setCellValue("AJ" . ($startRow + 1), $this->getPercentage('Izin') . ' %');

    $sheet->setCellValue("AC" . ($startRow + 2), 'A (Alpa):');
    $sheet->setCellValue("AJ" . ($startRow + 2), $this->getPercentage('Alpa') . ' %');

    $sheet->setCellValue("AC" . ($startRow + 3), 'Jumlah S+I+A:');
    $sheet->setCellValue("AJ" . ($startRow + 3), $this->getPercentage('Jumlah S+I+A') . ' %');

    // Hitung persentase absen rata-rata bulan ini
    $collection = $this->collection();
    $jumlahSiswa = $collection->count();
    $jumlahHari = $this->start_date->diffInDays($this->end_date) + 1;
    $totalAbsen = $collection->sum('Jumlah S+I+A');

    $persenAbsen = $jumlahSiswa && $jumlahHari
        ? round(($totalAbsen / ($jumlahSiswa * $jumlahHari)) * 100, 2)
        : 0;

    $sheet->setCellValue("AC" . ($startRow + 5), '% Absen rata-rata bulan ini:');
    $sheet->setCellValue("AJ" . ($startRow + 5), $persenAbsen . ' %');

    // Gaya font & alignment
    $sheet->getStyle("AC{$startRow}:AJ" . ($startRow + 5))
        ->getFont()->setBold(true)->setSize(10)->setName('Times New Roman');

    $sheet->getStyle("AC{$startRow}:AI" . ($startRow + 5))
        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT)
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

    $sheet->getStyle("AJ{$startRow}:AJ" . ($startRow + 5))
        ->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        return [];
    }
}
