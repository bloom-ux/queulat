# Loading assets

Queulat enqueues its admin assets through an implementation of
`Queulat\Contracts\Asset_Loader_Interface`. The bundled
`Queulat\Helpers\Webpack_Asset_Loader` reads a Webpack `manifest.json` /
`entrypoints.json` and exposes `enqueue_script( $handle )`, `enqueue_style( $handle )`,
`register_script( $path, $handle, $deps )` and `register_style( $path, $handle, $deps )`.

The loader is constructed with a unique `$prefix` and, optionally, the
`$base_directory` and `$base_uri` where the built files live (when omitted, they
default to the current theme's `assets/dist` folder). The handle you pass to
`enqueue_script()` / `enqueue_style()` must match a key in your `manifest.json`
or `entrypoints.json`.

For a theme:

```php
use Queulat\Helpers\Webpack_Asset_Loader;

// The base directory/URI default to the active theme's assets/dist folder.
$asset_loader = new Webpack_Asset_Loader( 'my-theme' );

add_action( 'wp_enqueue_scripts', function () use ( $asset_loader ) {
    $asset_loader->enqueue_script( 'frontend.js' );
    $asset_loader->enqueue_style( 'frontend.css' );
} );
```

For a plugin, pass the build paths explicitly:

```php
use Queulat\Helpers\Webpack_Asset_Loader;

$asset_loader = new Webpack_Asset_Loader(
    'my-plugin',
    plugin_dir_path( __FILE__ ) . 'assets/dist',
    plugins_url( 'assets/dist', __FILE__ )
);

add_action( 'admin_enqueue_scripts', function () use ( $asset_loader ) {
    $asset_loader->enqueue_script( 'admin.js', array( 'jquery' ) );
} );
```

You can also pass your own loader implementation of `Asset_Loader_Interface` to
`Queulat\Bootstrap`:

```php
$bootstrap = new Queulat\Bootstrap( $asset_loader );
$bootstrap->init();
```
