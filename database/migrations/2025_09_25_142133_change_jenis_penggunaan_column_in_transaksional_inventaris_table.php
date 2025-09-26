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
        Schema::table('transaksional_inventaris', function (Blueprint $table) {
            $table->string('jenis_penggunaan')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksional_inventaris', function (Blueprint $table) {
            $table->enum('jenis_penggunaan', ['tetap', 'permanen', 'mobile'])->after('nama_inventaris');
        });
    }
};
