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
    .js('resources/assets/js/i18n.js', 'public/assets/js')
    .sass('resources/assets/css/i18n.scss', 'public/assets/css');
