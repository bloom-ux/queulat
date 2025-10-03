<?php
/**
 * Base class for post meta fields
 *
 * @package Queulat
 */

namespace Queulat;

use WP_Error;

/**
 * Base class for registering post meta fields
 */
abstract class Post_Meta_Field extends Meta_Field implements Meta_Field_Interface {

	/**
	 * Get the meta object type
	 *
	 * @return string 'post'
	 */
	final public function get_object_type(): string {
		return 'post';
	}

	/**
	 * Whether revisions should be enabled for the meta field
	 *
	 * False by default, because currently it's not possible to have meta fields revisions
	 *
	 * @return bool
	 */
	public function get_revisions_enabled(): bool {
		return false;
	}

	/**
	 * Get the sanitization callback
	 *
	 * @return callable
	 */
	public function get_sanitize_callback(): callable {
		return array( $this, 'sanitize_callback' );
	}

	/**
	 * Get an authorization callback
	 *
	 * @return callable
	 */
	public function get_auth_callback(): callable {
		return array( $this, 'auth_callback' );
	}

	/**
	 * Sanitize meta field data
	 *
	 * @param mixed  $meta_value Raw meta value.
	 * @param string $meta_key The name of the meta field.
	 * @param string $object_type Name of the object type.
	 * @param string $object_subtype Name of the object sub-type (custom post type).
	 * @return mixed Sanitized value
	 */
	abstract public function sanitize_callback( $meta_value, $meta_key, $object_type, $object_subtype );

	/**
	 * Authorization callback for updating this meta field
	 *
	 * @param bool   $allowed Whether the user is allowed to update the field.
	 * @param string $meta_key Name of the meta field.
	 * @param int    $object_id ID of the editing object.
	 * @param int    $user_id ID of the user editing the field.
	 * @param string $cap The checked capability on the user.
	 * @param array  $caps Foo bar.
	 * @return bool|WP_Error True if allowed, false or WP_Error detailing the issue if not
	 */
	abstract public function auth_callback( $allowed, $meta_key, $object_id, $user_id, $cap, $caps ): bool|WP_Error;
}
