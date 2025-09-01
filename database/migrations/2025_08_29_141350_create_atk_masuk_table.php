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
        Schema::create('atk_masuk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atk_id')->constrained('atk')->onDelete('cascade');
            $table->integer('qty')->unsigned()->default(0);
            $table->decimal('harga_satuan', 10, 2)->notNullable(); // Harga satuan, misalnya Rp 5000.00
            $table->decimal('total_harga', 10, 2)->notNullable(); // Total bayar, misalnya Rp 50000.00
            $table->date('tanggal');
            $table->string('nota')->nullable();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajaran')->nullOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained('semester')->nullOnDelete();
            $table->foreignId('ditambah_oleh_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atk_masuk');
    }
};
