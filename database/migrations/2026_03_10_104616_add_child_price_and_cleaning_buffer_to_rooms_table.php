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
        Schema::table('rooms', function (Blueprint $table) {
            // Цена для детей (5–12 лет), nullable — если нет детской цены
            $table->decimal('child_price_per_hour', 10, 2)
                ->nullable()
                ->after('promo_price_per_hour');

            // Буфер уборки в минутах для конкретной комнаты
            // Если null — используется глобальный из system_settings
            $table->unsignedSmallInteger('cleaning_buffer_minutes')
                ->nullable()
                ->after('child_price_per_hour');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['child_price_per_hour', 'cleaning_buffer_minutes']);
        });
    }
};
