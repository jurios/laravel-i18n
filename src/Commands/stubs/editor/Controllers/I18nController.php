<?php

namespace {{namespace}}Http\Controllers\i18n;

class I18nController extends \Illuminate\Routing\Controller
{
    /**
     * Path to the views
     */
    const VIEW_PATH = 'vendor.i18n';

    /**
     * Start sync process
     */
    public function sync(\Illuminate\Http\Request $request)
    {
        \Illuminate\Support\Facades\Artisan::call('i18n:sync');

        return redirect()->route('i18n.dashboard');
    }
}