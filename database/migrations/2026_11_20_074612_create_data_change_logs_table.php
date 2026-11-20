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
        if (! Schema::hasTable('data_change_logs')) {
            Schema::create('data_change_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('user_name', 150);
                $table->string('user_email', 150)->nullable();
                $table->string('user_peran', 30)->default('guest');
                $table->string('aksi', 20);
                $table->string('model_type', 120);
                $table->string('model_label', 80);
                $table->unsignedBigInteger('model_id')->nullable();
                $table->string('subject_label', 255)->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->string('http_method', 10)->nullable();
                $table->string('url', 500)->nullable();
                $table->timestamp('created_at')->useCurrent()->index();

                $table->index(['aksi', 'created_at']);
                $table->index(['model_type', 'model_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_change_logs');
    }
};
