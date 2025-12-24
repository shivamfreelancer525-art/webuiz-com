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
        Schema::create('custom_domain_queue', function (Blueprint $table) {
            $table->increments('id');
            $table->string('host', 100)->index();
            $table->integer('user_id')->index();
            $table->string('server_ip', 45)->nullable();
            $table->enum('status', ['pending', 'validating', 'valid', 'failed'])->default('pending')->index();
            $table->text('validation_error')->nullable();
            $table->integer('validation_attempts')->default(0);
            $table->timestamp('created_at')->index();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('validated_at')->nullable();
            
            // Prevent duplicate pending domains for same user
            $table->unique(['user_id', 'host', 'status'], 'unique_pending_domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_domain_queue');
    }
};

