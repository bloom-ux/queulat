<?php

namespace Queulat\Forms\Element;

use Queulat\Forms\HTML_Element;

/**
 * Div HTML element class.
 *
 * Represents an HTML div element that can contain child elements and text content.
 * Extends the base HTML element functionality.
 *
 * @package Queulat
 * @since   0.1.0
 */
class Div extends HTML_Element {
	/**
	 * @inheritDoc
	 */
	public function get_tag_name() : string {
		return 'div';
	}

	/**
	 * @inheritDoc
	 */
	public function __toString() : string {
		$out      = '<' . $this->get_tag_name() . $this->render_attributes() . '>';
			$out .= $this->get_text_content();
		foreach ( $this->get_children() as $child ) {
			$out .= (string) $child;
		}
		$out .= '</' . $this->get_tag_name() . '>';
		return $out;
	}
}
