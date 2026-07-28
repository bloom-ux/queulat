# Meta boxes

Meta boxes let you build simpler, focused data-entry interfaces for your post types — as opposed to the block editor, which is oriented toward designing content layout. You create them by extending the included `Queulat\Metabox` abstract class.

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

Check [Using Queulat Forms](forms.md) for more info on available form fields or how to create your own.

# Creating admin pages

`@todo`
