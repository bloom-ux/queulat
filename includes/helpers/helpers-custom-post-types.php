<?php
/**
 * Helper function for Custom post types
 *
 * @package Queulat
 */

declare(strict_types=1);

use Queulat\Contracts\Post_Type_Interface;

function queulat_custom_post_type( string|Post_Type_Interface $cpt ): Post_Type_Interface {
	static $registry;
	$custom_post_type_class = is_string( $cpt ) ? $cpt : get_class( $cpt );
	if ( isset( $registry[ $custom_post_type_class ] ) ) {
		return $registry[ $custom_post_type_class ];
	}
	if ( $cpt instanceof Post_Type_Interface ) {
		$custom_post_type_class              = get_class( $cpt );
		$registry[ $custom_post_type_class ] = $cpt;
		return $cpt;
	}
	if ( ! class_exists( $custom_post_type_class, true ) ) {
		/* translators: %s: name of the php class */
		throw new InvalidArgumentException( esc_html( sprintf( _x( "%s class can't be found", 'invalid argument exception', 'queulat' ), $custom_post_type_class ) ) );
	}
	$custom_post_type = new $custom_post_type_class();
	if ( ! $custom_post_type instanceof Post_Type_Interface ) {
		/* translators: %s: name of the php class */
		throw new InvalidArgumentException( esc_html( sprintf( _x( '%s must implement Queulat\Contracts\Post_Type_Interface', 'invalid argument exception', 'queulat' ), $custom_post_type_class ) ) );
	}
	$registry[ $custom_post_type_class ] = new $cpt();
	return $registry[ $custom_post_type_class ];
}

/**
 * Get an instance of a custom post type object
 *
 * @param string $custom_post_type_class Name of the post type class instance to get. Must implement Post_Type_Interface.
 * @return Post_Type_Interface The custom post type instance
 * @throws InvalidArgumentException If the class doesn't implement Post_Type_Interface.
 */
function queulat_get_custom_post_type( string $custom_post_type_class ): Post_Type_Interface {
	static $registry;
	if ( isset( $registry[ $custom_post_type_class ] ) ) {
		return $registry[ $custom_post_type_class ];
	}
	if ( ! class_exists( $custom_post_type_class, true ) ) {
		/* translators: %s: name of the php class */
		throw new InvalidArgumentException( esc_html( sprintf( _x( "%s class can't be found", 'invalid argument exception', 'queulat' ), $custom_post_type_class ) ) );
	}
	$custom_post_type = new $custom_post_type_class();
	if ( ! $custom_post_type instanceof Post_Type_Interface ) {
		/* translators: %s: name of the php class */
		throw new InvalidArgumentException( esc_html( sprintf( _x( '%s must implement Queulat\Contracts\Post_Type_Interface', 'invalid argument exception', 'queulat' ), $custom_post_type_class ) ) );
	}
	$registry[ $custom_post_type_class ] = $custom_post_type;
	return $registry[ $custom_post_type_class ];
}

/**
 * Init a custom post type
 *
 * @param string|Post_Type_Interface $custom_post_type The name of the class or instance of a custom post type.
 * @return void
 */
function queulat_custom_post_type_init( string|Post_Type_Interface $custom_post_type ): void {
	if ( is_string( $custom_post_type ) ) {
		$custom_post_type = queulat_get_custom_post_type( $custom_post_type );
	}
	$custom_post_type->init();
}
