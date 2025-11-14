<?php
/**
 * Queulat namespaced autoloader that honours WordPress file naming conventions.
 *
 * @package Queulat
 */

declare(strict_types=1);

namespace Queulat\Helpers;

/**
 * Autoload classes using class-/interface-/trait-/enum- prefixed filenames.
 */
class Autoloader {

	/**
	 * Namespace separator.
	 */
	const NAMESPACE_SEPARATOR = '\\';

	/**
	 * Root namespace handled by this autoloader.
	 *
	 * @var string
	 */
	private $root_namespace = '';

	/**
	 * Base directory where source files live.
	 *
	 * @var string
	 */
	private $base_dir;

	/**
	 * Instantiate the autoloader.
	 *
	 * @param string $root_namespace Namespace prefix (defaults to 'Queulat\').
	 * @param string $base_dir       Absolute path to the base directory.
	 */
	public function __construct( string $root_namespace = 'Queulat\\', string $base_dir = '' ) {
		$root_namespace       = rtrim( $root_namespace, self::NAMESPACE_SEPARATOR );
		$this->root_namespace = '' === $root_namespace ? '' : $root_namespace . self::NAMESPACE_SEPARATOR;
		$this->base_dir       = $base_dir ? rtrim( $base_dir, '/\\' ) : dirname( __DIR__ );
	}

	/**
	 * Register this autoloader instance.
	 *
	 * @return void
	 */
	public function register(): void {
		spl_autoload_register( array( $this, 'load' ) );
	}

	/**
	 * Convenience helper to register the autoloader.
	 *
	 * @param string $root_namespace Namespace prefix.
	 * @param string $base_dir       Base directory.
	 * @return static
	 */
	public static function boot( string $root_namespace = 'Queulat\\', string $base_dir = '' ) {
		$autoloader = new static( $root_namespace, $base_dir );
		$autoloader->register();
		return $autoloader;
	}

	/**
	 * Attempt to load the given class.
	 *
	 * @param string $class Fully-qualified class name.
	 * @return void
	 */
	public function load( string $class ): void {
		if ( '' !== $this->root_namespace && 0 !== strpos( $class, $this->root_namespace ) ) {
			return;
		}

		$relative_class = '' !== $this->root_namespace ? substr( $class, strlen( $this->root_namespace ) ) : $class;
		$segments       = explode( self::NAMESPACE_SEPARATOR, $relative_class );

		$file_segment = array_pop( $segments );
		$directory    = $this->base_dir;

		foreach ( $segments as $segment ) {
			$directory .= DIRECTORY_SEPARATOR . $this->segment_to_directory( $segment );
		}

		foreach ( $this->build_candidates( $file_segment ) as $candidate ) {
			$file_path = $directory . DIRECTORY_SEPARATOR . $candidate;
			if ( is_readable( $file_path ) ) {
				require $file_path;
				return;
			}
		}
	}

	/**
	 * Convert a namespace segment to a directory slug.
	 *
	 * @param string $segment Namespace segment.
	 * @return string
	 */
	private function segment_to_directory( string $segment ): string {
		return $this->to_slug( $segment );
	}

	/**
	 * Build potential filenames for the given class segment.
	 *
	 * @param string $segment Class name segment.
	 * @return array<string>
	 */
	private function build_candidates( string $segment ): array {
		$candidates = array();

		// Detect explicit suffixes.
		if ( $this->ends_with( $segment, '_Interface' ) || $this->ends_with( $segment, 'Interface' ) ) {
			$trimmed    = $this->trim_suffix( $segment, array( '_Interface', 'Interface' ) );
			$candidates = array_merge(
				$candidates,
				$this->prefixed_filenames( 'interface', $trimmed )
			);
		} elseif ( $this->ends_with( $segment, '_Trait' ) || $this->ends_with( $segment, 'Trait' ) ) {
			$trimmed    = $this->trim_suffix( $segment, array( '_Trait', 'Trait' ) );
			$candidates = array_merge(
				$candidates,
				$this->prefixed_filenames( 'trait', $trimmed )
			);
		} elseif ( $this->ends_with( $segment, '_Enum' ) || $this->ends_with( $segment, 'Enum' ) ) {
			$trimmed    = $this->trim_suffix( $segment, array( '_Enum', 'Enum' ) );
			$candidates = array_merge(
				$candidates,
				$this->prefixed_filenames( 'enum', $trimmed )
			);
		}

		$slug = $this->to_slug( $segment );

		$candidates[] = 'class-' . $slug . '.php';
		$candidates[] = 'interface-' . $slug . '.php';
		$candidates[] = 'trait-' . $slug . '.php';
		$candidates[] = 'enum-' . $slug . '.php';

		return array_values( array_unique( $candidates ) );
	}

	/**
	 * Build filenames with a given prefix.
	 *
	 * @param string $prefix  File prefix (class/interface/trait/enum).
	 * @param string $segment Class segment without suffix.
	 * @return array<string>
	 */
	private function prefixed_filenames( string $prefix, string $segment ): array {
		$slug = $this->to_slug( $segment );
		return array(
			sprintf( '%s-%s.php', $prefix, $slug ),
		);
	}

	/**
	 * Convert an identifier to a slug (kebab-case).
	 *
	 * @param string $value Input value.
	 * @return string
	 */
	private function to_slug( string $value ): string {
		// Convert to lower case first to avoid "In_CAPS" → "in-c-a-p-s" (get "in-caps" instead).
		$value = strtolower( $value );
		$value = Strings::to_kebab_case( $value );
		$value = preg_replace( '/-+/', '-', $value );
		return trim( $value, '-' );
	}

	/**
	 * Check if a string ends with a suffix.
	 *
	 * @param string $haystack Input string.
	 * @param string $needle   Suffix.
	 * @return bool
	 */
	private function ends_with( string $haystack, string $needle ): bool {
		if ( '' === $needle ) {
			return true;
		}
		return substr( $haystack, -strlen( $needle ) ) === $needle;
	}

	/**
	 * Trim any matching suffix from the segment.
	 *
	 * @param string   $segment Class segment.
	 * @param string[] $suffixes Suffixes to trim.
	 * @return string
	 */
	private function trim_suffix( string $segment, array $suffixes ): string {
		foreach ( $suffixes as $suffix ) {
			if ( $this->ends_with( $segment, $suffix ) ) {
				return substr( $segment, 0, -strlen( $suffix ) );
			}
		}
		return $segment;
	}
}
