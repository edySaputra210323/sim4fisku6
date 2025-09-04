<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('atk', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->nullable(); // kode barang, misal A001
            $table->string('nama_atk');
            $table->foreignId('kategori_atk_id')->nullable()->constrained('kategori_atk')->cascadeOnDelete();
            $table->string('satuan'); // pcs, rim, box, dll
            $table->string('keterangan')->nullable();
            $table->integer('stock_awal')->default(0); // stok awal saat sistem mulai
            $table->integer('stock')->default(0);      // stok berjalan
            $table->string('foto_atk')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atk');
    }
};
