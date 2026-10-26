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
            $table->integer('saldo_periode_lalu')->default(0)->after('kategori_kas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_listrik', function (Blueprint $table) {
            $table->dropColumn('saldo_periode_lalu');
        });
    }
};
