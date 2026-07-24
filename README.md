# Installation

Install with [Composer](https://getcomposer.org/):

`composer require felipelavinz/queulat:dev-master`

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

# Leveraging WordPress architecture with Queulat

Queulat it's aimed at *improving the way we create things for WordPress*, so instead of fundamentally transforming it, it tries to use familiar concepts to build better-structured things for it, using *custom post types*, *custom post queries* and *custom post objects*.

You can generate these using its own *custom post type plugin generator*, which is available to admin users on the "Tools" menu.

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

`@todo`

### Post Queries

`@todo`

### Post Objects

`@todo`

## Generating plugins

You can scaffold a custom post type plugin either from the admin generator
(available to admin users under the "Tools" menu) or from the command line
using [WP-CLI](https://wp-cli.org/).

Queulat registers two commands:

* `wp queulat generate cpt-plugin <Class_Name>` — scaffolds a full custom post
  type plugin, including the post type, query and object classes, plus an entry
  file that boots Queulat's autoloader for the generated namespace.
* `wp queulat generate rest-field <Class_Name>` — scaffolds a `Queulat\REST_Field`
  subclass that can extend a WordPress REST API endpoint.

Both commands accept a `--namespace` flag to control the generated namespace
(defaults to `Queulat\CPT` for CPT plugins and `Queulat\REST` for REST fields).

Generated plugins place their PHP classes under an `includes/` directory and use
`Queulat\Helpers\Autoloader` to load them (see "Autoloading" above).

## REST fields

Queulat can extend the WordPress REST API with custom fields via the
`Queulat\REST_Field` abstract class, which implements both
`Queulat\Contracts\REST_Field_Interface` and `Queulat\Contracts\Hookable_Interface`.

Extend it and implement the abstract methods:

* `value_callback( array $response_data, string $field_name, WP_REST_Request $request, string $object_type ): mixed` — returns the field value for responses.
* `sanitize_callback( mixed $value, WP_REST_Request $request, string $field_name )` — sanitizes incoming values.
* `validate_callback( mixed $value, WP_REST_Request $request, string $field_name ): bool|WP_Error` — validates incoming values.
* `update_callback( $field_value, WP_Post|WP_Term|WP_User $data_object, $field_name, WP_REST_Request $request ): true|WP_Error` — persists submitted values.

The base class also exposes `get_type()`, `get_object_type()`, `get_attribute()`,
`get_schema()` and `get_description()` to describe the field, and its `init()`
method (from `Hookable_Interface`) registers the field with the REST API.

```php
use Queulat\REST_Field;

class Song_Duration_Field extends REST_Field {
    public function get_object_type() : string {
        return 'post';
    }
    public function get_attribute() : string {
        return 'song_duration';
    }
    // ... implement value_callback, sanitize_callback, validate_callback, update_callback
}

// Register it:
$field = new Song_Duration_Field();
$field->init();
```

## Meta boxes

Presently, the primary way to define meta data on post objects it's creating meta boxes; extending the included `Queulat\Metabox` abstract class.

The extending class *must* implement the abstract methods: `get_fields( ) : array` and `sanitize_data( array $data ) : array`; for instance:

```php
<?php

use Queulat\Metabox;
use Queulat\Forms\Node_Factory;
use Queulat\Forms\Element\Input_Text;

class Track_Meta_Box extends Metabox {
	/**
	 * Must return the list of form fields to be included on this meta box
	 *
	 * @return Queulat\Forms\Node_Interface[] Array of form fields.
	 **/
	public function get_fields() : array {
		return array(
			Node_Factory::make(
				Input_Text::class,
				array(
					'name'                   => 'length',
					'label'                  => 'Track length',
					'attributes.class'       => 'regular-text',
					'properties.description' => _x( 'Track duration, such as: 01:23:45 (1 hour, 23 minutes, 45 seconds)', 'length field description', 'track_cpt' ),
				)
			),
			Node_Factory::class(
				Input_Checkbox::class,
				array(
					'name'  => 'colour',
					'label' => 'Colour',
					'options' => array(
						'red'   => 'Red',
						'green' => 'Green',
						'blue'  => 'Blue',
						'any'   => 'Any colour'
					)
				)
			)
		);
	}

	/**
	 * Sanitize data from the metabox form.
	 *
	 * @param array $data Form data from the meta box (not the full $_POST).
	 * @return array Sanitized data.
	 **/
	public function sanitize_data( array $data ) : array {
		return queulat_sanitizer( $data, array(
			'length'   => array( 'sanitize_text' ),
			'colour.*' => array( 'sanitize_key' )
		) );
	}
}
```

Queulat takes care of updating the submitted data as post meta fields, and loading the saved values on the meta box form.

For sanitizing data, you could use whatever method you prefer. The referenced `queulat_sanitizer()` function it's a pretty simple way to apply callbacks to the matching values from the `$data` input. You can use `*` to match all properties (using dot notation).

Check the section on (using Queulat forms)[#using-queulat-forms] for more info on available form fields or how to create your own.

# Creating admin pages

# Using Queulat Forms

`@todo: add general description`

## Available form fields

`@todo: add description for each form field`

* Button
* Div
* Fieldset
* Form
* Google_Map
* Input
	- Input_Text
	- Input_Hidden
	- Input_Email
	- Input_Checkbox
	- Input_Number
	- Input_Radio
	- Input_Submit
	- Input_Url
* Recaptcha
* Select
* Textarea
* UI_Select2
* WP_Editor
* WP_Gallery
* WP_Image
* WP_Media
* WP_Nonce
* Yes_No

## Validating forms

Queulat provides a small `Queulat\Validator` together with a set of reusable
validation rules under `Queulat\Validator\Rules`. Each rule implements
`Queulat\Validator\Validator_Interface` and exposes a `validate( $value ): bool`
method.

Available rules:

* `Is_Email` — validates an email address.
* `Is_Boolean` — validates a boolean-ish value.
* `Is_Required` — ensures a value is present.
* `Min_Length` / `Max_Length` — enforce string length bounds.
* `Value_In` / `Value_Not_In` — restrict a value to (or away from) a set of allowed values.
* `Valid_Recaptcha` — verifies a Google reCAPTCHA response.

You can validate a single value against one or more rules, or use the validator
inside a metabox `sanitize_data()` method alongside `queulat_sanitizer()`.

```php
use Queulat\Validator\Validator;
use Queulat\Validator\Rules\Is_Email;
use Queulat\Validator\Rules\Is_Required;

$validator = new Validator( array( new Is_Required(), new Is_Email() ) );
$validator->validate( $email ); // bool
```

## Creating new form views

`@todo`

## Instantiating form elements with Node_Factory

`Node_Factory`it's a simple factory class that's able to create any kind of form element.

It exposes a single `make` method that can instantiate and configure an element. This method takes two parameters:

1. An "element name" as string, which should be a fully qualified name for a form element or component.
2. An associative array of "arguments" that are used to configure the object.

By default, Queulat is configured to handle the following attributes:

* attributes: HTML element attributes, such as class, id, type, etc; as associative array.
* children: nested elements, which can also be created with the `Node_Factory` as an array.
* label: the text labeling a form element.
* name: the field "name" that's used on form submission.
* options: an associative array of element options (for fields such as input radios, checkboxes, selects, etc.)
* properties: an array of arbitrary node properties which can be used by the form view or whatever, as an associative array.
* text_content: the textual content of the node.
* value: the form field value.

Arguments that are not supported by the element are skipped.

You can extend the supported arguments using `Node_Factory::register_argument()`.

### Usage

```php
<?php

use Queulat\Forms\Element\Div;
use Queulat\Forms\Node_Factory;
use Queulat\Forms\Element\Button;

$submit = Node_Factory::make(
	Div::class, array(
		'attributes' => array(
			'class'  => 'col-md-4 col-md-offset-8',
			'id'     => 'form-buttons'
		),
		'text_content' => '* required field',
		'children'     => array(
			Node_Factory::make(
				Button::class,
				'attributes' => array(
					'class'  => 'btn-lg',
					'type'   => 'submit'
				),
				'text_content' => 'Submit'
			)
		)
	);
);

echo $submit;
```

## Node_Factory

### Registering new argument handlers

You can register new arguments used by the Node_Factory using the `register_argument` method.

This method takes a `Node_Factory_Argument_Handler`, which needs:

* An `$argument` (string) which is the name of the argument key that you'll handle.
* A `$method` (string) which is the name of the method that will receive the parameters used on the factory method.
* An optional `$call_type` (`Queulat\Forms\Node_Factory_Call_Type`) which determines how the $method will treat the received configuration values.

The `$call_type` can be one of:

`Node_Factory_Call_Type::VALUE`: pass all arguments as a single array to the handler. This is the default setting. Example: `$obj->$method( $args );`

`Node_Factory_Call_Type::ARRAY`: pass arguments as individual parameters to the handler. Example: `call_user_func_array( [ $obj, $method ], $args );`

`Node_Factory_Call_Type::KEY_VALUE`: for each item in the argument, pass its key and value as parameters to the handler. Example:

```php
foreach ( $args as $key => $val ) {
    $obj->$method( $key, $val );
}
```

`Node_Factory_Call_Type::VALUE_ITEMS`: for each item in the argument, use the value as parameter for the handler. Example: `array_walk( $args, [ $obj, $method ] );`

# Interfaces

## Contracts

Queulat defines explicit contracts under the `Queulat\Contracts` namespace for
its main building blocks. Implementing these interfaces lets your classes plug
into Queulat's helpers and generators:

* `Post_Type_Interface` — the public API expected from post type definitions.
* `Post_Query_Interface` — the API for custom post queries.
* `Post_Object_Interface` — the API for custom post objects.
* `Metabox_Interface` — the API for metabox definitions.
* `REST_Field_Interface` — the API for REST API field extensions.
* `Asset_Loader_Interface` — the API for asset loaders (see "Loading assets" below).
* `Hookable_Interface` — any class that can register its own WordPress hooks via an `init()` method. `Queulat\Post_Type` and `Queulat\REST_Field` implement it.

### Registering hooks for hookable components

The `queulat_register_hooks()` helper calls `init()` on any `Hookable_Interface`
instance, which is the recommended way to wire a component's actions and filters:

```php
use function Queulat\queulat_register_hooks;

queulat_register_hooks( new My_Post_Type() );
```

## Loading assets

Queulat enqueues its admin assets through an implementation of
`Queulat\Contracts\Asset_Loader_Interface`. The bundled
`Queulat\Helpers\Webpack_Asset_Loader` reads a Webpack `manifest.json` /
`entrypoints.json` and exposes `enqueue_script( $handle )`, `enqueue_style( $handle )`,
`register_script( $path, $handle, $deps )` and `register_style( $path, $handle, $deps )`.

Provide your own loader by implementing `Asset_Loader_Interface` and passing it to
`Queulat\Bootstrap`:

```php
$loader   = new My_Asset_Loader( __DIR__ . '/dist' );
$bootstrap = new Queulat\Bootstrap( $loader );
$bootstrap->init();
```

## Node_Interface

| Related Interfaces | Related Traits |
| ------------------ | -------------- |
| `Component_Interface` | `Node_Trait` |
| `Element_Interface` | `Childless_Node_Trait` |

Nodes are the lowest level of objects that should be used with forms

Use `Node_Trait` to help implement this interface or `Childless_Node_Trait`. In general terms,
elements should use the former and components the latter.

### Component_Interface

Extends the Node_Interface and Attributes_Interface.

### Element_Interface

Extends the Node_Interface and Attributes_Interface. Also, adds the `get_tag_name` method.

#### HTML_Element_Interface

Extends the Element_Interface and Properties_Interface.

##### Form_Element_Interface

Extends HTML_Element_Interface and Form_Node_Interface.

## Attributes_Interface

"Attributes" are special properties used by objects implementing this interface. They're rendered as HTML attributes `key="val"`

Use `Attributes_Trait` to help implement this interface.

## Form_Node_Interface

Objects implementing this interface (elements or components) are used as form objects. They have a "name" which is used to send data to the server, a "value" and a "label".

The `Form_Control_Trait` helps implementing the "label" and "name" getters and setters from this interface.
The "value" getter and setter should be defined by your own custom component.

## Node_List_Interface

Extends ArrayAccess, SeekableIterator, Countable, Serializable.

Most commonly used to get an array-like set of children from a Node.

## Option_Node_Interface

Used by controls such as checkboxes, radios, selects and every component where the user is presented with several alternatives they can choose from.

Use `Options_Trait` to help implement this interface.

## Properties_Interface

Node properties (not to be confused with regular object properties) can store arbitrary data, such as view settings, error data or validation state.

Use `Properties_Trait` to help implement this interface.

## View_Interface

Base interface to be used by form views.
