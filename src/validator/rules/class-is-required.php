<?php

namespace Queulat\Validator;

/**
 * Required field validation rule.
 *
 * Validates that a value is not empty using PHP's empty() function.
 *
 * @package Queulat
 * @since   0.1.0
 */
class Is_Required implements Validator_Interface {
	/**
	 * Check if the value is not empty.
	 *
	 * @since 0.1.0
	 * @param mixed $value Value to validate.
	 * @return bool True if valid, false otherwise.
	 */
	public function is_valid( $value ): bool {
		return ! empty( $value );
	}

	/**
	 * Get validation error message.
	 *
	 * @since 0.1.0
	 * @return string Error message.
	 */
	public function get_message(): string {
		return __( 'Please complete this field', 'queulat' );
	}
}
