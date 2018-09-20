{{-- TODO: See css.blade.php file --}}
<script src="{{ asset('vendor/laravel-i18n/assets/tabler/js/require.min.js') }}"></script>

<script>
    requirejs.config({
        baseUrl: '{{ \Illuminate\Support\Facades\URL::to('/') }}/vendor/laravel-i18n/assets/tabler'
    });
</script>

<script src="{{ asset('vendor/laravel-i18n/assets/tabler/js/dashboard.js') }}"></script>