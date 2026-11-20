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
        if (! Schema::hasTable('method_status_logs')) {
            Schema::create('method_status_logs', function (Blueprint $table) {
                $table->id();
                $table->string('http_method', 10)->index();
                $table->string('route_name', 150)->nullable()->index();
                $table->string('controller_action', 255)->nullable();
                $table->string('path', 255);
                $table->unsignedSmallInteger('status_code')->index();
                $table->unsignedInteger('duration_ms')->default(0);
                $table->unsignedInteger('memory_kb')->default(0);
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('user_name', 150)->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamp('created_at')->useCurrent()->index();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('method_status_logs');
    }
};
