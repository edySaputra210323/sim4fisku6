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
        Schema::create('detail_atk_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atk_keluar_id')->constrained('atk_keluar')->cascadeOnDelete();
            $table->foreignId('atk_id')->constrained('atk')->cascadeOnDelete();
            $table->integer('qty')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_atk_keluar');
    }
};
