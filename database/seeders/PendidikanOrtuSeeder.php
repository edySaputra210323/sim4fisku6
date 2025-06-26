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
            ['kode' => 'A', 'pendidikan' => 'Tidak Tamat SD/MI/Paket A'],
            ['kode' => 'B', 'pendidikan' => 'SD/MI/Paket A'],
            ['kode' => 'C', 'pendidikan' => 'SMP/MTs/Paket B'],
            ['kode' => 'D', 'pendidikan' => 'SMA/MA/Paket C'],
            ['kode' => 'E', 'pendidikan' => 'Diploma 1 & 2'],
            ['kode' => 'F', 'pendidikan' => 'Diploma 3 & 4'],
            ['kode' => 'G', 'pendidikan' => 'S.1 (Sarjana)'],
            ['kode' => 'H', 'pendidikan' => 'S.2 (Magister)'],
            ['kode' => 'I', 'pendidikan' => 'S.3 (Doktor)'],
            ['kode' => 'J', 'pendidikan' => 'Lainnya'],
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
