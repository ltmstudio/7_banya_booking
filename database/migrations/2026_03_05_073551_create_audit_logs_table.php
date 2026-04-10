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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // кто
            $table->string('entity_type');                   // модель (Booking, Client и т.д.)
            $table->unsignedBigInteger('entity_id');
            $table->string('action');                        // created / updated / deleted
            $table->string('field')->nullable();             // какое поле изменилось
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->string('user_agent')->nullable(); // 👈 добавить — браузер/устройство
            $table->timestamp('created_at')->useCurrent(); // 👈 добавь useCurrent()
            $table->index(['entity_type', 'entity_id']);
            $table->index(['user_id', 'created_at']); // 👈 добавить — история действий пользователя
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
