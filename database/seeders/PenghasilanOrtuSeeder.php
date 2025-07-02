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
            ['kode' => 'A', 'penghasilan' => '< RP. 500.000,-'],
            ['kode' => 'B', 'penghasilan' => 'RP. 500.000,- S.D RP. 999.999,-'],
            ['kode' => 'C', 'penghasilan' => 'RP. 1.000.000,- S.D RP. 1.999.999,-'],
            ['kode' => 'D', 'penghasilan' => 'RP. 2.000.000,- S.D RP. 4.999.999,-'],
            ['kode' => 'E', 'penghasilan' => 'RP. 5.000.000 S.D RP. 20.000.000'],
            ['kode' => 'F', 'penghasilan' => '> RP. 20.000.000'],
            ['kode' => 'G', 'penghasilan' => 'TIDAK BERPENGHASILAN'],
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
