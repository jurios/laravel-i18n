<?php


namespace Kodilab\LaravelI18n\i18n;


use Carbon\Carbon;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Facades\App;
use Kodilab\LaravelI18n\Facades\i18n;
use Kodilab\LaravelI18n\Models\Locale;

class i18NManager
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
        if (i18n::isTimezoneValid($timezone)) {
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
}