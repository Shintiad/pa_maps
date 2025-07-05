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
        Schema::create('penduduks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tahun_id');
            $table->unsignedBigInteger('kecamatan_id');
            $table->integer('jumlah_penduduk');
            $table->timestamps();

            $table->foreign('tahun_id')->references('id')->on('tahuns')->onDelete('cascade');
            $table->foreign('kecamatan_id')->references('id')->on('kecamatans')->onDelete('cascade');

            $table->unique(['tahun_id', 'kecamatan_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penduduks', function (Blueprint $table) {
            $table->dropForeign(['tahun_id']);
            $table->dropColumn('tahun_id');
            $table->dropForeign(['kecamatan_id']);
            $table->dropColumn('kecamatan_id');
            $table->dropUnique(['tahun_id', 'kecamatan_id']);
        });
        Schema::dropIfExists('penduduks');
    }
};
