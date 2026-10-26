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
            $table->foreignId('warga_id')->nullable()->after('id')->constrained('warga')->nullOnDelete();
            $table->integer('kas_karang_taruna')->default(3000)->after('tagihan_awal');
            $table->string('kategori_kas')->default('karang_taruna')->after('kas_dusun');
            $table->date('tanggal_bayar')->nullable()->after('is_lunas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan_listrik', function (Blueprint $table) {
            $table->dropForeign(['warga_id']);
            $table->dropColumn(['warga_id', 'kas_karang_taruna', 'kategori_kas', 'tanggal_bayar']);
        });
    }
};
