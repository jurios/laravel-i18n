<?php

namespace Kodilab\LaravelI18n\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Kodilab\LaravelI18n\Language;

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

        if (!$request->session()->has(config('i18n.session_var_name')))
        {
            $available_languages = Language::enabled()->get();

            $locale = $this->getLocaleFromRequestOrFallbackLanguage($request, $available_languages);
            $request->session()->put(config('i18n.session_var_name'), $locale);
        }

        return $next($request);
    }

    private function getLocaleFromRequestOrFallbackLanguage(Request $request, Collection $available_languages)
    {
        $available_languages_ISO_639_1 = $available_languages->pluck('ISO_639_1')->toArray();

        $request_languages = $request->getLanguages();

        foreach ($request_languages as $request_language) {
            if (in_array($request_language, $available_languages_ISO_639_1))
            {
                return $request_language;
            }
        }

        return Language::getBaseLanguage()->reference;
    }
}
