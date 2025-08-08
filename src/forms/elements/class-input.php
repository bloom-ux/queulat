<?php

namespace Queulat\Forms\Element;

use Queulat\Forms\Node_Interface;
use Queulat\Forms\HTML_Form_Element;

/**
 * Input element class.
 *
 * Represents an HTML input element with various types and attributes.
 * Extends the base HTML form element functionality.
 *
 * @package Queulat
 * @since   0.1.0
 */
class Input extends HTML_Form_Element {
	protected static $type = 'text';
	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 * @param array  $properties   Element properties.
	 * @param string $text_content Element text content.
	 */
	public function __construct( array $properties = array(), $text_content = '' ) {
		$this->set_attribute( 'type', static::$type );
		parent::__construct( $properties, $text_content );
	}
	/**
	 * Get the HTML tag name.
	 *
	 * @since 0.1.0
	 * @return string Tag name.
	 */
	public function get_tag_name() : string {
		return 'input';
	}

	/**
	 * @inheritDoc
	 */
	public static function get_element_attributes() : array {
		return [
			'accept',
			'alt',
			'autocomplete',
			'autofocus',
			'checked',
			'disabled',
			'form',
			'formaction',
			'formenctype',
			'formmethod',
			'formnovalidate',
			'formtarget',
			'height',
			'list',
			'max',
			'maxlength',
			'min',
			'multiple',
			'name',
			'pattern',
			'placeholder',
			'readonly',
			'required',
			'size',
			'src',
			'step',
			'type',
			'value',
			'width',
		];
	}
	/**
	 * Set the input value.
	 *
	 * @since 0.1.0
	 * @param mixed $value Input value.
	 * @return Node_Interface Current instance for chaining.
	 */
	public function set_value( $value ) : Node_Interface {
		$this->set_attribute( 'value', $value );
		return $this;
	}
	/**
	 * Get the input value.
	 *
	 * @since 0.1.0
	 * @return mixed Input value.
	 */
	public function get_value() {
		return $this->get_attribute( 'value' );
	}
	/**
	 * @inheritDoc
	 */
	public function __toString() : string {
		return '<input ' . $this->render_attributes() . '>';
	}
}
