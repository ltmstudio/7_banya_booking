<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
        'description',
    ];

    // Удобный helper — получить значение по ключу
    public static function get(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    // Удобный helper — установить значение по ключу
    public static function set(string $key, mixed $value): void
    {
        static::updateOrInsert(
            ['key' => $key],
            ['value' => $value]
        );
    }
}
