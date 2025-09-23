<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class statusPegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'nama_status' => 'Aktif',
                'kode' => 'aktif',
                'warna' => 'success', // hijau
                'is_active' => true,
                'keterangan' => 'Sedang bekerja normal.',
            ],
            [
                'nama_status' => 'Nonaktif',
                'kode' => 'nonaktif',
                'warna' => 'gray', // abu-abu
                'is_active' => false,
                'keterangan' => 'Sementara tidak aktif (misalnya menunggu keputusan yayasan).',
            ],
            [
                'nama_status' => 'Resign / Mengundurkan Diri',
                'kode' => 'resign',
                'warna' => 'warning', // kuning
                'is_active' => false,
                'keterangan' => 'Pegawai keluar atas permintaan sendiri.',
            ],
            [
                'nama_status' => 'PHK',
                'kode' => 'phk',
                'warna' => 'danger', // merah
                'is_active' => false,
                'keterangan' => 'Diberhentikan oleh yayasan.',
            ],
            [
                'nama_status' => 'Mutasi',
                'kode' => 'mutasi',
                'warna' => 'info', // biru
                'is_active' => true,
                'keterangan' => 'Dipindahkan ke unit/lembaga lain di bawah yayasan.',
            ],
            [
                'nama_status' => 'Cuti',
                'kode' => 'cuti',
                'warna' => 'purple', // ungu (pakai extension tailwind filament)
                'is_active' => true,
                'keterangan' => 'Izin cuti resmi (misalnya cuti hamil, cuti sakit, dll.).',
            ],
            [
                'nama_status' => 'Tugas Belajar',
                'kode' => 'tugas-belajar',
                'warna' => 'info', // cyan / biru muda
                'is_active' => true,
                'keterangan' => 'Sedang diberi izin untuk melanjutkan studi/pendidikan.',
            ],
            [
                'nama_status' => 'Pensiun',
                'kode' => 'pensiun',
                'warna' => 'warning', // oranye
                'is_active' => false,
                'keterangan' => 'Sudah tidak bekerja karena masa pensiun.',
            ],
            [
                'nama_status' => 'Meninggal Dunia',
                'kode' => 'meninggal',
                'warna' => 'secondary', // abu gelap
                'is_active' => false,
                'keterangan' => 'Status akhir karena pegawai meninggal dunia.',
            ],
            [
                'nama_status' => 'Calon Pegawai / Probation',
                'kode' => 'probation',
                'warna' => 'warning', // emas/kuning
                'is_active' => true,
                'keterangan' => 'Masa percobaan sebelum diangkat menjadi pegawai tetap.',
            ],
        ];

        DB::table('status_pegawai')->insert($data);
    }
}
