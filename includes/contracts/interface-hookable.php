<?php
/**
 * Contract for classes that hook into WordPress filters or actions
 *
 * @package Queulat
 */

namespace Queulat\Contracts;

interface Hookable_Interface {

	/**
	 * Register WordPress actions and filters
	 *
	 * @return void
	 */
	public function init();
}
