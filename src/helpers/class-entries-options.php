<?php
/**
 * Class Entries_Options
 *
 * Provides an iterable list of post entries as options, useful for select fields.
 *
 * @package Queulat
 */

namespace Queulat\Helpers;

/**
 * Build Entries_Options for select fields
 */
class Entries_Options extends \ArrayIterator {
	/**
	 * A WP_Query for building the options
	 *
	 * @var \WP_Query
	 */
	private $query;

	/**
	 * The current post in the loop
	 *
	 * @var \WP_Post
	 */
	private $current_post;

	/**
	 * Constructor.
	 *
	 * @param array $params Query arguments for WP_Query.
	 * @param array $flags  Additional flags, e.g., 'show_option_none'.
	 */
	public function __construct( array $params = array(), array $flags = array() ) {
		$args        = wp_parse_args(
			$params,
			array(
				'post_status'    => 'publish',
				'orderby'        => 'title',
				'order'          => 'ASC',
				'posts_per_page' => -1,
			)
		);
		$flags       = wp_parse_args(
			$flags,
			array(
				'show_option_none' => false,
			)
		);
		$this->query = new \WP_Query( $args );
		if ( isset( $flags['show_option_none'] ) && $flags['show_option_none'] ) {
			array_unshift(
				$this->query->posts,
				(object) array(
					'ID'         => '',
					'post_title' => is_string( $flags['show_option_none'] ) ? $flags['show_option_none'] : _x( '(None)', 'null entry option', 'queulat' ),
				)
			);
		}
		parent::__construct( $this->query->posts );
	}

	/**
	 * Get the current post title.
	 *
	 * @return string
	 */
	#[\ReturnTypeWillChange]
	public function current() {
		$this->current_post = parent::current();
		return $this->current_post->post_title;
	}

	/**
	 * Get the current post ID.
	 *
	 * @return int|string
	 */
	#[\ReturnTypeWillChange]
	public function key() {
		$key = parent::key();
		return $this->query->posts[ $key ]->ID;
	}

	/**
	 * Get an array copy of the options as [ID => post_title].
	 *
	 * @return array
	 */
	#[\ReturnTypeWillChange]
	public function getArrayCopy() {
		return wp_list_pluck( $this->query->posts, 'post_title', 'ID' );
	}
}
