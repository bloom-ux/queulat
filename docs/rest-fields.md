# REST fields

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
