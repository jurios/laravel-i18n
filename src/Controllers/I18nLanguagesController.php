<?php

namespace Kodilab\LaravelI18n\Controllers;

use Kodilab\LaravelI18n\Language;

class I18NLanguagesController extends \Illuminate\Routing\Controller
{
    /**
     * Display a listing of the languages.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $languages = Language::all();

        //return view('activities.index', compact('activities'));
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

        return response()->json($language);
    }

    /**
     * Disable a language.
     *
     * @return \Illuminate\Http\Response
     */
    public function disable(Language $language)
    {
        $language->disabled = true;
        $language->save();

        return response()->json($language);
    }
}
