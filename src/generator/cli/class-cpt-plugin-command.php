<?php
/**
 * Custom post type plugin generator CLI
 *
 * @package Queulat
 */

namespace Queulat\Generator\CLI;

use Queulat\CLI_Argument;
use Queulat\CLI_Argument_Types;
use Queulat\CLI_Command;
use Queulat\Generator\Builder\Custom_Post_Type_Plugin;

use function WP_CLI\Utils\get_flag_value;

/**
 * CLI command for creating a custom post type plugin
 */
class CPT_Plugin_Command extends CLI_Command {

	/**
	 * Get command name
	 *
	 * @return string queulat generate cpt-plugin
	 */
	public function get_name(): string {
		return 'queulat generate cpt-plugin';
	}

	/**
	 * Get the callable that will execute the command
	 *
	 * @return callable|object|string
	 */
	public function get_callable(): callable|object|string {
		return function ( $args, $assoc_args ) {
			$cpt_args      = array();
			$all_arguments = $this->get_command_arguments();
			$positional_i  = 0;
			foreach ( $all_arguments as $arg ) {
				if ( $arg->get_type() === CLI_Argument_Types::positional ) {
					$arg_value = $args[ $positional_i ] ?? null;
					if ( $arg_value ) {
						$cpt_args[ $arg->get_name() ] = $arg_value;
					}
					++$positional_i;
				} elseif ( $arg->get_type() === CLI_Argument_Types::flag ) {
					$flag_value = get_flag_value( $assoc_args, $arg->get_name(), $arg->get_default() );
					if ( ! is_null( $flag_value ) ) {
						$cpt_args[ $arg->get_name() ] = $flag_value;
					}
				} elseif ( $arg->get_type() === CLI_Argument_Types::assoc ) {
					$assoc_arg_value = $assoc_args[ $arg->get_name() ] ?? null;
					if ( $assoc_arg_value ) {
						$cpt_args[ $arg->get_name() ] = $assoc_arg_value;
					}
				}
			}
			$slug = $cpt_args['post-type'];
			unset( $cpt_args['post-type'] );
			$cpt_args['rewrite']['slug'] = str_replace( '_', '-', $slug );
			$new_plugin                  = new Custom_Post_Type_Plugin( $slug, $cpt_args );
			$new_plugin->build();
			exit( 0 );
		};
	}

	/**
	 * Get command short description
	 *
	 * @return string
	 */
	public function get_shortdesc(): string {
		return __( 'Create a new Custom Post Type plugin', 'queulat' );
	}

	/**
	 * Get command long description
	 *
	 * @return string
	 */
	public function get_longdesc(): string {
		return '';
	}

	/**
	 * Command line arguments
	 *
	 * @return CLI_Argument[]
	 */
	public function get_command_arguments(): array {
		$args = array(
			new CLI_Argument(
				CLI_Argument_Types::positional,
				'post-type',
				'The post type identifier (key stored in post_type on database). Must be less than 20 characters, only lowercase alphanumeric characters.',
				false,
				false
			),
			new CLI_Argument(
				CLI_Argument_Types::assoc,
				'singular',
				'The singular form of the post type name to generate labels'
			),
			new CLI_Argument(
				CLI_Argument_Types::assoc,
				'plural',
				'The plural form of the post type name to generate labels'
			),
			new CLI_Argument(
				CLI_Argument_Types::assoc,
				'description',
				'A short descriptive summary of what the post type is'
			),
			new CLI_Argument(
				CLI_Argument_Types::flag,
				'public',
				'Whether a post type is intended for use publicly either via the admin interface or by front-end users'
			),
			new CLI_Argument(
				CLI_Argument_Types::flag,
				'hierarchical',
				'Whether the post type is hierarchical (e.g. page). Default false.'
			),
			new CLI_Argument(
				CLI_Argument_Types::assoc,
				'menu_icon',
				'Name of a Dashicons element, such as \'dashicons-admin-plugins\''
			),
		);
		return $args;
	}

	/**
	 * Get usage examples
	 *
	 * @return array
	 */
	public function get_examples(): array {
		return array();
	}
}
