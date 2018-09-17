{{-- TODO: See css.blade.php file --}}
<script src="{{ asset('vendor/i18n/assets/js/require.min.js') }}"></script>

<script>
    requirejs.config({
        baseUrl: '../../vendor/i18n'
    });
</script>

<script src="{{ asset('vendor/i18n/assets/js/dashboard.js') }}"></script>