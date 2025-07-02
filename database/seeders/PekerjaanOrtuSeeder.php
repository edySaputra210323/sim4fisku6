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
            ['kode' => 'B', 'pekerjaan' => 'TNI/POLRI'],
            ['kode' => 'C', 'pekerjaan' => 'GURU/DOSEN'],
            ['kode' => 'D', 'pekerjaan' => 'DOKTER'],
            ['kode' => 'E', 'pekerjaan' => 'POLITIKUS'],
            ['kode' => 'F', 'pekerjaan' => 'PEGAWAI SWASTA'],
            ['kode' => 'G', 'pekerjaan' => 'PEDAGANG/WIRASWASTA'],
            ['kode' => 'H', 'pekerjaan' => 'PETANI/PETERNAK'],
            ['kode' => 'I', 'pekerjaan' => 'SENIMAN'],
            ['kode' => 'J', 'pekerjaan' => 'BURUH'],
            ['kode' => 'K', 'pekerjaan' => 'DIRUMAH'],
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
