<?php
/**
 * Contract for Queulat post queries.
 *
 * @package Queulat
 */

declare(strict_types=1);

namespace Queulat\Contracts;

/**
 * Public API required for custom post queries.
 */
interface Post_Query_Interface extends \Iterator, \Countable {

	/**
	 * Return the post type handled by the query.
	 *
	 * @return string
	 */
	public function get_post_type(): string;

	/**
	 * Return the decorator class used for results.
	 *
	 * @return string
	 */
	public function get_decorator(): string;

	/**
	 * Provide default query arguments.
	 *
	 * @return array
	 */
	public function get_default_args(): array;

	/**
	 * Re-use a custom WP_Query instance.
	 *
	 * @param \WP_Query $query Query instance.
	 * @return void
	 */
	public function set_query( \WP_Query $query ): void;

	/**
	 * Get the underlying WP_Query instance.
	 *
	 * @return \WP_Query
	 */
	public function get_query(): \WP_Query;

	/**
	 * Return the IDs of the posts found.
	 *
	 * @return array
	 */
	public function get_found_posts_ids(): array;
}
