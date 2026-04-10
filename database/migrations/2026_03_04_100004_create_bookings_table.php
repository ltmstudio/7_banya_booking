<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Заявки/брони: комната, клиент или гость, дата/время, длительность,
 * статус, расчётная и итоговая сумма, причина отмены.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained();
            $table->foreignId('client_id')->nullable()->constrained();
            $table->foreignId('created_by')->nullable()->constrained('users'); // кто создал
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('cleaning_end_time');          // 👈 добавить — буфер уборки (end_time + 15/30 мин)
            $table->integer('duration_hours');               // 1, 2, 3... часов
            $table->integer('guests_count');                 // Количество человек
            $table->enum('status', [
                'new',          // Новая (с сайта)
                'preliminary',  // Предварительная бронь
                'confirmed',    // Подтверждено
                'paid',         // Оплачено
                'completed',    // Завершено
                'cancelled',     // Отменено
            ])->default('new');
            $table->enum('source', ['online', 'admin'])->default('admin'); // 👈 добавить — откуда пришла заявка
            $table->text('cancel_reason')->nullable();       // Причина отмены (обязательна при отмене)
            $table->decimal('base_amount', 10, 2);           // Первоначальная сумма
            $table->decimal('final_amount', 10, 2);          // Итоговая сумма (ручная скидка/штраф)
            $table->boolean('is_promo_applied')->default(false);        // 👈 добавить — применена ли акционная цена
            $table->boolean('is_online_payment')->default(false);
            $table->string('online_payment_status')->nullable(); // placeholder для Рысгал
            $table->string('online_payment_ref')->nullable();

            // 👇 добавить — данные гостя с сайта (до создания клиента)
            $table->string('guest_name')->nullable();
            $table->string('guest_phone')->nullable();
            $table->string('guest_last_name')->nullable();
            $table->date('guest_birthday')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
