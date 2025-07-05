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
        Schema::table('penyakits', function (Blueprint $table) {
            $table->text('pengertian')->after('link_metabase')->nullable();
            $table->text('penyebab')->after('pengertian')->nullable();
            $table->text('gejala')->after('penyebab')->nullable();
            $table->text('diagnosis')->after('gejala')->nullable();
            $table->text('komplikasi')->after('diagnosis')->nullable();
            $table->text('pengobatan')->after('komplikasi')->nullable();
            $table->text('pencegahan')->after('pengobatan')->nullable();
            $table->string('gambar')->after('pencegahan')->nullable();
            $table->string('sumber_informasi')->after('gambar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyakits', function (Blueprint $table) {
            $table->dropColumn('pengertian');
            $table->dropColumn('penyebab');
            $table->dropColumn('gejala');
            $table->dropColumn('diagnosis');
            $table->dropColumn('komplikasi');
            $table->dropColumn('pengobatan');
            $table->dropColumn('pencegahan');
            $table->dropColumn('gambar');
            $table->dropColumn('sumber_informasi');
        });
    }
};