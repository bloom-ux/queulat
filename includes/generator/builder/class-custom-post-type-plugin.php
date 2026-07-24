<?php
/**
 * Generate a Custom Post Type plugin
 *
 * @package Queulat
 */

declare(strict_types=1);

namespace Queulat\Generator\Builder;

use WP_Post_Type;
use Twig\Environment;
use Queulat\Helpers\Strings;
use Queulat\Generator\Renderer;
use stdClass;
use Twig\Loader\FilesystemLoader;

/**
 * Generate a Custom Post Type plugin
 */
class Custom_Post_Type_Plugin {

	/**
	 * Hold the temporary instance of the WordPress Post Type
	 *
	 * @var \WP_Post_Type
	 */
	private $wp_post_type;

	/**
	 * Hold the raw slug used as post type slug
	 *
	 * @var string
	 */
	private $raw_slug = '';

	/**
	 * Hold the raw namespace prefix
	 *
	 * @var string
	 */
	private $raw_prefix = 'queulat';

	/**
	 * Template variables
	 *
	 * @var array
	 */
	private array $template_vars = array();

	/**
	 * Build a new Custom Post Type plugin builder
	 *
	 * @param string $slug The post type slug (snake_case).
	 * @param string $prefix Namespace prefix (snake_case).
	 * @param array  $args  Post type arguments.
	 * @see https://developer.wordpress.org/reference/functions/register_post_type/#Arguments
	 */
	public function __construct( string $slug, string $prefix = 'queulat', array $args = array() ) {
		$this->raw_slug   = $slug;
		$this->raw_prefix = $prefix;
		$sanitized_slug   = mb_strtolower( sanitize_key( $this->raw_prefix ) . '_' . sanitize_key( $this->raw_slug ) );
		if ( empty( $args['rest_namespace'] ) ) {
			$args['rest_namespace'] = "{$prefix}/v1";
		}
		if ( ! empty( $args['singular'] ) && empty( $args['plural'] ) ) {
			$args['plural'] = Strings::plural( $args['singular'] );
		}
		if ( empty( $args['rest_base'] ) ) {
			$args['rest_base'] = Strings::to_kebab_case( mb_strtolower( Strings::plural( $slug ) ) );
		}
		$this->wp_post_type = new WP_Post_Type( $sanitized_slug, $args );
		if ( empty( $args['labels'] ) ) {
			$this->wp_post_type->labels = $this->generate_labels( $args );
		}
		if ( empty( $args['label'] ) && ! empty( $args['plural'] ) ) {
			$this->wp_post_type->label = $args['plural'];
		}
	}

	/**
	 * Generate custom labels for the post type
	 *
	 * @param array $args Post type parameters.
	 * @return stdClass Custom labels
	 *
	 * phpcs:disable WordPress.WP.I18n.NonSingularStringLiteralText,WordPress.WP.I18n.MissingArgDomain
	 */
	private function generate_labels( array $args = array() ): stdClass {
		$default_labels   = get_post_type_labels( $this->wp_post_type );
		$default_singular = __( $this->wp_post_type->labels->singular_name );
		$default_plural   = __( $this->wp_post_type->labels->name );
		$custom_singular  = sanitize_text_field( $args['singular'] );
		$custom_plural    = sanitize_text_field( $args['plural'] );
		$custom_labels    = (object) array();
		foreach ( $default_labels as $key => $label ) {
			$label               = __( $label );
			$new_label           = stripos( (string) $label, $default_plural ) === false ? str_ireplace( $default_singular, $custom_singular, (string) $label ) : str_ireplace( $default_plural, $custom_plural, (string) $label );
			$custom_labels->$key = $new_label;
		}
		$custom_labels->name_admin_bar = $custom_singular;
		return $custom_labels;
	}

