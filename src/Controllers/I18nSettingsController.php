<?php

namespace Kodilab\LaravelI18n\Controllers;

use Kodilab\LaravelI18n\Filters\LocaleFilter;
use Kodilab\LaravelI18n\Language;

class I18nSettingsController extends I18nController
{
    public function languages(LocaleFilter $filters)
    {
        $languages = Language::filters($filters)->results($filters);

        return view('i18n::settings/languages/languages', compact('languages', 'filters'));
    }
}
