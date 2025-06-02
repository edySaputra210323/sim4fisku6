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
            ['nm_unit' => 'Taman Kanak-Kanak Islam Terpadu', 'kode_unit' => 'TKIT'],
            ['nm_unit' => 'Sekolah Dasar Islam Terpadu', 'kode_unit' => 'SDIT'],
            ['nm_unit' => 'Sekolah Menengah Pertama Islam Terpadu', 'kode_unit' => 'SMPIT'],
            ['nm_unit' => 'Sekolah Menengah Atas Islam Terpadu', 'kode_unit' => 'SMAIT'],
            ['nm_unit' => 'Yayasan', 'kode_unit' => 'YYS'],
        ];

        Unit::insert($units);
    }
}
