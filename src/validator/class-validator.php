<?php

namespace Queulat;

use Queulat\Validator\Validator_Interface;

class Validator {
	protected $data;
	protected $rules;
	protected $errors    = array();
	protected $is_valid  = false;
	protected $validated = false;

	/**
	 * @param array|\ArrayIterator $data The data that will be validated; keys are input names, values are input values
	 * @param array|\ArrayIterator $rules A set of validation rules
	 */
	public function __construct( $data, $rules ) {

		$this->data  = $data;
		$this->rules = $rules;
	}

	/**
	 * @return bool
	 */
	public function is_valid(): bool {

		if ( $this->validated ) {
			return $this->is_valid;
		}

		$this->validate_loop();

		if ( empty( $this->errors ) ) {
			$this->is_valid = true;
		}

		return $this->is_valid;
	}

	/**
	 * Execute validation for all rules and data.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	protected function validate_loop() {
		if ( $this->validated ) {
			return;
		}

		foreach ( $this->rules as $key => $val ) {
			if ( is_array( $val ) ) {
				foreach ( $val as $validator ) {
					$this->validate_data( $key, $this->data[ $key ] ?? null, $validator );
				}
			} else {
				$this->validate_data( $key, $this->data[ $key ] ?? null, $val );
			}
		}

		$this->validated = true;
	}

	/**
	 * Validate a single piece of data against a validator.
	 *
	 * @since 0.1.0
	 * @param string              $name      Field name.
	 * @param mixed               $value     Field value.
	 * @param Validator_Interface $validator Validator instance.
	 * @return void
	 */
	protected function validate_data( $name, $value, Validator_Interface $validator ) {
		if ( ! $validator->is_valid( $value ) ) {
			$this->errors[ $name ] = $validator->get_message();
		}
	}

	/**
	 * @return bool
	 */
	public function is_invalid(): bool {
		return ! $this->is_valid();
	}

	/**
	 * @return array
	 */
	public function get_error_messages(): array {
		return $this->errors;
	}
}
