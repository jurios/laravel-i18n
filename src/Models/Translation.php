<?php

namespace Kodilab\LaravelI18n\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Kodilab\LaravelFilters\Filterable;

class Translation extends Model
{

    use Filterable;

    protected $table;

    protected $fillable = ['translation', 'locale_id', 'md5', 'text_id', 'needs_revision'];

    protected $casts = [
        'md5' => 'string',
        'translation' => 'string',
        'locale_id' => 'integer',
        'text_id' => 'integer',
        'needs_revision' => 'boolean'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('i18n.tables.translations');
    }

    protected static function boot()
    {
        parent::boot(); // TODO: Change the autogenerated stub

        self::saved(function (Translation $translation) {
            self::forgetTranslationFromCache($translation);
        });
    }

    //relationships

    public function locale()
    {
        return $this->belongsTo(Locale::class);
    }

    public function text()
    {
        return $this->belongsTo(Text::class);
    }

    //scope methods

    // methods
    /**
     * Returns whether the translation exists for the locale $locale
     * @param $md5
     * @param Locale $locale
     * @return bool
     */
    public static function existsTranslation($md5, Locale $locale)
    {
        return !is_null(Translation::where('md5', $md5)->where('locale_id', $locale->id)->first());
    }

    /**
     * Retrieves the fallback translation for a given $text. If it does not exist, then a translation is created
     *
     * @param string $text
     * @return Translation|null
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingLocaleException
     */
    public static function generateFallbackTranslationOrGetExistent(string $text)
    {
        $fallback_locale = Locale::getFallbackLocale();
        $md5 = md5($text);

        if(!self::existsTranslation($md5, $fallback_locale))
        {
            return Translation::create([
                'translation' => $text,
                'md5' => $md5,
                'locale_id' => $fallback_locale->id
            ]);
        }

        return self::getTranslation($text, Locale::getFallbackLocale());
    }

    /**
     * Returns the Translation of $text in $locale locale. If it doesn't exist, try to create it for the fallback locale
     *
     * @param string $text
     * @param Locale $locale
     * @param bool $honestly
     * @return Translation|null
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingLocaleException
     */
    public static function getTranslation(string $text, Locale $locale, $honestly = false)
    {
        $md5 = md5($text);

        /** @var Translation $translation */
        $translation = Translation::where('md5', $md5)->where('locale_id', $locale->id)->first();

        if(!is_null($translation))
        {
            return $translation;
        }

        $fallback_translation = self::generateFallbackTranslationOrGetExistent($text);

        return $honestly ? null : $fallback_translation;
    }

    /**
     * Returns the translations text of the $text in the $locale language
     *
     * @param string $text
     * @param Locale $locale
     * @param bool $honestly
     * @return string
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingLocaleException
     */
    public static function getTextTranslation(string $text, Locale $locale, $honestly = false)
    {
        $translation = self::getTranslation($text, $locale, $honestly);

        return !is_null($translation) ? $translation->translation : "";
    }

    /**
     * Store a $locale $translation in cache for a $ttl time
     *
     * @param string $md5
     * @param string $text
     * @param Locale $locale
     * @param int $ttl
     */
    public static function putTranslationCache(string $md5, string $text, Locale $locale, $ttl = 60)
    {
        Cache::put(self::translationCacheKey($md5, $locale), $text, $ttl);
    }

    /**
     * Forget from the cache a translation based on its $md5 and $locale
     *
     * @param string|null $md5
     * @param Locale|null $locale
     */
    public static function forgetCache(string $md5 = null, Locale $locale = null)
    {
        $translations = null;

        if (is_null($md5) && is_null($md5))
        {
            $translations = Translation::all();
        }

        if (is_null($md5) && !is_null($locale))
        {
            $translations = Translation::where('locale_id', $locale->id)->get();
        }

        if (is_null($locale) && !is_null($md5))
        {
            $translations = Translation::where('md5', $md5)->get();
        }

        if (!is_null($locale) && !is_null($md5))
        {
            $translations = Translation::where('md5', $md5)->where('locale_id', $locale->id)->get();
        }

        // Just in case...
        if (is_null($translations))
        {
            return;
        }

        foreach ($translations as $translation)
        {
            self::forgetTranslationFromCache($translation);
        }
    }

    /**
     * Forget from the cache a $translation
     *
     * @param Translation $translation
     */
    private static function forgetTranslationFromCache(Translation $translation)
    {
        Cache::forget(self::translationCacheKey($translation->md5, $translation->locale));
    }

    /**
     * Get the respective key from a translation based on its $md5 and $locale
     *
     * @param string $md5
     * @param Locale $locale
     * @return string
     */
    private static function translationCacheKey(string $md5, Locale $locale)
    {
        return $locale->reference . '_' . $md5;
    }
}
