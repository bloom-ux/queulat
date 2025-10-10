<?php
/**
 * Abstract class for CLI commands
 *
 * @package Queulat
 */

namespace Queulat;

use Queulat\Contracts\CLI_Argument_Interface;
use Queulat\Contracts\CLI_Command_Interface as CLI_Command_Contract;

/**
 * Abstract class for creating WP-CLI commands
 */
abstract class CLI_Command implements CLI_Command_Contract {

	/**
	 * Return the arguments for the command
	 *
	 * @return null|array|CLI_Argument_Interface[]|JsonSerializable[] Zero or more arguments
	 */
	abstract public function get_command_arguments(): ?array;

	/**
	 * Get short description (<80 chars)
	 *
	 * @return string
	 */
	abstract public function get_shortdesc(): string;

	/**
	 * Get usage examples.
	 *
	 * Each example should have 3 parts:
	 *   - Description: `# Title of the example`
	 *   - Command: `$ wp core version`
	 *   - Sample output: exact command output (except very long)
	 *
	 * @return array
	 */
	abstract public function get_examples(): array;

	/**
	 * Get command registration arguments
	 *
	 * @return array
	 */
	public function get_args(): array {
		return array(
			'shortdesc' => $this->get_shortdesc(),
			'synopsis'  => $this->get_synopsis(),
			'when'      => $this->get_when(),
			'longdesc'  => $this->get_longdesc(),
		);
	}

	/**
	 * Determine when the command is executed. Must be a WP-CLI hook.
	 *
	 * @return string Default command execution ("after_wp_load")
	 * @see https://make.wordpress.org/cli/handbook/references/internal-api/wp-cli-add-hook/#notes
	 */
	public function get_when(): string {
		return 'after_wp_load';
	}

	/**
	 * Get the command arguments
	 *
	 * @return array|CLI_Argument_Interface[] Zero or more arguments.
	 */
	public function get_synopsis(): array {
		$args = $this->get_command_arguments();
		return array_map(
			function ( $item ) {
				return is_array( $item ) ? $item : $item->jsonSerialize();
			},
			$args
		);
	}

	/**
	 * Get the text that is displayed after the command description
	 *
	 * It is recommended to have OPTIONS and EXAMPLES sections.
	 *
	 * Sections are identified by `## SECTION NAME`
	 *
	 * @return string
	 */
	public function get_longdesc(): string {
		return '';
	}

	/**
	 * Use an instance of this class as function
	 *
	 * @param array $args Positional arguments passed to the command.
	 * @param array $assoc_args Associative arguments.
	 * @return void
	 */
	public function __invoke( $args, $assoc_args ) {
		$callable = $this->get_callable();
		call_user_func( $callable, $args, $assoc_args );
	}

	/**
	 * Add cli command
	 *
	 * @return bool True if successful, false if deferred; error if fails
	 */
	public static function init(): bool {
		$called_class = get_called_class();
		$command_inst = new $called_class();
		return \WP_CLI::add_command(
			$command_inst->get_name(),
			$command_inst->get_callable(),
			$command_inst->get_args()
		);
	}
}
