<?php
/**
 * Interface for Meta fields
 *
 * @package Queulat
 */

namespace Queulat;

/**
 * Interface for WordPress meta field
 */
interface Meta_Field_Interface {

	/**
	 * The object type for the meta, such as 'post', 'comment', 'term', 'user'
	 *
	 * @return string
	 */
	public function get_object_type(): string;

	/**
	 * The specific subtype of object, such as custom post type slug
	 *
	 * @return string
	 */
	public function get_object_subtype(): string;

	/**
	 * The meta field key
	 *
	 * @return string
	 */
	public function get_key(): string;

	/**
	 * Meta data type
	 *
	 * @return Meta_Type
	 */
	public function get_type(): Meta_Type;

	/**
	 * Human-friendly, short meta title
	 *
	 * @return string
	 */
	public function get_title(): string;

	/**
	 * Meta description
	 *
	 * @return string
	 */
	public function get_description(): string;

	/**
	 * Indicate whether the field has a single or multiple values
	 *
	 * @return bool True if the meta has a single value
	 */
	public function get_single(): bool;

	/**
	 * Whether the meta should have revisions enabled
	 *
	 * Currently, only "post" objects can have revisions enabled.
	 * Custom post types with revisions can't have revisions enabled meta.
	 *
	 * @return bool
	 */
	public function get_revisions_enabled(): bool;

	/**
	 * The meta field default value
	 *
	 * The default value must match the meta field type
	 *
	 * @return mixed
	 */
	public function get_default();

	/**
	 * Get a sanitization callback to apply to the meta field value
	 *
	 * @return callable
	 */
	public function get_sanitize_callback(): callable;

	/**
	 * Get an authorization callback when updating the meta field
	 *
	 * @return callable
	 */
	public function get_auth_callback(): callable;

	/**
	 * Define whether to show the meta field on the REST API
	 *
	 * Can be a simple boolean or an array with "schema" and "prepare_callback" for complex values
	 *
	 * @return bool|array
	 */
	public function get_show_in_rest(): bool|array;
}
