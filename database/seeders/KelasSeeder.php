<?php

namespace Database\Seeders;

use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KelasSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['nama_kelas' => 'VIIA'],
            ['nama_kelas' => 'VIIB'],
            ['nama_kelas' => 'VIIC'],
            ['nama_kelas' => 'VIID'],
            ['nama_kelas' => 'VIIIA'],
            ['nama_kelas' => 'VIIIB'],
            ['nama_kelas' => 'VIIIC'],
            ['nama_kelas' => 'VIIID'],
            ['nama_kelas' => 'IXA'],
            ['nama_kelas' => 'IXB'],
            ['nama_kelas' => 'IXC'],
            ['nama_kelas' => 'IXD'],
        ];

        foreach ($data as $item) {
            DB::table('kelas')->insert([
                'nama_kelas' => $item['nama_kelas'],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
