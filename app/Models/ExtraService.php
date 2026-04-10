<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class ExtraService extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'extra_services';

    protected $fillable = ['name', 'icon_path', 'description', 'price', 'is_active'];

    public $translatable = ['name', 'description'];
}
