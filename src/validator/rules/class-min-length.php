<?php

namespace Queulat\Validator;

/**
 * Minimum length validator.
 *
 * Validates that a string value meets a specified minimum length requirement.
 *
 * @package Queulat
 * @since   0.1.0
 */
class Min_Length implements Validator_Interface {
	private $min_length = 0;
	private $encoding   = null;
	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 * @param int         $min_length Minimum required length.
	 * @param string|null $encoding   Character encoding to use for length calculation.
	 */
	public function __construct( int $min_length = 1, $encoding = null ) {
		if ( $min_length ) {
			$this->set_min_length( $min_length );
		}
		if ( $encoding ) {
			$this->set_encoding( $encoding );
		} elseif ( function_exists( 'mb_internal_encoding' ) ) {
			$this->set_encoding( mb_internal_encoding() );
		}
	}
	/**
	 * Set the minimum length.
	 *
	 * @since 0.1.0
	 * @param int $min_length Minimum required length.
	 * @return void
	 */
	public function set_min_length( $min_length ) {
		$this->min_length = (int) $min_length;
	}
	/**
	 * Set the character encoding.
	 *
	 * @since 0.1.0
	 * @param string|null $encoding Character encoding.
	 * @return void
	 */
	public function set_encoding( $encoding ) {
		$this->encoding = $encoding;
	}
	/**
	 * Check if the value is valid.
	 *
	 * @since 0.1.0
	 * @param string $value Value to validate.
	 * @return bool True if valid, false otherwise.
	 */
	public function is_valid( $value ): bool {
		$val_length = function_exists( 'mb_strlen' ) ? mb_strlen( $value, $this->encoding ) : strlen( $value );
		$val_length = (int) $val_length;
		return $val_length >= $this->min_length;
	}
	/**
	 * Get validation error message.
	 *
	 * @since 0.1.0
	 * @return string Error message.
	 */
	public function get_message(): string {
		return sprintf( _x( 'You must type at least %d characters on this field', 'queulat validator message', 'queulat' ), $this->min_length );
	}
}
