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
     * SetLocale constructor.
     */
    public function __construct()
    {
    }

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

        $locale = $this->getLocale();

        $this->setLocale($locale);

        return $next($request);
    }

    /**
     * Set locale into the runtime configuration
     *
     * @param Locale $locale
     */
    protected function setLocale(Locale $locale)
    {
        App::setLocale($locale->reference);
        date_default_timezone_set(!is_null($locale->tz) ? $locale->tz : config('app.timezone'));

        config([
            'app.timezone' => !is_null($locale->tz) ? $locale->tz : config('app.timezone'),
        ]);
    }

    /**
     * Method that will return the locale that is going to be used for that request
     *
     * @return Locale
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException
     */
    protected function getLocale()
    {
        return Locale::getFallbackLocale();
    }

}