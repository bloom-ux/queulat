<?php

namespace Queulat;

/**
 * Singleton pattern trait.
 *
 * Provides singleton functionality to classes that use this trait.
 * Ensures only one instance of the class is created.
 *
 * @package Queulat
 * @since   0.1.0
 */
trait Singleton {
	protected static $instance = null;

	/**
	 * Return instantiated class
	 * @return static
	 */
	public static function get_instance() {
		if ( is_null( static::$instance ) ) {
			$class            = get_called_class();
			static::$instance = new $class();
		}
		return static::$instance;
	}
	/**
	 * Constructor.
	 *
	 * Protected constructor to prevent direct instantiation.
	 * Use get_instance() method instead.
	 *
	 * @since 0.1.0
	 */
	public function __construct() {
	}
}
