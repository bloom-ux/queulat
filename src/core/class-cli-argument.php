<?php
/**
 * Command line argument
 *
 * @package Queulat
 */

namespace Queulat;

use Queulat\Contracts\CLI_Argument_Interface as CLI_Argument_Contract;

/**
 * Command line argument
 */
class CLI_Argument implements CLI_Argument_Contract {

	/**
	 * Build a new CLI_Argument object
	 *
	 * @param CLI_Argument_Types $type Type of argument.
	 * @param string             $name Name of the argument.
	 * @param string             $description Argument description.
	 * @param bool               $optional Whether the argument is optional; default true.
	 * @param bool               $repeating Whether the argument can be repeated; default false.
	 * @param null|array         $options An array of allowed options; default null.
	 * @param mixed              $default_value The default param avalue.
	 */
	public function __construct(
		private CLI_Argument_Types $type,
		private string $name,
		private string $description,
		private bool $optional = true,
		private bool $repeating = false,
		private ?array $options = null,
		private $default_value = null
	) {}

	/**
	 * Get the type of the argument.
	 *
	 * @return CLI_Argument_Types
	 */
	public function get_type(): CLI_Argument_Types {
		return $this->type;
	}

	/**
	 * Get the name of the argument.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return $this->name;
	}

	/**
	 * Get the description of the argument.
	 *
	 * @return string
	 */
	public function get_description(): string {
		return $this->description;
	}

	/**
	 * Get whether the argument is optional.
	 *
	 * @return bool
	 */
	public function get_optional(): bool {
		return $this->optional;
	}

	/**
	 * Get whether the argument can be repeated.
	 *
	 * @return bool
	 */
	public function get_repeating(): bool {
		return $this->repeating;
	}

	/**
	 * Get the options for the argument.
	 *
	 * @return array|null
	 */
	public function get_options(): ?array {
		return $this->options;
	}

	/**
	 * Get the default value for the argument.
	 *
	 * @return mixed
	 */
	public function get_default(): mixed {
		return $this->default_value;
	}

	/**
	 * Return the argument parameters as an array
	 *
	 * @return array
	 */
	public function jsonSerialize(): array {
		return array(
			'type'        => $this->get_type()->value,
			'name'        => $this->get_name(),
			'description' => $this->get_description(),
			'optional'    => $this->get_optional(),
			'repeating'   => $this->get_repeating(),
			'options'     => $this->get_options(),
			'default'     => $this->get_default(),
		);
	}
}
