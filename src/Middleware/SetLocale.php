<?php

namespace Kodilab\LaravelI18n\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Kodilab\LaravelI18n\Locale;

class SetLocale
{
    public function __construct()
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = null;

        if (!$request->session()->has('locale'))
        {
            /** @var Locale $locale */
            $locale = $this->getLocaleFromRequestOrFallbackLocale($request);

            $request->session()->put('locale', $locale);

        } else {

            /** @var Locale $locale */
            $locale = $request->session()->get('locale');

        }

        App::setLocale($locale->getLaravelLocale());
        \Carbon\Carbon::setLocale($locale->getCarbonLocale());

        // Here you can add other third-party packages that need locale configuration as well.

        return $next($request);
    }

    private function getLocaleFromRequestOrFallbackLocale(Request $request)
    {
        $request_languages = $request->getLanguages();

        foreach ($request_languages as $request_language) {

            $region = $this->getRegionFromLocale($request_language);
            $language = $this->getLanguageFromLocale($request_language);

            $best_locale = Locale::getBestLocale($language, $region);

            if (!is_null($best_locale))
            {
                return $best_locale;
            }
        }

        return Locale::getFallbackLocale();
    }

    private function getRegionFromLocale($locale)
    {
        $locale = explode("_", $locale);

        return isset($locale[1]) ? $locale[1] : null;
    }

    private function getLanguageFromLocale($locale)
    {
        $locale = explode("_", $locale);

        return isset($locale[0]) ? $locale[0] : null;
    }
}
