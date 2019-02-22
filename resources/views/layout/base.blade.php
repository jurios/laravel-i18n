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

<body class="h-100">
<div id="app" class="grid h-100">
    <flash></flash>
    <header>
        @include('i18n::layout.partials.header.nav')
    </header>
    <main class="grid">
        @include('i18n::layout.partials.navigation')
        <div class="fluid-container p-4">
            @yield('alerts')
            <div class="grid main--content">
                <div class="h1">@yield('title')</div>
                <div>
                    @yield('options')
                </div>
            </div>
            @yield('content')
        </div>
    </main>
</div>
    @include('i18n::layout.partials.modals.ajax_dialog.placeholder_modal')

    @stack('help-modals')

    @include('i18n::layout.partials.js')
    <script>
        @if(\Illuminate\Support\Facades\Session::has('status'))
        @php($status = \Illuminate\Support\Facades\Session::get('status'))
        @php($level = 'info')
        @php($dismissible = true)
        @php($message = null)
        @php($icon = true)
        @if(isset($status['message']))
        @php($message = $status['message'])
        @endif
        @if(isset($status['level']))
        @php($level = $status['level'])
        @endif
        @if(isset($status['dismissible']) && $status['dismissible'] === false)
        @php($dismissible = false)
        @endif
        flash("{!! ($message) !!}", "{{ $level }}", "{{ $icon }}", "{{ $dismissible }}");
        @endif
    </script>
    @stack('inline-js')
</body>
</html>