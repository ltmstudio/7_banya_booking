<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Банные комнаты: название, категория, описание, фото, цены, статус.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->json('name');                          // Название
            $table->enum('category', ['family', 'vip', 'standard']); // Семейная / VIP / Обычная
            $table->json('description')->nullable();
            $table->decimal('price_per_hour', 10, 2);        // Базовая цена за 1 час
            $table->decimal('promo_price_per_hour', 10, 2)->nullable(); // Акционная (онлайн) цена
            $table->boolean('is_active')->default(true);     // Статус (активна / отключена)
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
