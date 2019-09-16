<?php

namespace Kodilab\LaravelI18n\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kodilab\LaravelFilters\Traits\Filterable;
use Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException;
use Kodilab\LaravelI18n\Translations\Translator;

class Locale extends Model
{

    use Filterable;

    protected $table;

    protected $fillable = [
        'iso',
        'region',
        'description',
        'laravel_locale',
        'currency_number_decimals',
        'currency_decimals_punctuation',
        'currency_thousands_separator',
        'currency_symbol',
        'currency_symbol_position',
        'carbon_locale',
        'tz',
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

        self::creating(function (Locale $model) {
            if (!is_null(Locale::getLocale($model->name))) {
                throw new \Exception('Locale ' . $model->name . 'already exists.');
            }
        });
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('i18n.tables.locale', 'i18n_locales');
    }

    public function getNameAttribute()
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

    /**
     * Get the fallback locale. It does not exits, then an exception is sent.
     *
     * @return Locale
     * @throws MissingFallbackLocaleException
     */
    public static function getFallbackLocale()
    {
        /** @var Locale $fallback_locale */
        $fallback_locale = self::where('fallback', true)->first();

        if (is_null($fallback_locale)) {
            throw new MissingFallbackLocaleException('Fallback locale not found.');
        }

        return $fallback_locale;
    }

    /**
     * Returns a locale by name. If it does not exist, then null is returned.
     *
     * @param string $name
     * @return mixed
     */
    public static function getLocale(string $name)
    {
        $iso = explode("_", $name)[0];
        $region = isset(($splitted = explode("_", $name))[1]) ? $splitted[1] : null;

        return self::where('iso', $iso)->where('region', $region)->first();
    }

    /**
     * Returns a locale by name. If it does not exist, then fallback locale is returned
     *
     * @param string $name
     * @return Locale
     * @throws MissingFallbackLocaleException
     */
    public static function getLocaleOrFallback(string $name)
    {
        if (!is_null($locale = self::getLocale($name))) {
            return $locale;
        }

        return self::getFallbackLocale();
    }

    /**
     * Returns the translation collection of the locale
     *
     * @return \Illuminate\Support\Collection
     * @throws MissingFallbackLocaleException
     */
    public function getTranslationsAttribute()
    {
        $translator = new Translator($this);

        return $translator->translations;
    }

    /**
     * Find a translation by original text
     *
     * @param string $original
     * @return mixed
     * @throws MissingFallbackLocaleException
     */
    public function translation(string $original)
    {
        $translator = new Translator($this);

        return $translator->find($original);
    }

    /**
     * Updates a translation
     *
     * @param string $original
     * @param string $translation
     * @throws MissingFallbackLocaleException
     */
    public function updateTranslation(string $original, string $translation)
    {
        $translator = new Translator($this);

        $translator->update($original, $translation);
    }

    public function getPercentageAttribute()
    {
        $translator = new Translator($this);

        return $translator->percentage;
    }

    public function getTranslatedAttribute()
    {
        $translator = new Translator($this);

        $result = new Collection();

        foreach ($translator->translations as $translation)
        {
            if (!$translation->isEmpty()) {
                $result->put($translation->original, $translation);
            }
        }

        return $result;
    }
}
