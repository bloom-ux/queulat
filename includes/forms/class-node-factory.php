<?php
/**
 * Static factory to create elements or components
 *
 * @package Queulat
 */

declare(strict_types=1);

namespace Queulat\Forms;

use Queulat\Helpers\Arrays;
use Queulat\Forms\Node_Factory_Call_Type as Call_Type;

/**
 * Node Factory creates any kind of form element or component
 */
class Node_Factory {

	/**
	 * Hold a set of registered arguments handled by the factory
	 *
	 * Each element it's indexed by argument name and has a Node_Factory_Argument_Handler
	 * as value.
	 *
	 * @var array
	 */
	private static $registered_arguments = array();

	/**
	 * Allow using the factory as an object. Forward method calls to static methods
	 *
	 * @param string $name      The invoked method.
	 * @param array  $arguments Method arguments.
	 * @return void
	 * @throws \BadFunctionCallException When trying to call an undefined method.
	 */
	public function __call( $name, $arguments ) {
		if ( method_exists( __CLASS__, $name ) ) {
			call_user_func_array( array( __CLASS__, $name ), $arguments );
		} else {
			/* translators: %s: Name of the called class and method */
			$message = _x( 'Call to undefined method %s', 'node factory exception', 'queulat' );
			throw new \BadFunctionCallException( esc_html( sprintf( $message, __CLASS__ . "::{$name}()" ) ) );
		}
	}

	/**
	 * Create a new form element or component
	 *
	 * @param string $element_name Fully qualified name for the object class.
	 * @param array  $args         An specification of arguments used to build the object.
	 * @return Node_Interface      An instantiated object.
	 * @throws \LogicException     If the requested element class doesn't exist.
	 */
	public static function make( string $element_name, array $args = array() ): Node_Interface {
		if ( ! class_exists( $element_name ) ) {
			/* translators: %s: name of the PHP class of the element */
			$message = _x( "The '%s' element doesn't exists", 'node factory exception', 'queulat' );
			throw new \LogicException( esc_html( sprintf( $message, $element_name ) ) );
		}
		$obj  = new $element_name();
		$args = Arrays::reverse_flatten( $args );
		static::configure( $obj, $args );
		return $obj;
	}

	/**
	 * Get the list of registered arguments and their handlers.
	 *
	 * @return array
	 */
	public static function get_registered_arguments() {
		return static::$registered_arguments;
	}

	/**
	 * Register a new argument hander
	 *
	 * @param Node_Factory_Argument_Handler $handler Register an argument handler.
	 * @return void
	 */
	public static function register_argument( Node_Factory_Argument_Handler $handler ) {
		static::$registered_arguments[ $handler->argument ] = $handler;
	}

	/**
	 * Unregister an argument handler
	 *
	 * @param string $argument The key of the argument to unregister.
	 * @return void
	 */
	public static function unregister_argument( string $argument ) {
		unset( static::$registered_arguments[ $argument ] );
	}

	/**
	 * Configure an object instance
	 *
	 * @param Node_Interface $obj  The element to be configured.
	 * @param array          $args Builiding specs.
	 * @return Node_Interface      The built object
	 */
	public static function configure( Node_Interface $obj, array $args = array() ): Node_Interface {
		foreach ( static::get_registered_arguments() as $argument => $handler ) {
			if ( ! isset( $args[ $argument ] ) ) {
				continue;
			}
			// check if the object implements the required method.
			if ( is_callable( array( $obj, $handler->method ) ) ) {
				if ( is_array( $args[ $argument ] ) ) {
					// check if the arguments should be given as distinct parameters to the method,
					// use their keys as arguments or just use the value.
					$call_type = $handler->call_type ?? Call_Type::VALUE;
					switch ( $call_type ) {
						case Call_Type::ARRAY:
							call_user_func_array( array( $obj, $handler->method ), $args[ $argument ] );
							break;
						case Call_Type::KEY_VALUE:
							foreach ( $args[ $argument ] as $key => $val ) {
								$obj->{$handler->method}( $key, $val );
							}
							break;
						case Call_Type::VALUE_ITEMS:
							array_walk( $args[ $argument ], array( $obj, $handler->method ) );
							break;
						case Call_Type::VALUE:
							$obj->{ $handler->method }( $args[ $argument ] );
							break;
					}
				} else {
					$obj->{$handler->method}( $args[ $argument ] );
				}
			}
		}
		return $obj;
	}
}
