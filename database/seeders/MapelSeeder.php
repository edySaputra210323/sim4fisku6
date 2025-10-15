<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mapel = [
            ['nama_mapel' => 'Pendidikan Agama Islam dan Budi Pekerti', 'excel_column' => 'pai'],
            ['nama_mapel' => 'Pendidikan Pancasila dan Kewarganegaraan', 'excel_column' => 'pkn'],
            ['nama_mapel' => 'Bahasa Indonesia', 'excel_column' => 'bind'],
            ['nama_mapel' => 'Bahasa Inggris', 'excel_column' => 'bing'],
            ['nama_mapel' => 'Matematika', 'excel_column' => 'mtk'],
            ['nama_mapel' => 'Ilmu Pengetahuan Alam (IPA)', 'excel_column' => 'ipa'],
            ['nama_mapel' => 'Ilmu Pengetahuan Sosial (IPS)', 'excel_column' => 'ips'],
            ['nama_mapel' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan', 'excel_column' => 'pjok'],
            ['nama_mapel' => 'Informatika', 'excel_column' => 'info'],
            ['nama_mapel' => 'Bahasa Arab', 'excel_column' => 'barab'],
            ['nama_mapel' => 'Seni Rupa', 'excel_column' => 'srp'],
            ['nama_mapel' => 'Tajwid', 'excel_column' => 'tjw'],
            ['nama_mapel' => 'Upacara', 'excel_column' => 'upacara'],
            ['nama_mapel' => 'TWK', 'excel_column' => 'twk'],
            ['nama_mapel' => 'BPI', 'excel_column' => 'bpi'],
        ];

        DB::table('mapel')->insert(array_map(function ($m) {
            return array_merge($m, [
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }, $mapel));
    }
}
