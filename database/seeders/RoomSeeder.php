<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = [
            // 1. VIP женское отделение
            [
                'name' => json_encode(['ru' => 'VIP женское отделение', 'tk' => 'VIP zenan bölümi'], JSON_UNESCAPED_UNICODE),
                'category' => 'vip',
                'description' => json_encode(['ru' => 'VIP отделение для женщин. Время посещения: 2 часа.', 'tk' => 'VIP zenanlar üçin bölümi. Görüş wagty: 2 sagat.'], JSON_UNESCAPED_UNICODE),
                'price_per_hour' => 40.00,   // 80 манат за 2 часа
                'promo_price_per_hour' => null,
                'capacity' => 1,       // 1 взрослый
                'child_price_per_hour' => 17.50,   // 35 манат за 2 часа
                'cleaning_buffer_minutes' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 2. VIP мужское отделение
            [
                'name' => json_encode(['ru' => 'VIP мужское отделение', 'tk' => 'VIP erkek bölümi'], JSON_UNESCAPED_UNICODE),
                'category' => 'vip',
                'description' => json_encode(['ru' => 'VIP отделение для мужчин. Время посещения: 2 часа.', 'tk' => 'VIP erkekler üçin bölümi. Görüş wagty: 2 sagat.'], JSON_UNESCAPED_UNICODE),
                'price_per_hour' => 40.00,
                'promo_price_per_hour' => null,
                'capacity' => 1,
                'child_price_per_hour' => 17.50,
                'cleaning_buffer_minutes' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 3. Мини сауна
            [
                'name' => json_encode(['ru' => 'Мини сауна', 'tk' => 'Kiçi sauna'], JSON_UNESCAPED_UNICODE),
                'category' => 'standard',
                'description' => json_encode(['ru' => 'Мини сауна на 2 места.', 'tk' => 'Kiçi sauna 2 ýer üçin.'], JSON_UNESCAPED_UNICODE),
                'price_per_hour' => 40.00,
                'promo_price_per_hour' => null,
                'capacity' => 2,
                'child_price_per_hour' => null,
                'cleaning_buffer_minutes' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 4. Душевая кабина
            [
                'name' => json_encode(['ru' => 'Душевая кабина', 'tk' => 'Duş kabini'], JSON_UNESCAPED_UNICODE),
                'category' => 'standard',
                'description' => json_encode(['ru' => 'Душевая кабина на 6 мест.', 'tk' => 'Duş kabini 6 ýer üçin.'], JSON_UNESCAPED_UNICODE),
                'price_per_hour' => 180.00,
                'promo_price_per_hour' => null,
                'capacity' => 6,
                'child_price_per_hour' => null,
                'cleaning_buffer_minutes' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 5. Сауна №1
            [
                'name' => json_encode(['ru' => 'Сауна №1', 'tk' => 'Sauna №1'], JSON_UNESCAPED_UNICODE),
                'category' => 'standard',
                'description' => json_encode(['ru' => 'Сауна на 3 места.', 'tk' => 'Sauna 3 ýer üçin.'], JSON_UNESCAPED_UNICODE),
                'price_per_hour' => 100.00,
                'promo_price_per_hour' => null,
                'capacity' => 3,
                'child_price_per_hour' => null,
                'cleaning_buffer_minutes' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 6. Сауна №2
            [
                'name' => json_encode(['ru' => 'Сауна №2', 'tk' => 'Sauna №2'], JSON_UNESCAPED_UNICODE),
                'category' => 'standard',
                'description' => json_encode(['ru' => 'Сауна на 4 места.', 'tk' => 'Sauna 4 ýer üçin.'], JSON_UNESCAPED_UNICODE),
                'price_per_hour' => 120.00,
                'promo_price_per_hour' => null,
                'capacity' => 4,
                'child_price_per_hour' => null,
                'cleaning_buffer_minutes' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 7. Сауна №3
            [
                'name' => json_encode(['ru' => 'Сауна №3', 'tk' => 'Sauna №3'], JSON_UNESCAPED_UNICODE),
                'category' => 'standard',
                'description' => json_encode(['ru' => 'Сауна на 4 места.', 'tk' => 'Sauna 4 ýer üçin.'], JSON_UNESCAPED_UNICODE),
                'price_per_hour' => 120.00,
                'promo_price_per_hour' => null,
                'capacity' => 4,
                'child_price_per_hour' => null,
                'cleaning_buffer_minutes' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 8. Сауна №4
            [
                'name' => json_encode(['ru' => 'Сауна №4', 'tk' => 'Sauna №4'], JSON_UNESCAPED_UNICODE),
                'category' => 'standard',
                'description' => json_encode(['ru' => 'Сауна на 6 мест.', 'tk' => 'Sauna 6 ýer üçin.'], JSON_UNESCAPED_UNICODE),
                'price_per_hour' => 250.00,
                'promo_price_per_hour' => null,
                'capacity' => 6,
                'child_price_per_hour' => null,
                'cleaning_buffer_minutes' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 9. Сауна №5
            [
                'name' => json_encode(['ru' => 'Сауна №5', 'tk' => 'Sauna №5'], JSON_UNESCAPED_UNICODE),
                'category' => 'standard',
                'description' => json_encode(['ru' => 'Сауна на 6 мест.', 'tk' => 'Sauna 6 ýer üçin.'], JSON_UNESCAPED_UNICODE),
                'price_per_hour' => 250.00,
                'promo_price_per_hour' => null,
                'capacity' => 6,
                'child_price_per_hour' => null,
                'cleaning_buffer_minutes' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 10. Сауна №6
            [
                'name' => json_encode(['ru' => 'Сауна №6', 'tk' => 'Sauna №6'], JSON_UNESCAPED_UNICODE),
                'category' => 'standard',
                'description' => json_encode(['ru' => 'Сауна на 4 места.', 'tk' => 'Sauna 4 ýer üçin.'], JSON_UNESCAPED_UNICODE),
                'price_per_hour' => 150.00,
                'promo_price_per_hour' => null,
                'capacity' => 4,
                'child_price_per_hour' => null,
                'cleaning_buffer_minutes' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 11. Сауна №7
            [
                'name' => json_encode(['ru' => 'Сауна №7', 'tk' => 'Sauna №7'], JSON_UNESCAPED_UNICODE),
                'category' => 'standard',
                'description' => json_encode(['ru' => 'Сауна на 3 места.', 'tk' => 'Sauna 3 ýer üçin.'], JSON_UNESCAPED_UNICODE),
                'price_per_hour' => 130.00,
                'promo_price_per_hour' => null,
                'capacity' => 3,
                'child_price_per_hour' => null,
                'cleaning_buffer_minutes' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 12. Сауна №8
            [
                'name' => json_encode(['ru' => 'Сауна №8', 'tk' => 'Sauna №8'], JSON_UNESCAPED_UNICODE),
                'category' => 'standard',
                'description' => json_encode(['ru' => 'Сауна на 4 места.', 'tk' => 'Sauna 4 ýer üçin.'], JSON_UNESCAPED_UNICODE),
                'price_per_hour' => 130.00,
                'promo_price_per_hour' => null,
                'capacity' => 4,
                'child_price_per_hour' => null,
                'cleaning_buffer_minutes' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 13. Сауна №9
            [
                'name' => json_encode(['ru' => 'Сауна №9', 'tk' => 'Sauna №9'], JSON_UNESCAPED_UNICODE),
                'category' => 'standard',
                'description' => json_encode(['ru' => 'Сауна на 6 мест.', 'tk' => 'Sauna 6 ýer üçin.'], JSON_UNESCAPED_UNICODE),
                'price_per_hour' => 250.00,
                'promo_price_per_hour' => null,
                'capacity' => 6,
                'child_price_per_hour' => null,
                'cleaning_buffer_minutes' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 14. Сауна №10
            [
                'name' => json_encode(['ru' => 'Сауна №10', 'tk' => 'Sauna №10'], JSON_UNESCAPED_UNICODE),
                'category' => 'standard',
                'description' => json_encode(['ru' => 'Сауна на 6 мест.', 'tk' => 'Sauna 6 ýer üçin.'], JSON_UNESCAPED_UNICODE),
                'price_per_hour' => 250.00,
                'promo_price_per_hour' => null,
                'capacity' => 6,
                'child_price_per_hour' => null,
                'cleaning_buffer_minutes' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 15. Сауна №11
            [
                'name' => json_encode(['ru' => 'Сауна №11', 'tk' => 'Sauna №11'], JSON_UNESCAPED_UNICODE),
                'category' => 'standard',
                'description' => json_encode(['ru' => 'Сауна на 8 мест.', 'tk' => 'Sauna 8 ýer üçin.'], JSON_UNESCAPED_UNICODE),
                'price_per_hour' => 300.00,
                'promo_price_per_hour' => null,
                'capacity' => 8,
                'child_price_per_hour' => null,
                'cleaning_buffer_minutes' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 16. Сауна – люкс №1
            [
                'name' => json_encode(['ru' => 'Сауна – люкс №1', 'tk' => 'Sauna – lyuks №1'], JSON_UNESCAPED_UNICODE),
                'category' => 'vip',
                'description' => json_encode(['ru' => 'Сауна люкс на 8 мест.', 'tk' => 'Sauna lyuks 8 ýer üçin.'], JSON_UNESCAPED_UNICODE),
                'price_per_hour' => 800.00,
                'promo_price_per_hour' => null,
                'capacity' => 8,
                'child_price_per_hour' => null,
                'cleaning_buffer_minutes' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            // 17. Сауна – люкс №2
            [
                'name' => json_encode(['ru' => 'Сауна – люкс №2', 'tk' => 'Sauna – lyuks №2'], JSON_UNESCAPED_UNICODE),
                'category' => 'vip',
                'description' => json_encode(['ru' => 'Сауна люкс на 8 мест.', 'tk' => 'Sauna lyuks 8 ýer üçin.'], JSON_UNESCAPED_UNICODE),
                'price_per_hour' => 800.00,
                'promo_price_per_hour' => null,
                'capacity' => 8,
                'child_price_per_hour' => null,
                'cleaning_buffer_minutes' => null,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('rooms')->insert($rooms);
    }
}
