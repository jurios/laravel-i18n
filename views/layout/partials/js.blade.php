{{-- TODO: See css.blade.php file --}}
<script src="{{ asset('vendor/i18n/assets/js/require.min.js') }}"></script>

<script>
    requirejs.config({
        baseUrl: '{{ \Illuminate\Support\Facades\URL::to('/') }}/vendor/i18n'
    });
</script>

<script src="{{ asset('vendor/i18n/assets/js/dashboard.js') }}"></script>