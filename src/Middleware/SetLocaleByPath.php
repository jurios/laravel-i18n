<?php

namespace Kodilab\LaravelI18n\Middleware;


use Illuminate\Support\Facades\URL;
use Kodilab\LaravelI18n\Models\Locale;

class SetLocaleByPath extends SetLocale
{

    protected function getLocale()
    {
        $path_locale_reference = $this->request->segment(1);

        $locale = Locale::getLocaleByReference($path_locale_reference);

        URL::defaults(['locale' => $path_locale_reference]);

        return !is_null($locale) ? $locale : Locale::getFallbackLocale();

    }
}