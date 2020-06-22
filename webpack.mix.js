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

mix.scripts([
        'resources/js/jquery.js',
        'resources/js/bootstrap.bundle.js',
        'node_modules/aos/dist/aos.js',
        'resources/js/functions.js',
    ],  'public/js/app.js')
    .scripts([
        'resources/js/svgMap.js'
    ],  'public/js/map.js');
