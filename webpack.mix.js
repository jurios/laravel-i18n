const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.copy('node_modules/jquery/dist/jquery.js', 'public/assets/js')
    .copy('node_modules/bootstrap/dist/css/bootstrap.css', 'public/assets/css')
    .copy('node_modules/bootstrap/dist/js/bootstrap.js', 'public/assets/js')
    .copy('node_modules/@fortawesome/fontawesome-free/css/all.css', 'public/assets/css/fontawesome.css')
    .copy('node_modules/@fortawesome/fontawesome-free/webfonts', 'public/assets/webfonts')
    .js([
        'resources/assets/js/i18n.js',
        'resources/assets/js/components/vue-select/Select.vue',
        'resources/assets/js/components/flash/FlashMessageComponent.vue'
    ], 'public/assets/js')
    .sass('resources/assets/css/i18n.scss', 'public/assets/css');
