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
            ['kode' => 'A', 'jarak' => '0 S.D. 1 KM'],
            ['kode' => 'B', 'jarak' => '1 S.D. 3 KM'],
            ['kode' => 'C', 'jarak' => '3 S.D. 5 KM'],
            ['kode' => 'D', 'jarak' => '5 S.D. 10 KM'],
            ['kode' => 'E', 'jarak' => 'LEBIH DARI 10 KM']
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
