<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class PopupPromo extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'popup_promos';

    protected $fillable = ['title', 'body', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public array $translatable = ['title', 'body'];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
