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
    <header s>
        @include('i18n::layout.partials.header.nav')
    </header>
    <div class="content grid">
        <div class="sidebar bg-dark">
            @include('i18n::layout.partials.navigation')
        </div>
        <div class="fluid-container p-4">
            @yield('content')
        </div>
    </div>
    @include('i18n::layout.partials.js')
</body>
</html>