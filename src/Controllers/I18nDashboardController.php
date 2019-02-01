<?php

namespace Kodilab\LaravelI18n\Controllers;


class I18nDashboardController extends \Illuminate\Routing\Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        return view('i18n::dashboard/dashboard');
    }
}
