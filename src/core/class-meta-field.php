<?php
/**
 * Abstract class for meta fields
 *
 * @package Queulat
 */

namespace Queulat;

/**
 * Abstract class for meta fields
 *
 * Takes care of meta field registration the class or inherited methods
 */
abstract class Meta_Field implements Meta_Field_Interface {

	/**
	 * Register a meta field
	 */
	public function init() {
		register_meta(
			$this->get_object_type(),
			$this->get_key(),
			array(
				'object_subtype'    => $this->get_object_subtype(),
				'type'              => $this->get_type() ? $this->get_type()->value : 'string',
				'label'             => $this->get_title(),
				'description'       => $this->get_description(),
				'single'            => $this->get_single(),
				'default'           => $this->get_default(),
				'sanitize_callback' => $this->get_sanitize_callback(),
				'auth_callback'     => $this->get_auth_callback(),
				'show_in_rest'      => $this->get_show_in_rest(),
				'revisions_enabled' => $this->get_revisions_enabled(),
			)
		);
	}
}
