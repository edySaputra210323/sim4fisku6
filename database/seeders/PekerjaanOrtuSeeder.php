<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PekerjaanOrtuSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode' => 'A', 'pekerjaan' => 'PNS'],
            ['kode' => 'B', 'pekerjaan' => 'TNI/Polri'],
            ['kode' => 'C', 'pekerjaan' => 'Guru/Dosen'],
            ['kode' => 'D', 'pekerjaan' => 'Dokter'],
            ['kode' => 'E', 'pekerjaan' => 'Politikus'],
            ['kode' => 'F', 'pekerjaan' => 'Pegawai Swasta'],
            ['kode' => 'G', 'pekerjaan' => 'Pedagang/Wiraswasta'],
            ['kode' => 'H', 'pekerjaan' => 'Petani/Peternak'],
            ['kode' => 'I', 'pekerjaan' => 'Seniman'],
            ['kode' => 'J', 'pekerjaan' => 'Buruh'],
            ['kode' => 'K', 'pekerjaan' => 'Dirumah'],
        ];

        foreach ($data as $item) {
            DB::table('pekerjaan_ortu')->insert([
                'kode_pekerjaan' => $item['kode'],
                'nama_pekerjaan' => $item['pekerjaan'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
