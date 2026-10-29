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
        Schema::table('keuangan', function (Blueprint $table) {
            $table->string('kategori_kas', 100)->default('Kas Karang Taruna')->after('nominal');
            $table->index('kategori_kas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('keuangan', function (Blueprint $table) {
            $table->dropIndex(['kategori_kas']);
            $table->dropColumn('kategori_kas');
        });
    }
};
