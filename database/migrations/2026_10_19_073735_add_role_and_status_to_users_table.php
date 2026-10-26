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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('peran', ['superadmin', 'admin', 'warga'])->default('warga')->after('email');
            $table->string('no_hp', 30)->nullable()->after('peran');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif')->after('no_hp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['peran', 'no_hp', 'status']);
        });
    }
};
