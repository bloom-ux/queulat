<?php
/**
 * Register scaffold aliases for Queulat generator commands.
 *
 * This allows Queulat generators to be discovered under WP-CLI's built-in
 * `scaffold` namespace without modifying the original command classes.
 *
 * @package Queulat
 */

declare(strict_types=1);

namespace Queulat\Generator\CLI;

use Queulat\CLI_Command;

/**
 * Map existing Queulat generator commands to scaffold aliases.
 */
class Scaffold_Aliases {

	/**
	 * Map of primary command names to their scaffold aliases.
	 *
	 * @var array<string, string>
	 */
	private const ALIASES = array(
		'queulat generate cpt-plugin' => 'scaffold queulat-cpt-plugin',
		'queulat generate rest-field' => 'scaffold queulat-rest-field',
	);

	/**
	 * Register scaffold aliases for known Queulat commands.
	 *
	 * @param CLI_Command[] $commands Command instances.
	 * @return void
	 */
	public static function register( array $commands ): void {
		foreach ( $commands as $command ) {
			$alias = self::ALIASES[ $command->get_name() ] ?? null;
			if ( null === $alias ) {
				continue;
			}
			\WP_CLI::add_command(
				$alias,
				$command->get_callable(),
				$command->get_args()
			);
		}
	}
}
