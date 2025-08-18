<?php
/**
 * Array utility class.
 *
 * Provides static methods for common array operations including
 * associative array detection, flattening, and recursive filtering.
 *
 * @package Queulat
 */

namespace Queulat\Helpers;

/**
 * Arrays utilities
 */
class Arrays {
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
	 * Determines if an array is associative.
	 *
	 * An array is "associative" if it doesn't have sequential numeric keys beginning with zero.
	 *
	 * @param  iterable|object $arr The tested array.
	 * @return bool
	 */
	public static function is_associative( $arr ): bool {
		$keys = is_array( $arr ) ? array_keys( $arr ) : array_keys( $arr->getArrayCopy() );
		return array_keys( $keys ) !== $keys;
	}

	/**
	 * Flatten a multi-dimensional associative array with dots.
	 *
	 * @param array $arr   Input array.
	 * @param int   $mode  Mode of flattening.
	 * @return array Array with dotted keys
	 * @see \Minwork\Helper\Arr for mode constants.
	 */
	public static function flatten( array $arr, int $mode = \Minwork\Helper\Arr::UNPACK_ALL ): array {
		return \Minwork\Helper\Arr::unpack( $arr, $mode );
	}

	/**
	 * Reverse a flattened array in its original form.
	 *
	 * @param  array  $arr  Flattened array.
	 * @param  string $glue Glue used in flattening.
	 * @return array  the unflattened array
	 */
	public static function reverse_flatten( array $arr, string $glue = '.' ): array {
		$return = array();
		foreach ( $arr as $key => $value ) {
			if ( stripos( $key, $glue ) !== false ) {
				$keys = explode( $glue, $key );
				$temp =& $return;
				while ( count( $keys ) > 1 ) { //phpcs:ignore
					$key = array_shift( $keys );
					$key = is_numeric( $key ) ? (int) $key : $key;
					if ( ! isset( $temp[ $key ] ) || ! is_array( $temp[ $key ] ) ) {
						$temp[ $key ] = array();
					}
					$temp =& $temp[ $key ];
				}
				$key          = array_shift( $keys );
				$key          = is_numeric( $key ) ? (int) $key : $key;
				$temp[ $key ] = $value;
			} else {
				$key            = is_numeric( $key ) ? (int) $key : $key;
				$return[ $key ] = $value;
			}
		}
		return $return;
	}

	/**
	 * Recursively filter an array
	 *
	 * @param  array    $arr      The input array.
	 * @param  callable $callback A custom function for filtering (by default, uses array_filter).
	 * @return array              Filtered array
	 */
	public static function filter_recursive( array $arr, $callback = null ) {
		foreach ( $arr as &$value ) {
			if ( is_array( $value ) ) {
				$value = null === $callback ? static::filter_recursive( $value ) : static::filter_recursive( $value, $callback );
			}
		}
		return null === $callback ? array_filter( $arr ) : array_filter( $arr, $callback );
	}
}
