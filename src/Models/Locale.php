<?php

namespace Kodilab\LaravelI18n\Models;

use Illuminate\Database\Eloquent\Model;
use Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException;
use Kodilab\LaravelI18n\Translations\TranslationsManager;

class Locale extends Model
{

    protected $table;

    protected $fillable = [
        'iso',
        'description',
        'laravel_locale',
        'currency_number_decimals',
        'currency_decimals_punctuation',
        'currency_thousands_separator',
        'currency_symbol',
        'currency_symbol_position',
        'carbon_locale',
        'carbon_tz',
        'fallback',
        'enabled'
    ];

    protected $casts = [
        'dialect_of_id' => 'integer',
        'enabled' => 'boolean',
        'fallback' => 'boolean',
        'currency_number_decimals' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        self::saving(function (Locale $model) {

            $model->iso = strtolower($model->iso);
            $model->region = !is_null($model->region) ? strtoupper($model->region) : null;
            $model->carbon_locale = !is_null($model->carbon_locale) ? strtolower($model->carbon_locale) : $model->iso;
            $model->laravel_locale = !is_null($model->laravel_locale) ? strtolower($model->laravel_locale) : $model->iso;

            if ($model->isFallback()) {
                $model->enabled = true;
            }

        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('i18n.tables.locale', 'i18n_locales');
    }

    public function getReferenceAttribute()
    {
        if (!is_null($this->region)) {
            return $this->iso . '_' . $this->region;
        }

        return $this->iso;
    }

    public function isFallback()
    {
        return $this->fallback;
    }

    public static function getFallbackLocale()
    {
        /** @var Locale $fallback_locale */
        $fallback_locale = self::where('fallback', true)->first();

        if (is_null($fallback_locale)) {
            throw new MissingFallbackLocaleException('Fallback locale not found.');
        }

        return $fallback_locale;
    }

    public function getTranslationsAttribute()
    {
        $manager = new TranslationsManager($this);

        return $manager->translations;
    }

    public function translation(string $original)
    {
        $manager = new TranslationsManager($this);

        return $manager->find($original);
    }

    public function getPercAttribute()
    {
        $manager = new TranslationsManager($this);

        return $manager->percentage;
    }
}
