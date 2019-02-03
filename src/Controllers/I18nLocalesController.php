<?php

namespace Kodilab\LaravelI18n\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Kodilab\LaravelI18n\Filters\LocaleFilter;
use Kodilab\LaravelI18n\Http\Requests\CreateLocaleRequest;
use Kodilab\LaravelI18n\Http\Requests\EditLocaleRequest;
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
        $locales = Locale::with('translations')
            ->filters($filters)->results($filters);

        return view($this->getConfigView(__FUNCTION__, 'i18n::locales.index'),
            compact('locales', 'filters'));
    }

    public function create()
    {
        $locale = new Locale();

        return view($this->getConfigView(__FUNCTION__, 'i18n::locales.form'),
            compact('locale'));
    }

    public function store(CreateLocaleRequest $request)
    {
        $locale = Locale::create([
            'ISO_639_1' => $request->input('ISO_639_1'),
            'region' => $request->input('region'),
            'description' => $request->input('description'),
            'laravel_locale' => $request->input('laravel_locale'),
            'currency_number_decimals' => $request->input('currency_number_decimals'),
            'currency_decimals_punctuation' => $request->input('currency_decimals_punctuation'),
            'currency_thousands_separator' => $request->input('currency_thousands_separator'),
            'currency_symbol' => $request->input('currency_symbol'),
            'currency_symbol_position' => $request->input('currency_symbol_position'),
            'carbon_locale' => $request->input('carbon_locale'),
            'carbon_tz' => $request->input('carbon_tz')
        ]);

        return redirect()->route('i18n.locales.index');
    }

    public function edit(Locale $locale)
    {
        return view($this->getConfigView(__FUNCTION__, 'i18n::locales.form'),
            compact('locale'));
    }

    public function update(EditLocaleRequest $request, Locale $locale)
    {

        //Unfortunately, can't get the locale in the EditLocaleRequest (not sure why :( ), thus this validation must be
        // done here.
        Validator::make($request->all(), [
            'ISO_639_1' => [function ($attribute, $value, $fail) use ($request, $locale) {
                $occ = Locale::where('ISO_639_1', $value)->where('region', $request->input('region'))->first();

                //Check if exists another locale with the same ISO-Region and is not the given one
                if (!is_null($occ) && $occ->id !== $locale->id)
                {
                    $fail('Already exists another locale for this ISO - region');
                }
            }
        ]])->validate();

        $locale->update([
            'ISO_639_1' => $request->input('ISO_639_1'),
            'region' => $request->input('region'),
            'description' => $request->input('description'),
            'laravel_locale' => $request->input('laravel_locale'),
            'currency_number_decimals' => $request->input('currency_number_decimals'),
            'currency_decimals_punctuation' => $request->input('currency_decimals_punctuation'),
            'currency_thousands_separator' => $request->input('currency_thousands_separator'),
            'currency_symbol' => $request->input('currency_symbol'),
            'currency_symbol_position' => $request->input('currency_symbol_position'),
            'carbon_locale' => $request->input('carbon_locale'),
            'carbon_tz' => $request->input('carbon_tz')
        ]);

        return redirect()->route('i18n.locales.index');
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
