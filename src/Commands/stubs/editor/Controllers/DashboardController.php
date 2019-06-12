<?php

namespace {{namespace}}Http\Controllers\i18n;

class DashboardController extends I18nController
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        return view(self::VIEW_PATH . '.dasboard');
    }
}