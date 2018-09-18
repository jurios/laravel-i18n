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
        $languages = Language::filtersResults($filters);

        return view('i18n::languages/index', compact('languages', 'filters'));
    }

    /**
     * Show enable dialog
     *
     * @param Language $language
     * @return mixed
     */
    public function enable_dialog(Language $language)
    {
        return view('i18n::languages/modals/enable_language', compact('language'));
    }

    /**
     * Enable a language.
     *
     * @return \Illuminate\Http\Response
     */
    public function enable(Language $language)
    {
        $language->enabled = true;
        $language->save();

        return redirect()->route('i18n.languages.index');
    }

    /**
     * Show disable dialog
     *
     * @param Language $language
     * @return mixed
     */
    public function disable_dialog(Language $language)
    {
        return view('i18n::languages/modals/disable_language', compact('language'));
    }

    /**
     * Disable a language.
     *
     * @return \Illuminate\Http\Response
     */
    public function disable(Language $language)
    {
        $language->enabled = false;
        $language->save();

        return redirect()->route('i18n.languages.index');
    }
}
