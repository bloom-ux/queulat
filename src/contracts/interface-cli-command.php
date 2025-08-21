<?php
/**
 * A minimal contract for a WP-CLI command
 *
 * @package Queulat
 */

namespace Queulat\Contracts;

/**
 * Contract for creating WP-CLI commands
 */
interface CLI_Command {

	/**
	 * Get name of the command. Should use namespacing
	 *
	 * @return string Name of the command
	 */
	public function get_name(): string;

	/**
	 * Get the method or function that will process the command
	 *
	 * The function receives $args (positional) and $assoc_args from the command line.
	 *
	 * @return callable|object|string
	 */
	public function get_callable(): callable|object|string;

	/**
	 * Get command registration arguments
	 *
	 * @return array Associative array with additional params.
	 * @see WP_CLI::add_command()
	 */
	public function get_args(): array;
}
