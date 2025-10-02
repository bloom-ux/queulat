<?php
/**
 * String utility class.
 *
 * Provides static methods for common string operations including case conversion,
 * word limiting, and pluralization using the Doctrine Inflector.
 *
 * @package Queulat
 */

namespace Queulat\Helpers;

use Doctrine\Inflector\InflectorFactory;

/**
 * Strings utilities
 */
class Strings {
	/**
	 * Private constructor to prevent instantiation.
	 *
	 * This class should only be used statically.
	 *
	 * @since 0.1.0
	 */
	private function __construct() {
	}

	/**
	 * Convert a string to "kebab-case" (lowercase and separated by dashes)
	 *
	 * @param  string $input_string The input string.
	 * @param  int    $limit  Limit the conversion to an amount of parts.
	 * @return string         String converted to kebab-case
	 */
	public static function to_kebab_case( string $input_string, int $limit = 0 ): string {
		$input_string = static::to_snake_case( $input_string, $limit );
		return str_replace( '_', '-', $input_string );
	}

	/**
	 * Convert a string to "snake_case" (lowercase and separated by underscores)
	 *
	 * @param string $input_string The input string.
	 * @return string String converted to snake_case
	 */
	public static function to_snake_case( string $input_string ): string {
		return preg_replace_callback(
			'/([A-Z])/',
			function ( $str_match ) {
				return '_' . strtolower( $str_match[1] );
			},
			$input_string
		);
	}

	/**
	 * Convert a string to Capitalized_Snake_Case
	 *
	 * @param  string $input_string The input string.
	 * @param  int    $limit  Limit the conversion to this amount of parts.
	 * @return string         String converted to Capitalized_Snake_Case
	 */
	public static function to_capitalized_snake_case( string $input_string, int $limit = 0 ): string {
		$input_string = preg_replace( '/[^a-zA-Z0-9]/', '-', $input_string );
		$input_string = explode( '-', $input_string );
		if ( $limit ) {
			$input_string = array_slice( $input_string, 0, $limit );
		}
		$input_string = array_map(
			function ( $item ) {
				return strtoupper( $item[0] ) . substr( $item, 1 );
			},
			$input_string
		);
		return implode( '_', $input_string );
	}

	/**
	 * Limit a string up to the desired amount of characters, but always finish
	 * on full words (and optionally, the $end string)
	 *
	 * @param  string $input_string The string that will be cut.
	 * @param  int    $limit  The maximum amount of characters for the string.
	 * @param  string $end    What to append at the end of the string, if the initial length it's over $limit.
	 * @return string         The shortened string
	 */
	public static function limit_words( string $input_string, int $limit, string $end = '' ): string {
		// cleanup the string.
		$input_string = function_exists( 'wp_strip_all_tags' ) ? wp_strip_all_tags( $input_string ) : sanitize_text_field( $input_string );

		if ( function_exists( 'mb_strlen' ) && mb_strlen( $input_string ) < $limit ) {
			return $input_string;
		} elseif ( strlen( $input_string ) < $limit ) {
			return $input_string;
		}

		$input_string = substr( $input_string, 0, $limit );
		$words        = explode( ' ', $input_string );
		// pop a possibly-cut word.
		array_pop( $words );
		// pop the latest of the words, get it clean if it ends on a punctuation mark.
		$last_word = array_pop( $words );
		$last_word = preg_replace( '/[\W]/u', '', $last_word );
		$words[]   = $last_word;
		return implode( ' ', $words ) . $end;
	}

	/**
	 * Get the plural form of an English word
	 *
	 * @param string $input Input string.
	 * @return string Pluralized string
	 */
	public static function plural( string $input ): string {
		$inflector = InflectorFactory::create()->build();
		return $inflector->pluralize( $input );
	}
}
