<?php
/**
 * Contract for asset loader
 *
 * @package Queulat
 */

declare(strict_types=1);

namespace Queulat\Contracts;

/**
 * Asset loader interface
 */
interface Asset_Loader_Interface {

	/**
	 * Enqueue a registered script by handle with optional dependencies.
	 *
	 * @param string $handle Script handle.
	 * @param array  $deps  Optional dependency handles.
	 */
	public function enqueue_script( string $handle, array $deps = array() ): void;

	/**
	 * Enqueue a registered style by handle with optional dependencies.
	 *
	 * @param string $handle Style handle.
	 * @param array  $deps  Optional dependency handles.
	 */
	public function enqueue_style( string $handle, array $deps = array() ): void;

	/**
	 * Register a script path and return its handle, optionally specifying dependencies.
	 *
	 * @param string $path   Script file path.
	 * @param string $handle Optional handle override.
	 * @param array  $deps  Optional dependency handles.
	 * @return string Registered script handle.
	 */
	public function register_script( string $path, string $handle = '', array $deps = array() ): string;

	/**
	 * Register a style path and return its handle, optionally specifying dependencies.
	 *
	 * @param string $path   Style file path.
	 * @param string $handle Optional handle override.
	 * @param array  $deps  Optional dependency handles.
	 * @return string Registered style handle.
	 */
	public function register_style( string $path, string $handle = '', array $deps = array() ): string;
}
