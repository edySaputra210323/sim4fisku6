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
        ];

        // Masukkan data ke tabel status_siswa
        DB::table('status_siswa')->insert($statusSiswa);
    }
}
