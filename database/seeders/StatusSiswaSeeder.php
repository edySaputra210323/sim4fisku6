<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StatusSiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Data untuk tabel status_siswa
        $statusSiswa = [
            [
                'status' => 'Aktif',
                'deskripsi' => 'Siswa aktif di sekolah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status' => 'Lulus',
                'deskripsi' => 'Siswa telah lulus',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status' => 'Pindah',
                'deskripsi' => 'Siswa pindah ke sekolah lain',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status' => 'Cuti',
                'deskripsi' => 'Siswa yang mengambil jeda sementara dari studi mereka dengan izin resmi dari sekolah. Mereka berencana untuk kembali melanjutkan pendidikan setelah periode cuti berakhir.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'status' => 'Drop Out',
                'deskripsi' => 'siswa yang diberhentikan dari sekolah dikarenakan melanggar aturan sekolah atau pelanggaran     kasus besar',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Masukkan data ke tabel status_siswa
        DB::table('status_siswa')->insert($statusSiswa);
    }
}
