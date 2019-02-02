<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-100">
<head>
    <title>{{ config('app.name', 'Laravel') }}: i18n</title>

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    @include('i18n::layout.partials.css')
    @stack('css')

    <style>
        @stack('inline-css')
    </style>
</head>

<body class="grid h-100">
    <header>
        @include('i18n::layout.partials.header.nav')
    </header>
    <main class="grid">
        @include('i18n::layout.partials.navigation')
        <div class="fluid-container p-4">
            <h1>@yield('title')</h1>
            @yield('content')
        </div>
    </main>
    @include('i18n::layout.partials.modals.ajax_dialog.placeholder_modal')

    @stack('help-modals')

    @include('i18n::layout.partials.js')
    @stack('inline-js')
</body>
</html>