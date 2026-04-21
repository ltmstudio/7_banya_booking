<?php

namespace Database\Seeders;

use App\Models\SiteContent;
use Illuminate\Database\Seeder;

class SiteContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SiteContent::query()->updateOrCreate(
            ['key' => 'about'],
            ['content' => [
                'ru' => 'Мы создали уютное пространство для отдыха и восстановления сил. Наш банный комплекс предлагает комнаты на любой вкус — семейные, VIP и обычные. Приходите и отдохните с нами!',
                'tk' => 'Biz dynç almak we güýç dikeltmek üçin amatly giňişlik döretdik. Hamam toplumymyz her tat üçin otaglary hödürleýär — maşgala, VIP we adaty. Geliň we biz bilen dynç alyň!',
            ]]
        );

        SiteContent::query()->updateOrCreate(
            ['key' => 'about_title'],
            ['content' => [
                'ru' => 'О нашем комплексе',
                'tk' => 'Toplumymyz hakda',
            ]]
        );
    }
}
