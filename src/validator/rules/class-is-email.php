<?php

namespace Queulat\Validator;

/**
 * Email validation rule.
 *
 * Validates that a value is a valid email address using WordPress's is_email() function.
 *
 * @package Queulat
 * @since   0.1.0
 */
class Is_Email implements Validator_Interface {
	/**
	 * Check if the value is a valid email address.
	 *
	 * @since 0.1.0
	 * @param string $value Value to validate.
	 * @return bool True if valid, false otherwise.
	 */
	public function is_valid( $value ) : bool {
		return is_email( $value );
	}
	/**
	 * Get validation error message.
	 *
	 * @since 0.1.0
	 * @return string Error message.
	 */
	public function get_message() : string {
		return __( 'Please enter a valid e-mail address', 'queulat' );
	}
}
