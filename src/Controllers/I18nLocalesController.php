<?php

namespace Kodilab\LaravelI18n\Controllers;

use Illuminate\Http\Request;
use Kodilab\LaravelI18n\Filters\LocaleFilter;
use Kodilab\LaravelI18n\Models\Locale;

class I18nLocalesController extends I18nController
{
    /**
     * Display a listing of the languages.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(LocaleFilter $filters)
    {
        $locales = Locale::with('translations')->enabled()
            ->filters($filters)->results($filters);

        return view($this->getConfigView(__FUNCTION__, 'i18n::locales.index'),
            compact('locales', 'filters'));
    }

    public function enable_dialog(Request $request, Locale $locale)
    {
        return view($this->getConfigView(__FUNCTION__, 'i18n::locales.modals.enable_locale'),
            compact('locale'));
    }

    public function enable(Locale $locale)
    {
        //TODO: Create a method
        $locale->enabled = true;
        $locale->save();

        return redirect()->route('i18n.locales.index');
    }

    public function disable_dialog(Locale $locale)
    {
        return view($this->getConfigView(__FUNCTION__, 'i18n::locales.modals.disable_locale'),
            compact('locale'));
    }

    public function disable(Locale $locale)
    {
        $locale->enabled = false;
        $locale->save();

        return redirect()->route('i18n.locales.index');
    }
}
