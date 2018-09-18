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
        $languages = Language::with('translations')->filtersResults($filters);

        return view('i18n::languages/index', compact('languages', 'filters'));
    }
}
