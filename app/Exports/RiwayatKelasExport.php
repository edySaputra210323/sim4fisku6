<?php

namespace App\Exports;

use App\Models\RiwayatKelas;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class RiwayatKelasExport implements FromQuery, WithHeadings, WithMapping, WithEvents
{
    protected $kelasId;
    protected $tahunAjaranId;
    protected $semesterId;
    protected $guru;

    public function __construct($kelasId = null, $tahunAjaranId, $semesterId, $guru = null)
    {
        $this->kelasId = $kelasId;
        $this->tahunAjaranId = $tahunAjaranId;
        $this->semesterId = $semesterId;
        $this->guru = $guru;
    }

    public function query()
    {
        $query = RiwayatKelas::query()
            ->where('tahun_ajaran_id', $this->tahunAjaranId)
            ->where('semester_id', $this->semesterId)
            ->join('data_siswa', 'riwayat_kelas.data_siswa_id', '=', 'data_siswa.id')
            ->join('status_siswa', 'data_siswa.status_id', '=', 'status_siswa.id')
            ->where('status_siswa.status', 'aktif')
            ->with(['dataSiswa', 'kelas', 'guru', 'tahunAjaran', 'semester']);

        if ($this->kelasId) {
            $query->where('riwayat_kelas.kelas_id', $this->kelasId);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            [
                'No',
                'Nama Siswa',
                'NIS',
                'NISN',
                'JK',
                'TTL',
                'Jmlh Saudara',
                'Data Ayah', '', '', '', '',
                'Data Ibu', '', '', '', '',
                'Alamat',
            ],
            [
                'No',
                'Nama Siswa',
                'NIS',
                'NISN',
                'JK',
                'TTL',
                'Jmlh Saudara',
                'Nama',
                'No HP',
                'Pekerjaan',
                'Pendidikan',
                'Penghasilan',
                'Nama',
                'No HP',
                'Pekerjaan',
                'Pendidikan',
                'Penghasilan',
                'Alamat',
            ]
        ];
    }

    public function map($riwayatKelas): array
    {
        static $row = 0;
        $row++;

        return [
            $row,
            $riwayatKelas->dataSiswa->nama_siswa ?? '-',
            $riwayatKelas->dataSiswa->nis ?? '-',
            $riwayatKelas->dataSiswa->nisn ?? '-',
            $riwayatKelas->dataSiswa->jenis_kelamin ?? '-',
            $riwayatKelas->dataSiswa->tempat_tanggal_lahir ?? '-',
            $riwayatKelas->dataSiswa->status_jumlah_saudara ?? '-',
            $riwayatKelas->dataSiswa->nm_ayah ?? '-',
            $riwayatKelas->dataSiswa->no_hp_ayah ?? '-',
            $riwayatKelas->dataSiswa->pekerjaanAyah->kode_pekerjaan ?? '-',
            $riwayatKelas->dataSiswa->pendidikanAyah->kode_jenjang_pendidikan ?? '-',
            $riwayatKelas->dataSiswa->penghasilanAyah->kode_penghasilan ?? '-',
            $riwayatKelas->dataSiswa->nm_ibu ?? '-',
            $riwayatKelas->dataSiswa->no_hp_ibu ?? '-',
            $riwayatKelas->dataSiswa->pekerjaanIbu->kode_pekerjaan ?? '-',
            $riwayatKelas->dataSiswa->pendidikanIbu->kode_jenjang_pendidikan ?? '-',
            $riwayatKelas->dataSiswa->penghasilanIbu->kode_penghasilan ?? '-',
            $riwayatKelas->dataSiswa->alamat_lengkap ?? '-',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Geser header tabel ke bawah (mulai row 15)
                $event->sheet->insertNewRowBefore(1, 14);

                /**
                 * === Kop Sekolah ===
                 */
                // Logo
                $drawing = new Drawing();
                $drawing->setPath(public_path('images/logoSMPIT.png')); 
                $drawing->setHeight(180);
                $drawing->setCoordinates('B3'); 
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawing->setWorksheet($sheet);

                $riwayatPertama = $this->query()->first();

                $namaKelas     = $riwayatPertama->kelas->nama_kelas ?? '-';
                $namaGuru      = $riwayatPertama->guru->nm_pegawai ?? '-';
                $namaTahun     = $riwayatPertama->tahunAjaran->th_ajaran ?? '-';
                $namaSemester  = $riwayatPertama->semester->nm_semester ?? '-';

                // Judul besar
                $sheet->mergeCells('C2:R2');
                $sheet->setCellValue('C2', 'DAFTAR NAMA SISWA (MODEL 8355)');
                $sheet->mergeCells('C3:R3');
                $sheet->setCellValue('C3', 'TAHUN AJARAN ' . $namaTahun . ' SEMESTER ' . $namaSemester);

                $sheet->getStyle('C2:C3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    ],
                ]);

                // Identitas sekolah
                $sheet->setCellValue('C5', 'NAMA SEKOLAH');
                $sheet->setCellValue('D5', ': SMPIT AL-FITYAN');

                $sheet->setCellValue('C6', 'ALAMAT SEKOLAH');
                $sheet->setCellValue('D6', ': JL. RAYA SUNGAI KAKAP, RT.03/RW.01, DESA PAL 9');

                $sheet->setCellValue('C7', 'KECAMATAN');
                $sheet->setCellValue('D7', ': SUNGAI KAKAP');

                $sheet->setCellValue('C8', 'KABUPATEN');
                $sheet->setCellValue('D8', ': KUBU RAYA');

                $sheet->setCellValue('C9', 'PROPINSI');
                $sheet->setCellValue('D9', ': KALIMANTAN BARAT');

                $sheet->setCellValue('E5', 'TAHUN AJARAN');
                $sheet->setCellValue('F5', ': ' . $namaTahun);

                $sheet->setCellValue('E6', 'SEMESTER');
                $sheet->setCellValue('F6', ': ' . $namaSemester);

                $sheet->setCellValue('E7', 'KELAS');
                $sheet->setCellValue('F7', ': ' . $namaKelas);

                $sheet->setCellValue('E8', 'WALI KELAS');
                $sheet->setCellValue('F8', ': ' . $namaGuru);

                $sheet->setCellValue('E9', 'KODE POS');
                $sheet->setCellValue('F9', ': 78381');

                $sheet->getStyle('C5:C14')->applyFromArray([
                    'font' => ['bold' => true],
                ]);

                $sheet->getRowDimension(2)->setRowHeight(25);
                $sheet->getRowDimension(3)->setRowHeight(20);

                /**
                 * === Header Tabel Siswa (mulai row 15) ===
                 */
                // Merge kolom
                $mergeCols = ['A','B','C','D','E','F','G','R']; 
                foreach ($mergeCols as $col) {
                    $event->sheet->mergeCells("{$col}15:{$col}16");
                }
                $event->sheet->mergeCells('H15:L15');
                $event->sheet->mergeCells('M15:Q15');

                // Styling header
                $event->sheet->getStyle('A15:R16')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                // Background header
                $event->sheet->getStyle('H15:L15')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'BDD7EE'],
                    ],
                ]);
                $event->sheet->getStyle('M15:Q15')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F8CBAD'],
                    ],
                ]);
                $event->sheet->getStyle('A16:R16')->applyFromArray([
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D9D9D9'],
                    ],
                ]);

                // Row height
                $sheet->getRowDimension(15)->setRowHeight(30);
                $sheet->getRowDimension(16)->setRowHeight(25);
                $sheet->getDefaultRowDimension()->setRowHeight(20);

                // Column width
                $sheet->getColumnDimension('A')->setWidth(5);
                $sheet->getColumnDimension('B')->setWidth(25);
                $sheet->getColumnDimension('H')->setWidth(20);
                $sheet->getColumnDimension('M')->setWidth(20);

                foreach (range('C','R') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }
            },
        ];


    }
}
