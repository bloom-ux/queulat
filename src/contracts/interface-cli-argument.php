<?php
/**
 * Contract for WP-CLI Arguments.
 *
 * @package Queulat
 */

namespace Queulat\Contracts;

use JsonSerializable;
use Queulat\CLI_Argument_Types;

/**
 * Contract for CLI argument objects
 */
interface CLI_Argument extends JsonSerializable {

	/**
	 * Get the type of the argument.
	 *
	 * @return CLI_Argument_Types
	 */
	public function get_type(): CLI_Argument_Types;

	/**
	 * Get the name of the argument.
	 *
	 * @return string
	 */
	public function get_name(): string;

	/**
	 * Get the description of the argument.
	 *
	 * @return string
	 */
	public function get_description(): string;

	/**
	 * Get whether the argument is optional.
	 *
	 * @return bool
	 */
	public function get_optional(): bool;

	/**
	 * Get whether the argument can be repeated.
	 *
	 * @return bool
	 */
	public function get_repeating(): bool;

	/**
	 * Get the options for the argument.
	 *
	 * @return array|null
	 */
	public function get_options(): ?array;

	/**
	 * Get the default value for the argument.
	 *
	 * @return mixed
	 */
	public function get_default(): mixed;

	/**
	 * Serialize the argument to an array for JSON encoding.
	 *
	 * @return array
	 */
	public function jsonSerialize(): array;
}
