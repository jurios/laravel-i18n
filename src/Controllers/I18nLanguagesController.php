<?php

namespace Kodilab\LaravelI18n\Controllers;

use Illuminate\Http\Request;
use Kodilab\LaravelI18n\Filters\LanguageFilter;
use Kodilab\LaravelI18n\Language;

class I18NLanguagesController extends I18nController
{
    /**
     * Display a listing of the languages.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LanguageFilter $filters)
    {
        $languages = Language::with('translations')->enabled()->filters($filters)->results($filters);

        return view('i18n::languages/index', compact('languages', 'filters'));
    }

    public function enable_dialog(Request $request, Language $language)
    {
        return view('i18n::languages.modals.enable_language', compact('language'));
    }

    public function enable(Language $language)
    {
        $language->enable();

        return redirect()->route('i18n.languages.index');
    }

    public function disable_dialog(Language $language)
    {
        return view('i18n::languages.modals.disable_language', compact('language'));
    }

    public function disable(Language $language)
    {
        $language->disable();

        return redirect()->route('i18n.languages.index');
    }
}
