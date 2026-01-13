<?php
/**
 * Contract for Queulat post objects.
 *
 * @package Queulat
 */

declare(strict_types=1);

namespace Queulat\Contracts;

/**
 * Describes the public API that custom post objects must expose.
 */
interface Post_Object_Interface {

	/**
	 * Get the underlying WP_Post instance.
	 *
	 * @return \WP_Post
	 */
	public function get_post(): \WP_Post;

	/**
	 * Retrieve a post meta field with multiple values.
	 *
	 * @param string $key Meta key name.
	 * @return array
	 */
	public function get_multiple( string $key ): array;

	/**
	 * Retrieve a post meta field with a single value.
	 *
	 * @param string $key Meta key name.
	 * @return string
	 */
	public function get_single( string $key ): string;

	/**
	 * Return the post thumbnail HTML.
	 *
	 * @param string $size Image size.
	 * @param array  $attr Image attributes.
	 * @return string
	 */
	public function get_thumbnail( string $size = 'post-thumbnail', array $attr = array() ): string;
}
