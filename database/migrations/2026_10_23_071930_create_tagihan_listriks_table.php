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
        Schema::create('tagihan_listrik', function (Blueprint $table) {
            $table->id();
            $table->string('periode');
            $table->unsignedTinyInteger('rt');
            $table->string('nama_pelanggan');
            $table->string('nomor_struk');
            $table->integer('tagihan_awal');
            $table->integer('kas_dusun')->default(3000);
            $table->integer('total_tagihan');
            $table->integer('selisih_bayar')->default(0);
            $table->boolean('is_lunas')->default(false);
            $table->string('kode_gabungan')->nullable()->index();
            $table->timestamps();

            $table->index(['periode', 'rt']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tagihan_listrik');
    }
};
