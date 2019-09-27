<?php


namespace Kodilab\LaravelI18n\i18n;


use Carbon\Carbon;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Facades\App;
use Kodilab\LaravelI18n\Models\Locale;

class i18n
{
    /**
     * @var Config
     */
    protected $config;

    public function __construct(Config $config)
    {
        $this->config = $config;

        $fallback_locale = Locale::getFallbackLocale();

        $this->setFallbackLocale($fallback_locale);
        $this->setLocale(Locale::getLocaleOrFallback($this->config->get('app.locale')));
    }

    /**
     * Set the locale setting
     *
     * @param Locale $locale
     */
    public function setLocale(Locale $locale)
    {
        if ($locale->exists) {
            App::setLocale($locale->laravel_locale);
            Carbon::setLocale($locale->carbon_locale);
        }
    }

    /**
     * Set the fallback locale setting
     *
     * @param Locale $locale
     */
    public function setFallbackLocale(Locale $locale)
    {
        if ($locale->exists) {
            app('config')->set('app.fallback_locale', $locale->reference);
        }
    }

    /**
     * Set the timezone setting
     *
     * @param string $timezone
     */
    public function setTimezone(string $timezone)
    {
        if (self::isTimezoneValid($timezone)) {
            app('config')->set('app.timezone', $timezone);
            date_default_timezone_set($timezone);
        }
    }

    /**
     * Returns the loaded locale
     *
     */
    public function getLocale()
    {
        return Locale::getLocale(config('app.locale'));
    }

    /**
     * Returns the loaded fallback locale
     */
    public function getFallbackLocale()
    {
        return Locale::getLocale(config('app.fallback_locale'));
    }

    /**
     * Generate the locale reference based on language and region attributes
     *
     * @param string $language
     * @param string|null $region
     * @return string
     */
    public static function generateReference(string $language, string $region = null)
    {
        $language = mb_strtolower(trim($language));
        $region = !is_null($region) ? mb_strtoupper(trim($region)) : null;

        return is_null($region) ? $language : "{$language}_{$region}";
    }

    /**
     * Returns the language from a locale reference
     *
     * @param string $reference
     * @return mixed
     */
    public static function getLanguage(string $reference)
    {
        if (!self::isReferenceValid($reference)) {
            throw new \InvalidArgumentException('Name ' . $reference . ' is not a valid locale reference');
        }

        return explode('_', $reference)[0];
    }

    /**
     * Returns the region from the locale reference or null
     *
     * @param string $reference
     * @return |null
     */
    public static function getRegion(string $reference)
    {
        if (!self::isReferenceValid($reference)) {
            throw new \InvalidArgumentException('Name ' . $reference . ' is not a valid locale reference');
        }

        $exploded = explode('_', $reference);

        return isset($exploded[1]) ? $exploded[1] : null;
    }

    /**
     * Returns whether a reference is valid
     *
     * @param string $reference
     * @return bool
     */
    public static function isReferenceValid(string $reference)
    {
        return (bool)preg_match('/^[a-z]{2,3}(_[A-Z]{2,3})?$/', $reference);
    }

    /**
     * Returns whether a timezone is valid
     *
     * @param string $timezone
     * @return bool
     */
    public static function isTimezoneValid(string $timezone)
    {
        $occurrence = array_filter(timezone_identifiers_list(), function ($item) use ($timezone) {
            return $item === $timezone;
        });

        return count($occurrence) > 0;
    }
}