<?php

namespace Database\Seeders;

use App\Models\KategoriAtk;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KategoriAtkseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['nama_kategori' => 'Pena'],
            ['nama_kategori' => 'Kertas'],
            ['nama_kategori' => 'Tinta'],
            ['nama_kategori' => 'Alat Tulis Lainnya'],
            ['nama_kategori' => 'Peralatan Kantor'],
            ['nama_kategori' => 'Buku Tulis'],
            ['nama_kategori' => 'Map dan Binder'],
            ['nama_kategori' => 'Alat Gambar'],
            ['nama_kategori' => 'Perekat'],
            ['nama_kategori' => 'Peralatan Kebersihan'],
        ];

        foreach ($categories as $category) {
            KategoriAtk::create($category);
        }
    
    }
}
