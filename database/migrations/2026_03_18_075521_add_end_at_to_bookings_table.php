<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Добавляет поле `end_at` (timestamp) для быстрого поиска просроченных броней.
 * `end_at` = booking_date + cleaning_end_time (дата/время окончания с учетом уборки).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->timestamp('end_at')->nullable()->index();
        });

        // Бэкфилл для существующих записей (PostgreSQL): дата + time -> timestamp.
        DB::statement("
            UPDATE bookings
            SET end_at = (booking_date::text || ' ' || cleaning_end_time::text)::timestamp
            WHERE end_at IS NULL
        ");
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['end_at']);
            $table->dropColumn('end_at');
        });
    }
};
