<?php

namespace Kodilab\LaravelI18n\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Kodilab\LaravelI18n\Language;

class Callback
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
        $response = $next($request);

        if ($response instanceof Response)
        {
            if ($request->has('_callback'))
            {
                return redirect($request->input('_callback'));
            }
        }

        return $response;
    }

    private function getLocaleFromRequestOrDefaultLanguage(Request $request, Collection $available_languages)
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
