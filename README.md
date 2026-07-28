# Queulat

A developers' toolset for WordPress: custom post types, queries and objects, REST
fields, meta boxes and a form builder, wired together with a small set of
helpers and a WordPress-style autoloader.

## Installation

Install with [Composer](https://getcomposer.org/):

`composer require bloom-ux/queulat:dev-main`

Composer will install on `wp-content/mu-plugins/queulat`

If you need to install on a different folder, you should add something like this to your project's composer.json:

```json
{
	"extra" : {
		"installer-paths" : {
			"htdocs/wp-content/mu-plugins/{$name}" : ["type:wordpress-muplugin"]
		}
	}
}
```

Where `htdocs/wp-content/mu-plugins/{$name}` it's the path to your mu-plugins directory. Queulat will be installed as a sub-folder on the specified folder.

## Loading Queulat as mu-plugin

Queulat uses the Composer autoloader to lazy-load most of its code, so you need to make sure that the autoloader is included before initializing Queulat.

Also, since mu-plugins installed on a sub-folder are not automatically loaded by WordPress you must manually require the main file.

You can solve this with a single file on the mu-plugins folder, such as:

```php
<?php
/**
 * Plugin Name: Queulat Loader
 * Description: Load Queulat mu-plugin
 */

// Load Composer autoloader (ABSPATH it's the path to wp-load.php).
require_once ABSPATH .'/../vendor/autoload.php';

// Load Queulat main file.
require_once __DIR__ .'/queulat/queulat.php';
```

Plugin headers are optional, but recommended.

You could also use something like [Bedrock's autoloader](https://github.com/roots/bedrock/blob/master/web/app/mu-plugins/bedrock-autoloader.php), which will load all mu-plugins installed on sub-folders (you can just copy that file on your mu-plugin folder and it will automagically load Queulat).

## Autoloading

Queulat's own classes are loaded through Composer's classmap autoloader (see "Loading Queulat as mu-plugin" above). In addition, Queulat ships a small WordPress-style autoloader — `Queulat\Helpers\Autoloader` — intended for code that follows WordPress file-naming conventions, such as generated plugins.

The autoloader maps a namespace prefix to a base directory: each namespace segment becomes a kebab-cased subdirectory, and the class name is resolved to a `class-`, `interface-`, `trait-`, or `enum-` prefixed file. For example, `My_Plugin\Song\Song_Post_Type` would be looked up as `includes/song/class-song-post-type.php`.

Generated custom post type plugins already wire this up via the plugin stub:

```php
if ( is_callable( array( '\Queulat\Helpers\Autoloader', 'boot' ) ) ) {
    \Queulat\Helpers\Autoloader::boot( 'My_Plugin\\', __DIR__ . '/includes' );
}
```

To use it in your own plugin, boot it with your namespace and base directory before registering any hooks:

```php
\Queulat\Helpers\Autoloader::boot( 'My_Plugin\\', __DIR__ . '/includes' );
```

## Documentation

* [Getting started](docs/getting-started.md) — architecture overview, post types, queries, objects and plugin bootstrapping.
* [Generating plugins](docs/generating-plugins.md) — scaffolding CPT and REST field plugins with WP-CLI.
* [REST fields](docs/rest-fields.md) — extending the WordPress REST API.
* [Loading assets](docs/assets.md) — enqueuing Webpack-built assets.
* [Meta boxes](docs/meta-boxes.md) — building focused data-entry interfaces.
* [Using Queulat Forms](docs/forms.md) — form elements, validation and the `Node_Factory`.
* [Interfaces](docs/interfaces.md) — contracts, hookable components and form node interfaces.

See also the [Changelog](CHANGELOG.md).
