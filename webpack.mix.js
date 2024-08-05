const mix = require("laravel-mix");

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

mix.options({
    autoprefixer: {remove: false}
});
mix.setPublicPath("./")
.setResourceRoot("../")
    .sass("src/resources/sass/core/core.scss", "css/core.css")
    .sass("src/resources/sass/invoice/_80mm.scss", "css/invoice/80mm.css")
    .sass("node_modules/dropzone/src/dropzone.scss", "css/dropzone.css")
    .sass("src/resources/sass/_global.scss", "css/fontawesome.css")
    .js("src/resources/js/mainApp.js", "js/core.js").vue()
    .extract([
        // Extract packages from node_modules to vendor.js
        "jquery",
        "bootstrap",
        "popper.js",
        "axios",
        "sweetalert2",
        "lodash"
    ])
    .sourceMaps()

if (mix.inProduction()) {
    mix.version()
        .options({
            // Optimize JS minification process
            terser: {}
        });
} else {
    // Uses inline source-maps on development
    mix.webpackConfig({
        devtool: "inline-source-map"
    });
}