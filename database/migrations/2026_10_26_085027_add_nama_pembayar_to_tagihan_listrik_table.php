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
        Schema::table('tagihan_listrik', function (Blueprint $table) {
            $table->string('nama_pembayar')->nullable()->after('nama_warga');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_listrik', function (Blueprint $table) {
            $table->dropColumn(['nama_pembayar']);
        });
    }
};
