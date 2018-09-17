<link href="{{ asset('vendor/i18n/assets/css/dashboard.css') }}" rel="stylesheet">

{{--TODO: If js are not included here (and in js partial) "$().tooltip is not a function" error is shown--}}
<script src="{{ asset('vendor/i18n/assets/js/require.min.js') }}"></script>

<script>
    requirejs.config({
        baseUrl: '../../vendor/i18n'
    });
</script>

<script src="{{ asset('vendor/i18n/assets/js/dashboard.js') }}"></script>