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
        Schema::table('warga', function (Blueprint $table) {
            $table->string('nama_pelanggan_pln')->nullable()->after('nama');
        });

        Schema::table('tagihan_listrik', function (Blueprint $table) {
            $table->string('nama_warga')->nullable()->after('nama_pelanggan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_listrik', function (Blueprint $table) {
            $table->dropColumn(['nama_warga']);
        });

        Schema::table('warga', function (Blueprint $table) {
            $table->dropColumn(['nama_pelanggan_pln']);
        });
    }
};
