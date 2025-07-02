<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PendidikanOrtuSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['kode' => 'A', 'pendidikan' => 'TIDAK TAMAT SD/MI/PAKET A'],
            ['kode' => 'B', 'pendidikan' => 'SD/MI/PAKET A'],
            ['kode' => 'C', 'pendidikan' => 'SMP/MTS/PAKET B'],
            ['kode' => 'D', 'pendidikan' => 'SMA/MA/PAKET C'],
            ['kode' => 'E', 'pendidikan' => 'DIPLOMA 1 & 2'],
            ['kode' => 'F', 'pendidikan' => 'DIPLOMA 3 & 4'],
            ['kode' => 'G', 'pendidikan' => 'S.1 (SARJANA)'],
            ['kode' => 'H', 'pendidikan' => 'S.2 (MAGISTER)'],
            ['kode' => 'I', 'pendidikan' => 'S.3 (DOKTOR)'],
            ['kode' => 'J', 'pendidikan' => 'LAINNYA'],
        ];

        foreach ($data as $item) {
            DB::table('pendidikan_ortu')->insert([
                'kode_jenjang_pendidikan' => $item['kode'],
                'jenjang_pendidikan' => $item['pendidikan'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
