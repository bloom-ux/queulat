<?php

namespace Queulat\Forms;

/**
 * Form control trait.
 *
 * Provides common functionality for form controls including label and name handling.
 * This trait should be used by form elements that need label and name properties.
 *
 * @package Queulat
 * @since   0.1.0
 */
trait Form_Control_Trait {
	protected $label = '';
	protected $name  = '';

	/**
	 * @inheritDoc
	 * @suppress PhanTypeMismatchReturn
	 */
	public function set_label( string $label ) : Node_Interface {
		$this->label = $label;
		return $this;
	}

	/**
	 * Get the form control label.
	 *
	 * @since 0.1.0
	 * @return string The label text.
	 */
	public function get_label() : string {
		return $this->label;
	}

	/**
	 * @inheritDoc
	 * @suppress PhanTypeMismatchReturn
	 */
	public function set_name( string $name ) : Node_Interface {
		if ( $this instanceof Attributes_Interface ) {
			$this->set_attribute( 'name', $name );
		} else {
			$this->name = $name;
		}
		return $this;
	}

	/**
	 * Get the form control name.
	 *
	 * @since 0.1.0
	 * @return string The control name.
	 */
	public function get_name() : string {
		return $this instanceof Attributes_Interface ? $this->get_attribute( 'name' ) : $this->name;
	}
}
