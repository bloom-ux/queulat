<?php
/**
 * Base class for REST fields
 *
 * @package Queulat
 */

namespace Queulat;

use WP_User;
use WP_Post;
use WP_Term;
use WP_Error;
use WP_REST_Request;

/**
 * Abstract class for a REST field
 */
abstract class REST_Field implements REST_Field_Interface {

	/**
	 * Hook into WordPress.
	 */
	public function init() {
		add_action( 'rest_api_init', array( $this, 'register' ) );
	}

	/**
	 * Handle registration on the API.
	 */
	public function register() {
		register_rest_field(
			$this->get_object_type(),
			$this->get_attribute(),
			array(
				'get_callback' => $this->get_value_callback(),
				'update_callback' => $this->get_update_callback(),
				'schema' => $this->get_schema(),
			)
		);
	}

	/**
	 * Generate basic schema.
	 *
	 * For more complex use cases, extend this method on your implementing class.
	 *
	 * @return ?array Params for JSON schema or null
	 */
	public function get_schema(): ?array {
		return array(
			'type' => $this->get_type()->value,
			'arg_options' => array(
				'description' => $this->get_description(),
				'sanitize_callback' => $this->get_sanitize_callback(),
				'validate_callback' => $this->get_validate_callback(),
			),
		);
	}

	/**
	 * Get the callback function used to retrieve the field value.
	 *
	 * @return callable|null The callback function that will be called to get the field value, or null if not set.
	 */
	public function get_value_callback(): ?callable {
		return array( $this, 'value_callback' );
	}

	/**
	 * Get the callback function used to sanitize the field value.
	 *
	 * @return callable|null The callback function that will be called to sanitize input values, or null if not set.
	 */
	public function get_sanitize_callback(): ?callable {
		return array( $this, 'sanitize_callback' );
	}

	/**
	 * Get the callback function used to validate the field value.
	 *
	 * @return callable|null The callback function that will be called to validate input values, or null if not set.
	 */
	public function get_validate_callback(): ?callable {
		return array( $this, 'validate_callback' );
	}

	/**
	 * Get the callback function used to update the field value.
	 *
	 * @return callable|null The callback function that will be called to update/save field values, or null if not set.
	 */
	public function get_update_callback(): ?callable {
		return array( $this, 'update_callback' );
	}

	/**
	 * Method for getting the field value from database or else.
	 *
	 * @param array           $response_data The response data array for the object being retrieved.
	 * @param string          $field_name The name of the field being requested.
	 * @param WP_REST_Request $request The current REST API request object.
	 * @param string          $object_type The type of object being retrieved (post, user, term, etc.).
	 * @return mixed The field value to be included in the REST response.
	 */
	abstract public function value_callback( array $response_data, string $field_name, WP_REST_Request $request, string $object_type ): mixed;

	/**
	 * Method for sanitizing the field data received by the API.
	 *
	 * @param mixed           $value The raw value submitted via the REST API.
	 * @param WP_REST_Request $request The current REST API request object.
	 * @param string          $field_name The name of the field being sanitized.
	 * @return mixed|WP_error The sanitized value ready for further processing or WP_Error.
	 */
	abstract public function sanitize_callback( mixed $value, WP_REST_Request $request, string $field_name );

	/**
	 * Method for saving or updating the field.
	 *
	 * @param mixed                   $field_value The sanitized field value to be saved.
	 * @param WP_Post|WP_Term|WP_User $data_object The WordPress object being updated.
	 * @param mixed                   $field_name The name of the field being updated.
	 * @param WP_REST_Request         $request The current REST API request object.
	 * @return true|WP_Error True on successful update, WP_Error on failure.
	 */
	abstract public function update_callback( $field_value, WP_Post|WP_Term|WP_User $data_object, $field_name, WP_REST_Request $request ): true|WP_Error;

	/**
	 * Method for validating the value received by the API.
	 *
	 * @param mixed           $value The value to be validated.
	 * @param WP_REST_Request $request The current REST API request object.
	 * @param string          $field_name The name of the field being validated.
	 * @return bool|WP_Error True if valid, false or WP_Error if validation fails.
	 */
	abstract public function validate_callback( mixed $value, WP_REST_Request $request, string $field_name ): bool|WP_Error;
}
