<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SiteContent extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = ['key', 'content'];

    public array $translatable = ['content'];

    /**
     * Получает локализованный контент по ключу.
     */
    public static function getContent(string $key, string $locale = 'ru'): string
    {
        $record = static::query()->where('key', $key)->first();
        if (! $record) {
            return '';
        }

        return $record->getTranslation('content', $locale, false) ?? '';
    }

    /**
     * Обновляет/создаёт контент по ключу.
     *
     * @param  array{ru: string, tk: string}  $translations
     */
    public static function setContent(string $key, array $translations): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['content' => $translations]
        );
    }
}
