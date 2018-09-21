<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <title>{{ config('app.name', 'Laravel') }}: i18n</title>

    @include('i18n::layout.partials.js')
    @include('i18n::layout.partials.css')
    @stack('css')
    <style>
        @stack('inline-css')
    </style>
</head>

<body>
    <div class="page">
        <div class="page-main">
            @include('i18n::layout.partials.header.header')
            @include('i18n::layout.partials.navigation')
            <div class="my-3 my-md-5">
                <div class="container">
                    @yield('alerts')
                    <div class="page-header">
                        <h1 class="page-title">
                            @yield('title')
                        </h1>
                        <div class="page-subtitle">
                            @yield('subtitle')
                        </div>
                        <div class="page-options d-flex">
                            @yield('options')
                        </div>
                    </div>
                    <div class="row">
                        @yield('content')
                    </div>
                </div>
            </div>
            <div class="my-3 my-md-5">
                <div class="container text-center">
                    <div class="page-subtitle">
                        Copyright © 2018 Laravel-i18n. Theme by codecalm.net (tabler)
                    </div>
                    <div class="page-subtitle">
                        <i class="fe fe-code"></i> with <i class="fe fe-heart"></i> by Kodilab
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('i18n::layout.partials.modals.ajax_dialog.placeholder_modal')

    @stack('help-modals')

    @stack('js')

    @stack('inline-js')

</body>
</html>