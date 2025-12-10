<?php
/**
 * Contract for Queulat metaboxes.
 *
 * @package Queulat
 */

declare(strict_types=1);

namespace Queulat\Contracts;

use Queulat\Forms\Form_Node_Interface;

/**
 * Describes the expected API for metabox implementations.
 */
interface Metabox_Interface {

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function init();

	/**
	 * Return the identifier for the metabox.
	 *
	 * @return string
	 */
	public function get_id();

	/**
	 * Get the metabox title.
	 *
	 * @return string
	 */
	public function get_title();

	/**
	 * Get the post type the metabox applies to.
	 *
	 * @return string
	 */
	public function get_post_type();

	/**
	 * Retrieve any registered arguments for the metabox.
	 *
	 * @return array
	 */
	public function get_args();

	/**
	 * Provide the form fields rendered in the metabox.
	 *
	 * @return Form_Node_Interface[]
	 */
	public function get_fields(): array;

	/**
	 * Sanitize submitted data before saving.
	 *
	 * @param array $data Raw input data.
	 * @return array
	 */
	public function sanitize_data( array $data ): array;
}
