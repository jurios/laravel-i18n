<?php

namespace Kodilab\LaravelI18n\Controllers;


class I18nDashboardController extends I18nController
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        return view($this->getConfigView(__FUNCTION__, 'i18n::dashboard.dashboard'));
    }
}
