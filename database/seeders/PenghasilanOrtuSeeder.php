<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PenghasilanOrtuSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode' => 'A', 'penghasilan' => '< Rp. 500.000,-'],
            ['kode' => 'B', 'penghasilan' => 'Rp. 500.000,- s.d Rp. 999.999,-'],
            ['kode' => 'C', 'penghasilan' => 'Rp. 1.000.000,- s.d Rp. 1.999.999,-'],
            ['kode' => 'D', 'penghasilan' => 'Rp. 2.000.000,- s.d Rp. 4.999.999,-'],
            ['kode' => 'E', 'penghasilan' => 'Rp. 5.000.000 s.d Rp. 20.000.000'],
            ['kode' => 'F', 'penghasilan' => '> Rp. 20.000.000'],
            ['kode' => 'G', 'penghasilan' => 'Tidak Berpenghasilan'],
        ];

        foreach ($data as $item) {
            DB::table('penghasilan_ortu')->insert([
                'kode_penghasilan' => $item['kode'],
                'penghasilan' => $item['penghasilan'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
