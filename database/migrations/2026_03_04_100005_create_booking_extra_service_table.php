<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Связь брони и доп. услуг с фиксацией цены и количества на момент брони.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_extra_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->foreignId('extra_service_id')->constrained();
            $table->unsignedTinyInteger('quantity')->default(1); // 👈 добавить — кол-во единиц услуги
            $table->decimal('price_at_booking', 10, 2);      // Цена на момент бронирования
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_extra_service');
    }
};
