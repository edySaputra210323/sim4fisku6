<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['nm_unit' => 'TKIT', 'kode_unit' => '01', 'deskripsi' => 'Taman Kanak-Kanak Islam Terpadu'],
            ['nm_unit' => 'SDIT', 'kode_unit' => '02', 'deskripsi' => 'Sekolah Dasar Islam Terpadu'],
            ['nm_unit' => 'SMPIT', 'kode_unit' => '03', 'deskripsi' => 'Sekolah Menengah Pertama Islam Terpadu'],
            ['nm_unit' => 'SMAIT', 'kode_unit' => '04', 'deskripsi' => 'Sekolah Menengah Atas Islam Terpadu'],
            ['nm_unit' => 'Yayasan', 'kode_unit' => '05', 'deskripsi' => 'Yayasan Al-Fityan Kubu Raya'],
        ];

        Unit::insert($units);
    }
}
