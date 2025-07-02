<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TransportSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode' => 'A', 'kendaraan' => 'JALAN KAKI'],
            ['kode' => 'B', 'kendaraan' => 'PERAHU'],
            ['kode' => 'C', 'kendaraan' => 'SEPEDA'],
            ['kode' => 'D', 'kendaraan' => 'MOTOR'],
            ['kode' => 'E', 'kendaraan' => 'MOBIL'],
            ['kode' => 'F', 'kendaraan' => 'ANTAR-JEMPUT'],
            ['kode' => 'G', 'kendaraan' => 'ANGKUTAN UMUM'],
            ['kode' => 'H', 'kendaraan' => 'LAINNYA'],
        ];

        foreach ($data as $item) {
            DB::table('transport')->insert([
                'kode_transport' => $item['kode'],
                'nama_transport' => $item['kendaraan'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
