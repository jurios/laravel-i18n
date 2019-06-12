<?php

namespace {{namespace}}Http\Controllers\i18n;

class LocaleController extends I18nController
{
    public function index()
    {
        $locales = \Kodilab\LaravelI18n\Models\Locale::all();

        return view(self::VIEW_PATH . '.editor.locales.index', compact('locales'));
    }
}