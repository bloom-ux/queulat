<?php
/**
 * This interface defines the contract for REST API field objects that can be
 * registered with WordPress REST API endpoints to extend their functionality.
 *
 * @package Queulat
 */

namespace Queulat;

/**
 * Interface for REST API field implementations.
 */
interface REST_Field_Interface {

	/**
	 * Get the meta type for this REST field.
	 *
	 * @return Meta_Type The field data type.
	 */
	public function get_type(): Meta_Type;

	/**
	 * Get the object type this field is registered for.
	 *
	 * @return string The object type or custom post type slug this field applies.
	 */
	public function get_object_type(): string;

	/**
	 * Get the attribute name for this REST field.
	 *
	 * @return string The attribute name that will be used in REST API responses and requests.
	 */
	public function get_attribute(): string;

	/**
	 * Get the JSON schema definition for this field.
	 *
	 * @return array|null The JSON schema array that describes the field structure, or null if no schema is defined.
	 */
	public function get_schema(): ?array;

	/**
	 * Get the human-readable description of this field.
	 *
	 * @return string A brief description explaining what this field represents and how it should be used.
	 */
	public function get_description(): string;

	/**
	 * Get the callback function used to retrieve the field value.
	 *
	 * @return callable|null The callback function that will be called to get the field value, or null if not set.
	 */
	public function get_value_callback(): ?callable;

	/**
	 * Get the callback function used to sanitize the field value.
	 *
	 * @return callable|null The callback function that will be called to sanitize input values, or null if not set.
	 */
	public function get_sanitize_callback(): ?callable;

	/**
	 * Get the callback function used to validate the field value.
	 *
	 * @return callable|null The callback function that will be called to validate input values, or null if not set.
	 */
	public function get_validate_callback(): ?callable;

	/**
	 * Get the callback function used to update the field value.
	 *
	 * @return callable|null The callback function that will be called to update/save field values, or null if not set.
	 */
	public function get_update_callback(): ?callable;
}
