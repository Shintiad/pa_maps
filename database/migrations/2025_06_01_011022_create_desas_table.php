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
        Schema::create('desas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('kecamatan_id');
            $table->string('nama_desa');
            $table->timestamps();

            $table->foreign('kecamatan_id')->references('id')->on('kecamatans')->onDelete('cascade');
            
            $table->unique(['kecamatan_id', 'nama_desa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('desas', function (Blueprint $table) {
            $table->dropForeign(['kecamatan_id']);
            $table->dropColumn('kecamatan_id');
            $table->dropUnique(['kecamatan_id', 'nama_desa']);
        });
        Schema::dropIfExists('desas');
    }
};
