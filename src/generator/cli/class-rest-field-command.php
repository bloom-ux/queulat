<?php
/**
 * CLI command for generating REST field classes.
 *
 * @package Queulat
 */

declare(strict_types=1);

namespace Queulat\Generator\CLI;

use Queulat\CLI_Command;
use Queulat\CLI_Argument;
use Queulat\CLI_Argument_Types;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

/**
 * Generate a REST field class extending Queulat\REST_Field via WP-CLI.
 */
class REST_Field_Command extends CLI_Command {

	/**
	 * Command name.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'queulat generate rest-field';
	}

	/**
	 * Execute the command.
	 *
	 * @return callable
	 */
	public function get_callable(): callable {
		return function ( array $args, array $assoc_args ) {
			list( $class_name ) = $args;
			$namespace          = $assoc_args['namespace'] ?? 'Queulat\\REST';
			$object_type        = $assoc_args['object_type'] ?? 'post';
			$attribute          = $assoc_args['attribute'] ?? 'custom_field';
			$destination        = $assoc_args['path'] ?? 'includes';

			$loader    = new FilesystemLoader( __DIR__ . '/../stubs' );
			$twig      = new Environment( $loader );
			$file_body = $twig->render(
				'class-stub-rest-field.twig',
				array(
					'class_name'  => $class_name,
					'namespace'   => $namespace,
					'object_type' => $object_type,
					'attribute'   => $attribute,
				)
			);

			$path = trailingslashit( $destination ) . 'class-' . strtolower( str_replace( '_', '-', $class_name ) ) . '-rest-field.php';

			if ( file_exists( $path ) ) {
				\WP_CLI::error( sprintf( 'The file %s already exists.', $path ) );
			}

			wp_mkdir_p( dirname( $path ) );

			if ( false === file_put_contents( $path, $file_body ) ) {
				\WP_CLI::error( sprintf( 'Could not write the REST field class to %s.', $path ) );
			}

			\WP_CLI::success( sprintf( 'REST field class generated at %s.', $path ) );
		};
	}

	/**
	 * Command synopsis.
	 *
	 * @return array
	 */
	public function get_command_arguments(): array {
		return array(
			new CLI_Argument( CLI_Argument_Types::positional, 'class_name', 'The class name for the REST field ("My_Attribute", "Foo_Bar")' ),
			new CLI_Argument( CLI_Argument_Types::assoc, 'namespace', 'FQN namespace for the generated class. ("Vendor\This_Package")', true ),
			new CLI_Argument( CLI_Argument_Types::assoc, 'object_type', 'Object type handled by the field ("post" or custom post type slug)', true ),
			new CLI_Argument( CLI_Argument_Types::assoc, 'attribute', 'Attribute name registered on the REST field. ("my_attribute")', true ),
			new CLI_Argument( CLI_Argument_Types::assoc, 'path', 'Destination directory for the class file.', true ),
		);
	}

	public function get_shortdesc(): string {
		return __( 'Generate a REST field class extending Queulat\REST_Field.', 'queulat' );
	}

	public function get_examples(): array {
		return array(
			array(
				'# Generate a REST field class for posts',
				'$ wp queulat generate rest-field Post_Author_Field --attribute=author_field --object_type=post',
			),
		);
	}
}
