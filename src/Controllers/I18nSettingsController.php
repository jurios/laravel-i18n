<?php

namespace Kodilab\LaravelI18n\Controllers;

use Kodilab\LaravelI18n\Filters\LanguageFilter;
use Kodilab\LaravelI18n\Language;

class I18nSettingsController extends I18nController
{
    public function languages(LanguageFilter $filters)
    {
        $languages = Language::filters($filters)->results($filters);

        return view('i18n::settings/languages/languages', compact('languages', 'filters'));
    }
}
