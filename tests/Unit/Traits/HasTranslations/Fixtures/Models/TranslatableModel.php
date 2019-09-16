<?php


namespace Kodilab\LaravelI18n\Tests\Unit\Traits\HasTranslations\Fixtures\Models;


use Illuminate\Database\Eloquent\Model;
use Kodilab\LaravelI18n\Traits\HasTranslations;

class TranslatableModel extends Model
{
    use HasTranslations;

    protected $casts = [
        'field' => 'translatable'
    ];
}