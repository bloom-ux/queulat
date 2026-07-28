# Getting started

Queulat it's aimed at *improving the way we create things for WordPress*, so instead of fundamentally transforming it, it tries to use familiar concepts to build better-structured things for it, using *custom post types*, *custom post queries* and *custom post objects*.

You can generate these using Queulat's *custom post type plugin generator*, available as a WP-CLI command.

## Types, queries and objects

Each custom post type plugin it's composed of:

* An entry file, which initializes the plugin
* A post type definition, for instance: `Song_Post_Type`. This class defines the labels and other arguments for registering the post type on WordPress. By default, the plugin activation will add the needed permisions for administrators and flush rewrites. You could extend this class if you need to define custom hooks for extra functionality for the post type.
* A query definition, like `Song_Post_Query`. You can use this class to create new database queries, using any default params that you might want to define for this type of content, and iterate over the results using a simple `foreach` instead of the classic WordPress loop.
* An object definition: `Song_Post_Object`, which will be returned on the `foreach` loop when using the custom query. This way, you could add any custom methods to this class, which will be available on the `foreach` loop.

Using Queulat, you could do something like:

```php
$tracklist = new Song_Post_Query( array(
	'tax_query'      => array(
		array(
			'taxonomy' => 'albums',
			'term'     => 'dark-side-of-the-moon',
			'field'    => 'slug'
		)
	)
) );

foreach ( $tracklist as $track ) {
	echo $track->title();
	echo $track->duration();
	echo $track->lyrics();
}
```

### Post Types

Queulat's code generator scaffolds the basic files for a custom post type — the
post type, query and object classes plus a plugin entry file — and you extend
them to add your own behaviour.

A post type is defined by extending `Queulat\Post_Type` and implementing two
methods:

* `get_post_type(): string` — returns the post type slug (e.g. `song`).
* `get_post_type_args(): array` — returns the arguments passed to WordPress'
  `register_post_type()`, including labels, `supports`, `rewrite`, `menu_icon`
  and REST configuration.

```php
namespace My\Song;

use Queulat\Post_Type;

class Song_Post_Type extends Post_Type {
    public function get_post_type(): string {
        return 'song';
    }

    public function get_post_type_args(): array {
        return array(
            'label'         => __( 'Songs', 'my-song' ),
            'labels'        => array(
                'name'          => __( 'Songs', 'my-song' ),
                'singular_name' => __( 'Song', 'my-song' ),
            ),
            'public'       => true,
            'show_in_rest' => true,
            'menu_icon'    => 'dashicons-format-audio',
            'supports'     => array( 'title', 'editor', 'thumbnail' ),
            'rewrite'      => array( 'slug' => 'songs' ),
        );
    }
}
```

Because `Post_Type` implements `Queulat\Contracts\Hookable_Interface`, you
register it by passing an instance to `queulat_register_hooks()` (usually from
your plugin's `bootstrap()` function, which the generator also creates):

```php
use function Queulat\queulat_register_hooks;
use My\Song\Song_Post_Type;

queulat_register_hooks( new Song_Post_Type() );
```

This calls `init()`, which hooks the post type registration into WordPress.

### Post Queries

A query class extends `Queulat\Post_Query` and lets you run `WP_Query`-style
searches for your post type while returning decorated objects instead of plain
`WP_Post` instances. Implement:

* `get_post_type(): string` — the post type slug the query targets.
* `get_decorator(): string` — the `Post_Object` subclass used to wrap each result.
* `get_default_args(): array` — default query arguments merged into every query.

```php
namespace My\Song;

use Queulat\Post_Query;

class Song_Post_Query extends Post_Query {
    public function get_post_type(): string {
        return 'song';
    }

    public function get_decorator(): string {
        return Song_Post_Object::class;
    }

    public function get_default_args(): array {
        return array(
            'post_status' => 'publish',
            'orderby'     => 'title',
            'order'       => 'ASC',
        );
    }
}
```

You can then iterate the results with a simple `foreach`, as shown in the
example at the top of this section.

### Post Objects

A post object class extends `Queulat\Post_Object` and wraps a single post,
exposing its data and any custom methods you define. The class returned by your
query's `get_decorator()` is what you get on each iteration, so you can add
domain-specific helpers that read post meta or related data.

```php
namespace My\Song;

use Queulat\Post_Object;

class Song_Post_Object extends Post_Object {
    public function get_duration(): string {
        return (string) get_post_meta( $this->ID, 'duration', true );
    }

    public function get_lyrics(): string {
        return (string) get_post_meta( $this->ID, 'lyrics', true );
    }
}
```

With the examples above, the loop from the introduction becomes:

```php
foreach ( new Song_Post_Query() as $song ) {
    echo $song->get_title();    // method provided by Post_Object
    echo $song->get_duration(); // your custom method
    echo $song->get_lyrics();   // your custom method
}
```

## Bootstrapping a plugin

The code generator produces an entry file that wires everything together. A
complete plugin looks like this (`My\Song` example):

```php
<?php
/**
 * Plugin Name: Songs Custom Post Type
 * Description: Manage songs as a custom post type.
 * Version: 0.1.0
 * License: GPL-3.0-or-later
 * Text Domain: my-song
 *
 * @package my-song
 */

declare( strict_types=1 );

namespace My\Song;

use Queulat\Helpers\Webpack_Asset_Loader;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( is_callable( array( '\Queulat\Helpers\Autoloader', 'boot' ) ) ) {
    \Queulat\Helpers\Autoloader::boot( 'My\Song\\', __DIR__ . '/includes' );
}

add_action( 'plugins_loaded', '\My\Song\bootstrap' );

register_activation_hook( __FILE__, '\My\Song\plugin_activation' );

/**
 * Initialize plugin functionality.
 */
function bootstrap() {
    $asset_loader = new Webpack_Asset_Loader(
        'my-song',
        plugin_dir_path( __FILE__ ) . 'build',
        plugins_url( 'build/', __FILE__ )
    );
    queulat_register_hooks( new Asset_Loader( $asset_loader ) );

    queulat_register_hooks( new Song_Post_Type() );

    queulat_register_hooks( new Duration_REST_Field() );
    queulat_register_hooks( new Lyrics_REST_Field() );
}

/**
 * Run plugin activation tasks (flush rewrite rules, etc.).
 *
 * @param bool $network_wide Whether the activation is network-wide.
 */
function plugin_activation( $network_wide = false ) {
    $post_type = new Song_Post_Type();
    $post_type->activate_plugin( $network_wide );
}
```

The `Asset_Loader` referenced above is a small hookable wrapper that enqueues the
built assets on the admin side. The Webpack loader itself only implements
`Queulat\Contracts\Asset_Loader_Interface`, not `Hookable_Interface`, so it is
wrapped like this:

```php
namespace My\Song;

use Queulat\Contracts\Asset_Loader_Interface;
use Queulat\Contracts\Hookable_Interface;

class Asset_Loader implements Hookable_Interface {
    public function __construct( public Asset_Loader_Interface $asset_loader ) {
    }

    public function init(): void {
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
    }

    public function admin_enqueue_scripts(): void {
        $this->asset_loader->enqueue_script( 'admin-scripts.js' );
    }
}
```

Because `Song_Post_Type`, the REST field classes and `Asset_Loader` all implement
`Queulat\Contracts\Hookable_Interface`, a single `queulat_register_hooks()` call
per component is enough to register their WordPress hooks. The REST field classes
are defined by extending `Queulat\REST_Field` (see [REST fields](rest-fields.md)).
