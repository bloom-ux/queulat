<?php
/**
 * Create a custom post type for the plugin creator
 *
 * @package Queulat
 */

namespace Queulat\Generator\Builder;

use Queulat\Helpers\Arrays;

/**
 * Create a custom post type for use on the plugin creator
 */
class Custom_Post_Type {


	/**
	 * Parameters to build the custom post type, sanitized
	 *
	 * @var array
	 */
	private $params = array();

	/**
	 * List of reserved keywords that can't be used as CPT slug
	 *
	 * @var array
	 */
	public static $reserved_keywords = array(
		// core post types.
		'post',
		'page',
		'attachment',
		'revision',
		'nav_menu_item',
		'custom_css',
		'customize_changeset',
		'oembed_cache',
		'user_request',
		'wp_block',
		'wp_global_styles',
		'wp_template',
		'wp_template_part',
		// other reserved keywords.
		'action',
		'author',
		'order',
		'theme',
	);

	/**
	 * Build a new custom post type generator
	 *
	 * @param array $params Input for the generator.
	 */
	public function __construct( array $params = array() ) {
		if ( $params ) {
			$this->params = $this->sanitize_input( $params );
		}
	}

	/**
	 * Get list of features that a custom post type can support
	 *
	 * @return array Custom post type possible features as slug => label
	 */
	public static function get_supports(): array {
		return array(
			'title'           => __( 'Title', 'queulat' ),
			'editor'          => __( 'Editor (content)', 'queulat' ),
			'author'          => __( 'Author', 'queulat' ),
			'thumbnail'       => __( 'Featured image', 'queulat' ),
			'excerpt'         => __( 'Excerpt', 'queulat' ),
			'trackbacks'      => __( 'Trackbacks', 'queulat' ),
			'custom-fields'   => __( 'Custom fields', 'queulat' ),
			'comments'        => __( 'Comments', 'queulat' ),
			'revisions'       => __( 'Revisions', 'queulat' ),
			'page-attributes' => __( 'Page attributes: menu order, parent (if hierarchical is true)', 'queulat' ),
			'post-formats'    => __( 'Post formats', 'queulat' ),
		);
	}

	/**
	 * Sanitize the input given by the user to build the custom post type definition
	 *
	 * @param array $input Data sent by the user.
	 * @return array Sanitized data
	 */
	public function sanitize_input( array $input ): array {
		$flat      = Arrays::flatten( $input );
		$sanitized = array();
		foreach ( $flat as $key => $val ) {
			switch ( $key ) {
				case 'slug':
					$sanitized[ $key ] = $val;
					break;
				case 'description':
					$sanitized[ $key ] = sanitize_textarea_field( $val );
					break;
				case 'label':
					$sanitized[ $key ] = sanitize_text_field( $val );
					break;
				case 'show_in_menu':
					switch ( $val ) {
						case 'no_show':
							$sanitized[ $key ] = false;
							break;
						case 'top_level':
							$sanitized[ $key ] = true;
							break;
						default:
							$sanitized[ $key ] = sanitize_text_field( $val );
							break;
					}
					break;
				case 'public':
				case 'hierarchical':
				case 'has_archive':
				case 'rewrite.with_front':
				case 'rewrite.feeds':
				case 'rewrite.pages':
				case 'can_export':
				case 'delete_with_user':
				case 'show_in_rest':
				case 'rewrite_enable':
					$sanitized[ $key ] = (bool) $val;
					break;
				default:
					if ( stripos( $key, 'supports.' ) !== false ) {
						if ( array_key_exists( $val, static::get_supports() ) ) {
							$sanitized[ $key ] = $val;
						}
					}
					break;
			}
		}
		if ( ! isset( $sanitized['rewrite_enable'] ) || ! $sanitized['rewrite_enable'] ) {
			unset( $sanitized['rewrite'], $sanitized['rewrite_enable'] );
			$sanitized['rewrite'] = false;
		} else {
			unset( $sanitized['rewrite_enable'] );
		}
		return Arrays::reverse_flatten( $sanitized );
	}
}
