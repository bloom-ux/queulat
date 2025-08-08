<?php

namespace Queulat\Forms;

/**
 * Abstract base class for form components.
 *
 * Provides common functionality for form components that implement multiple interfaces.
 * Components are UI elements that don't support global HTML attributes but can hold
 * text content and properties.
 *
 * @package Queulat
 * @since   0.1.0
 */
abstract class Component implements Component_Interface, Form_Node_Interface, Properties_Interface {

	protected $text_content = '';

	use Form_Control_Trait;
	use Properties_Trait;
	use Childless_Node_Trait;
	use Attributes_Trait;

	/**
	 * @inheritDoc
	 * @internal Components are not supposed to have global attributes
	 */
	public static function get_global_attributes(): array {
		return array();
	}

	/**
	 * @inheritDoc
	 */
	public function set_text_content( string $text ): Node_Interface {
		$this->text_content = $text;
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_text_content(): string {
		return $this->text_content;
	}
}
