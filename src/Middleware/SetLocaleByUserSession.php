<?php

namespace Kodilab\LaravelI18n\Middleware;


use Illuminate\Support\Facades\URL;
use Kodilab\LaravelI18n\Models\Locale;

class SetLocaleByUserSession extends SetLocale
{

    protected function getLocale()
    {
        $locale = Locale::getFallbackLocale();

        if ($this->request->session()->has('user_locale')) {
            $locale = $this->request->session()->get('user_locale');
        }

        URL::defaults(['locale' => $locale->reference]);

        return $locale;

    }
}