<?php

namespace Queulat\Validator;

/**
 * Maximum length validator.
 *
 * Validates that a string value does not exceed a specified maximum length.
 *
 * @package Queulat
 * @since   0.1.0
 */
class Max_Length implements Validator_Interface {
	private $max_length = 0;
	private $encoding   = null;
	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 * @param int         $max_length Maximum allowed length.
	 * @param string|null $encoding   Character encoding to use for length calculation.
	 */
	public function __construct( int $max_length = 0, $encoding = null ) {
		if ( $max_length ) {
			$this->set_max_length( $max_length );
		}
		if ( $encoding ) {
			$this->set_encoding( $encoding );
		} elseif ( function_exists( 'mb_internal_encoding' ) ) {
			$this->set_encoding( mb_internal_encoding() );
		}
	}
	/**
	 * Set the maximum length.
	 *
	 * @since 0.1.0
	 * @param int $max_length Maximum allowed length.
	 * @return void
	 */
	public function set_max_length( $max_length ) {
		$this->max_length = (int) $max_length;
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
		return $val_length <= $this->max_length;
	}
	/**
	 * Get validation error message.
	 *
	 * @since 0.1.0
	 * @return string Error message.
	 */
	public function get_message(): string {
		return sprintf( _x( 'You can only type up to %d characters on this field', 'queulat validator message', 'queulat' ), $this->max_length );
	}
}
