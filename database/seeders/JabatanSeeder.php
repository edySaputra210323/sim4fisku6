<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatan = [
            ['nm_jabatan' => 'Guru'],
            ['nm_jabatan' => 'Kepala Sekolah'],
            ['nm_jabatan' => 'Staf TU'],
            ['nm_jabatan' => 'Wakil Kepala Sekolah Bagian Kesiswaan'],
            ['nm_jabatan' => 'Wakil Kepala Sekolah Bagian Kurikulum'],
            ['nm_jabatan' => 'Bendahara'],
        ];

        Jabatan::insert($jabatan);
    }
}
