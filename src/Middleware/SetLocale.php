<?php


namespace Kodilab\LaravelI18n\Middleware;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Kodilab\LaravelI18n\Models\Locale;

abstract class SetLocale
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Locale
     */
    protected $locale;

    /**
     * @var string
     */
    protected $timezone;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException
     */
    public function handle(Request $request, \Closure $next)
    {
        $this->request = $request;

        $this->locale = $this->locale();
        $this->timezone = $this->timezone($this->locale);

        $this->setLocale($this->locale);
        $this->setTimezone($this->timezone);

        return $next($request);
    }

    /**
     * Returns the locale to be used by Laravel
     *
     * @return Locale
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException
     */
    protected function locale()
    {
        return Locale::getFallbackLocale();
    }

    /**
     * Returns the timezone to be used by Laravel
     *
     * @param Locale $locale
     * @return string|null
     */
    protected function timezone(Locale $locale)
    {
        return $locale->tz;
    }

    /**
     * Change Laravel locale
     *
     * @param Locale $locale
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException
     */
    private function setLocale(Locale $locale)
    {
        $locale->enabled ? App::setLocale($locale->reference) : App::setLocale(Locale::getFallbackLocale()->reference);
    }

    /**
     * Chnage Laravel timezone
     *
     * @param string|null $timezone
     */
    private function setTimezone(string $timezone = null)
    {
        $timezone = is_null($timezone) ? config('app.timezone', 'UTC') : $timezone;

        date_default_timezone_set($timezone);

        config([
            'app.timezone' => $timezone,
        ]);
    }
}