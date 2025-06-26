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
            ['kode' => 'A', 'kendaraan' => 'Jalan Kaki'],
            ['kode' => 'B', 'kendaraan' => 'Perahu'],
            ['kode' => 'C', 'kendaraan' => 'Sepeda'],
            ['kode' => 'D', 'kendaraan' => 'Motor'],
            ['kode' => 'E', 'kendaraan' => 'Mobil'],
            ['kode' => 'F', 'kendaraan' => 'Antar-Jemput'],
            ['kode' => 'G', 'kendaraan' => 'Angkutan Umum'],
            ['kode' => 'H', 'kendaraan' => 'Lainnya'],
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
