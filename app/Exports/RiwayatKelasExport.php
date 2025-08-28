<?php

namespace App\Exports;

use App\Models\RiwayatKelas;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

// class RiwayatKelasExport implements FromCollection
class RiwayatKelasExport implements FromQuery, WithHeadings, WithMapping
{
    protected $kelasId;
    protected $tahunAjaranId;
    protected $semesterId;

    public function __construct($kelasId = null, $tahunAjaranId, $semesterId)
    {
        $this->kelasId = $kelasId;
        $this->tahunAjaranId = $tahunAjaranId;
        $this->semesterId = $semesterId;
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
            'No',
            'Nama Siswa',
            'NIS',
            'NISN',
            'Jenis Kelamin',
            'Tempat Tanggal Lahir',
            'Jumlah Saudara',
            'Nama Ayah',
            'No HP Ayah',
            'Pekerjaan Ayah',
            'Pendidikan Ayah',
            'Penghasilan Ayah',
            'Nama Ibu',
            'No HP Ibu',
            'Pekerjaan Ibu',
            'Pendidikan Ibu',
            'Penghasilan Ibu',
            'Alamat',
            'Kelas',
            'Wali Kelas',
            'Tahun Ajaran',
            'Semester',
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
            $riwayatKelas->dataSiswa->pekerjaanAyah->nama_pekerjaan ?? '-',
            $riwayatKelas->dataSiswa->pendidikanAyah->jenjang_pendidikan ?? '-',
            $riwayatKelas->dataSiswa->penghasilanAyah->penghasilan ?? '-',
            $riwayatKelas->dataSiswa->nm_ibu ?? '-',
            $riwayatKelas->dataSiswa->no_hp_ibu ?? '-',
            $riwayatKelas->dataSiswa->pekerjaanIbu->nama_pekerjaan ?? '-',
            $riwayatKelas->dataSiswa->pendidikanIbu->jenjang_pendidikan ?? '-',
            $riwayatKelas->dataSiswa->penghasilanIbu->penghasilan ?? '-',
            $riwayatKelas->dataSiswa->alamat_lengkap ?? '-',
            $riwayatKelas->kelas->nama_kelas ?? '-',
            $riwayatKelas->guru->nm_pegawai ?? '-',
            $riwayatKelas->tahunAjaran->th_ajaran ?? '-',
            $riwayatKelas->semester->nm_semester ?? '-',
        ];
    }
}
