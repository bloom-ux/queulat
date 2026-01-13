<?php
/**
 * Contract for Queulat custom post types.
 *
 * @package Queulat
 */

declare(strict_types=1);

namespace Queulat\Contracts;

/**
 * Interface describing the public API required by Queulat\Post_Type descendants.
 */
interface Post_Type_Interface {

	/**
	 * Register actions and filters needed by the post type.
	 *
	 * @return void
	 */
	public function init(): void;

	/**
	 * Register the post type with WordPress.
	 *
	 * @return \WP_Post_Type|\WP_Error
	 */
	public function register();

	/**
	 * Get the post type slug/key.
	 *
	 * @return string
	 */
	public function get_post_type(): string;

	/**
	 * Get the arguments used to register the post type.
	 *
	 * @return array
	 */
	public function get_post_type_args(): array;

	/**
	 * Get the associated WP_Post_Type object if available.
	 *
	 * @return null|\WP_Post_Type
	 */
	public function get_post_type_object(): ?\WP_Post_Type;
}
