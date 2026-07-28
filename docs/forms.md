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
	)
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
