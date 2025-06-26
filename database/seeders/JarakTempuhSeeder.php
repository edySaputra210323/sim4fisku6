<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class JarakTempuhSeeder extends Seeder
{
   public function run(): void
    {
        $data = [
            ['kode' => 'A', 'jarak' => '0 s.d. 1 Km'],
            ['kode' => 'B', 'jarak' => '1 s.d. 3 Km'],
            ['kode' => 'C', 'jarak' => '3 s.d. 5 Km'],
            ['kode' => 'D', 'jarak' => '5 s.d. 10 Km'],
            ['kode' => 'E', 'jarak' => 'Lebih dari 10 Km']
        ];

        foreach ($data as $item) {
            DB::table('jarak_tempuh')->insert([
                'kode_jarak_tempuh' => $item['kode'],
                'nama_jarak_tempuh' => $item['jarak'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
