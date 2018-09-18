<?php

namespace Kodilab\LaravelI18n\Controllers;

use Kodilab\LaravelI18n\Filters\LanguageFilter;
use Kodilab\LaravelI18n\Language;

class I18NLanguagesController extends \Illuminate\Routing\Controller
{
    /**
     * Display a listing of the languages.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LanguageFilter $filters)
    {
        $languages = Language::with('translations')->enabled()->filtersResults($filters);

        return view('i18n::languages/index', compact('languages', 'filters'));
    }

    public function mark_default_dialog(Language $language)
    {
        return view('i18n::languages.modals.default_language', compact('language'));
    }

    public function mark_default(Language $language)
    {
        $language->markAsDefault();

        return redirect()->route('i18n.languages.index');
    }
}