	/**
	 * Render the post type arguments as string
	 *
	 * @param string $package_name Package name for use in localization strings.
	 * @return string
	 */
	private function render_post_type_arguments( string $package_name ): string {
		$object_vars                    = get_object_vars( $this->wp_post_type );
		$longest_key                    = Renderer::get_longest_key_length( array_keys( $object_vars ) );
		$object_vars['capability_type'] = array(
			$this->wp_post_type->name,
			sanitize_key( Strings::plural( $this->wp_post_type->name ) ),
		);
		$properties                     = '';
		$localize                       = array(
			'label',
			'labels',
			'description',
		);
		foreach ( $object_vars as $key => $val ) {
			// internal properties.
			if ( strpos( $key, '_' ) === 0 || 'name' === $key || 'cap' === $key ) {
				continue;
			}
			$properties .= Renderer::render_array_member( $key, $val, $longest_key, in_array( $key, $localize, true ), $package_name ? $package_name : '' );
		}
		return $properties;
	}

	/**
	 * Build all template variables used on the files to be generated
	 *
	 * @return array Associative array with var name as keys
	 */
	public function get_template_vars(): array {
		if ( ! empty( $this->template_vars ) ) {
			return $this->template_vars;
		}
		$label               = $this->wp_post_type->label;
		$file_name           = Strings::to_kebab_case( $this->wp_post_type->name );
		$class_name          = Strings::to_capitalized_snake_case( $this->raw_slug );
		$description         = $this->wp_post_type->description;
		$post_type           = $this->wp_post_type->name;
		$prefix              = $this->raw_prefix;
		$base_name           = Strings::to_kebab_case( $this->raw_slug );
		$namespace           = Strings::to_capitalized_snake_case( $this->raw_prefix ) . '\\' . $class_name;
		$package_name        = str_replace( '_', '-', sanitize_key( $this->raw_prefix ) . '-' . sanitize_key( $this->raw_slug ) . '-cpt' );
		$post_type_arguments = Renderer::ident( $this->render_post_type_arguments( $package_name ), 3 );
		$this->template_vars = compact( 'label', 'file_name', 'class_name', 'description', 'post_type', 'post_type_arguments', 'prefix', 'base_name', 'namespace', 'package_name' );
		return $this->template_vars;
	}

	/**
	 * Get the name of the templates that will be used
	 *
	 * @return array
	 */
	public function get_templates(): array {
		$template_vars = $this->get_template_vars();
		return array(
			'stub-cpt.twig'          => "{$template_vars['file_name']}-cpt.php",
			'class-post-type.twig'   => "class-{$template_vars['base_name']}-post-type.php",
			'class-post-query.twig'  => "class-{$template_vars['base_name']}-post-query.php",
			'class-post-object.twig' => "class-{$template_vars['base_name']}-post-object.php",
		);
	}

	/**
	 * Generate the plugin files
	 */
	public function build() {
		$template_vars = $this->get_template_vars();

		// replace "stub" in stub file names with the file name.
		$stub         = $template_vars['file_name'];
		$loader       = new FilesystemLoader( __DIR__ . '/../stubs' );
		$twig         = new Environment( $loader, array() );
		$templates    = $this->get_templates();
		$output_files = array();
		foreach ( $templates as $template => $output_file_name ) {
			$output_files[ $output_file_name ] = $twig->render( $template, $template_vars );
		}

		$url   = wp_nonce_url( 'tools.php?page=queulat-cpt-plugin-generator', 'queulat-cpt-plugin-generator' );
		$creds = request_filesystem_credentials( $url, '', false, WP_PLUGIN_DIR, array() );

		if ( ! $creds ) {
			// @todo si no tengo credenciales... generar un zip?
			wp_die( 'No tienes permiso para escribir archivos en tu instalación de WordPress' );
		}

		WP_Filesystem( $creds );

		global $wp_filesystem;

		$plugin_dir = $template_vars['package_name'];

		$wp_filesystem->mkdir( WP_PLUGIN_DIR . "/{$plugin_dir}" );
		$wp_filesystem->mkdir( WP_PLUGIN_DIR . "/{$plugin_dir}/includes" );
		foreach ( $output_files as $filename => $contents ) {
			if ( str_starts_with( $filename, 'class-' ) ) {
				$wp_filesystem->put_contents( WP_PLUGIN_DIR . "/{$plugin_dir}/includes/{$filename}", $contents );
			} else {
				$wp_filesystem->put_contents( WP_PLUGIN_DIR . "/{$plugin_dir}/{$filename}", $contents );
			}
		}

		return true;
	}
}
