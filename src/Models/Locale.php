<?php

namespace Kodilab\LaravelI18n;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Kodilab\LaravelI18n\Exceptions\MissingLocaleException;

class Locale extends Model
{

    protected $table;

    protected $fillable = ['language_id', 'fallback', 'created_by_sync'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('i18n.tables.locale', 'i18n_locales');
    }


    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    //Scopes
    public function scopeEnabled(Builder $query, bool $value = true)
    {
        return $query->where('enabled', $value);
    }

    //Methods
    public function getLaravelLocale()
    {
        if ($this->language)
        {
            return $this->language->reference;
        }

        return null;
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
     * Returns the user configured locale. If it's not configured by middleware then fallback language is used
     * @return mixed
     * @throws MissingLanguageException
     */
    public static function getUserLocale()
    {
        if (session()->has('locale'))
        {
            return session()->get('locale');
        }

        $user_locale = self::getFallbackLocale();

        session()->put('locale', $user_locale);

        return $user_locale;
    }

    static function getFallbackLocale()
    {
        if (session()->has('fallback_locale'))
        {
            return session()->get('fallback_locale');
        }

        // TODO: Try to load the translation from this locale
        $fallback_locale = Locale::enabled()->where('fallback', true)->first();

        if(is_null($fallback_locale))
        {
            throw new MissingLocaleException(
                'Enabled fallback locale not found. One fallback locale must exists');
        }

        session()->flash('fallback_language', $fallback_locale);

        return $fallback_locale;
    }

    static function getBestLocale(string $language, string $region = null)
    {
        $language = Language::getLanguageFromISO_639_1($language);

        if (is_null($language))
        {
            return null;
        }

        $query = Locale::enabled()->where('language_id', $language->id);
        /** @var Collection $locales_based_on_language */
        $locales = clone $query;
        $locales_based_on_language = $locales->get();

        if ($locales_based_on_language->isEmpty())
        {
            return null;
        }

        if (!is_null($region))
        {
            $locales = clone $query;

            /** @var Collection $locales_based_on_language_and_region */
            $locales_based_on_language_and_region = $locales->where('region', $region)->get();

            if (!$locales_based_on_language_and_region->isEmpty())
            {
                return $locales_based_on_language_and_region->first();
            }
        }

        return $locales_based_on_language->first();
    }

}
