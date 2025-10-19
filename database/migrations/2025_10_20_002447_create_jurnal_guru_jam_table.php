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
        Schema::create('jurnal_guru_jam', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jurnal_guru_id')->constrained('jurnal_guru')->cascadeOnDelete();
            $table->unsignedTinyInteger('jam_ke'); // misal 1-7
            $table->timestamps();

            $table->unique(['jurnal_guru_id', 'jam_ke']); // 1 jurnal tidak boleh duplikat jam ke yang sama
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jurnal_guru_jam');
    }
};
