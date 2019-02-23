<?php

namespace Kodilab\LaravelI18n\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Kodilab\LaravelI18n\Exceptions\MissingLocaleException;
use Kodilab\LaravelI18n\Models\Locale;

abstract class SetLocale
{
    /** @var Request $request */
    protected $request;

    public function __construct()
    {}

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     * @throws MissingLocaleException
     */
    public function handle(Request $request, Closure $next)
    {
        $this->request = $request;

        $locale = $this->getLocale();

        $this->saveRequestLocale($locale);
        $this->updateLocales($locale);

        return $next($request);
    }

    /**
     * Method that will return the locale that is going to be used for that request
     *
     * @return mixed
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingLocaleException
     */
    protected function getLocale()
    {
        return Locale::getFallbackLocale();
    }

    private function updateLocales(Locale $locale)
    {
        // TODO: Check if we should do this
        App::setLocale($locale->laravel_locale);
        \Carbon\Carbon::setLocale($locale->carbon_locale);
    }

    private function saveRequestLocale($locale)
    {
        $this->request->session()->put('locale', $locale);
    }

}
