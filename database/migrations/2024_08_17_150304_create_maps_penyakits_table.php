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
        Schema::create('maps_penyakits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tahun_id');
            $table->unsignedBigInteger('penyakit_id');
            $table->string('link_metabase');
            $table->timestamps();

            $table->foreign('tahun_id')->references('id')->on('tahuns')->onDelete('cascade');
            $table->foreign('penyakit_id')->references('id')->on('penyakits')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maps_penyakits', function (Blueprint $table) {
            $table->dropForeign(['tahun_id']);
            $table->dropColumn('tahun_id');
            $table->dropForeign(['penyakit_id']);
            $table->dropColumn('penyakit_id');
        });
        Schema::dropIfExists('maps_penyakits');
    }
};
