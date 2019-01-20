<?php

namespace Kodilab\LaravelI18n;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kodilab\LaravelI18n\Exceptions\MissingLocaleException;

class Locale extends Model
{

    protected $table;

    protected $fillable = ['ISO_639_1', 'fallback', 'created_by_sync'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('i18n.tables.locale', 'i18n_locales');
    }


    public function translations()
    {
        return $this->belongsTo(Translation::class);
    }

    //Scopes
    public function scopeEnabled(Builder $query, bool $value = true)
    {
        return $query->where('enabled', $value);
    }

    public function getReferenceAttribute()
    {
        return is_null($this->region) ? $this->ISO_639_1 : $this->ISO_639_1 . '_' . $this->region;
    }

    //Methods
    public function isFallbackLocale()
    {
        return $this->fallback;
    }

    public function getLaravelLocale()
    {
        return $this->laravel_locale;
    }

    public function getCarbonLocale()
    {
        if (!is_null($this->carbon_locale))
        {
            return $this->carbon_locale;
        }

        return $this->getLaravelLocale();
    }

    /**
     * Returns the user configured locale. If it's not configured by middleware then fallback locale is used
     * @return mixed
     * @throws MissingLocaleException
     */
    public static function getUserLocale()
    {
        if (session()->has('locale'))
        {
            return session()->get('locale');
        }

        $locale = Locale::getFallbackLocale();

        session()->put('locale', $locale);

        return $locale;
    }

    static function getFallbackLocale()
    {
        // TODO: Try to load the translation from this locale
        $fallback_locale = Locale::enabled()->where('fallback', true)->first();

        if(is_null($fallback_locale))
        {
            throw new MissingLocaleException(
                'Enabled fallback locale not found. One fallback locale must exists');
        }

        return $fallback_locale;
    }

    static function getBestLocale(string $locale, string $region = null)
    {
        //TODO
    }

}
