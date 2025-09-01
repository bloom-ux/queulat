<?php
/**
 * Bootstrap class for initializing Queulat plugin functionality.
 *
 * This class handles the initialization of the plugin, including asset loading,
 * admin page setup, and node factory argument registration.
 *
 * @package Queulat
 * @since   0.1.0
 */

namespace Queulat;

use Queulat\Forms\Node_Factory;
use Queulat\Forms\Node_Factory_Argument_Handler;

/**
 * Hook Queulat into WordPress
 */
class Bootstrap {
	/**
	 * Initialize the plugin functionality.
	 *
	 * Sets up action hooks for admin initialization, asset enqueuing,
	 * and registers default node factory arguments.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'muplugins_loaded', array( $this, 'init_generator_admin' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ), 9999 );
		$this->register_default_node_factory_args();
		add_action( 'init', array( $this, 'load_translations' ) );
	}

	/**
	 * Initialize custom post type generator admin screen
	 *
	 * @return void
	 */
	public function init_generator_admin() {
		( new Generator\Admin\CPT_Plugin() )->init();
	}

	/**
	 * Load Queulat translations
	 *
	 * @return void
	 */
	public function load_translations() {
		load_muplugin_textdomain( 'queulat', str_replace( WPMU_PLUGIN_DIR, '', __DIR__ ) . '/../../languages' );
	}

	/**
	 * Enqueue admin assets for the plugin.
	 *
	 * Loads CSS assets from the manifest file and enqueues them for admin pages.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	public function enqueue_assets() {
		static $asset_versions;
		$versions_path  = __DIR__ . '/../../dist/manifest.json';
		$asset_versions = json_decode( file_get_contents( $versions_path ) );
		wp_enqueue_style( 'queulat-forms', plugins_url( '..' . $asset_versions->{'dist/admin.css'}, __DIR__ ), array(), null, 'all' );
	}

	/**
	 * Register default argument handlers for the node factory.
	 *
	 * Sets up handlers for common node properties like attributes, label,
	 * name, options, properties, value, text content, and children.
	 *
	 * @since 0.1.0
	 * @return void
	 */
	private function register_default_node_factory_args() {
		$handlers = array(
			new Node_Factory_Argument_Handler( 'attributes', 'set_attribute', Node_Factory::CALL_TYPE_KEY_VALUE ),
			new Node_Factory_Argument_Handler( 'label', 'set_label' ),
			new Node_Factory_Argument_Handler( 'name', 'set_name' ),
			new Node_Factory_Argument_Handler( 'options', 'set_options', Node_Factory::CALL_TYPE_VALUE ),
			new Node_Factory_Argument_Handler( 'properties', 'set_property', Node_Factory::CALL_TYPE_KEY_VALUE ),
			new Node_Factory_Argument_Handler( 'value', 'set_value' ),
			new Node_Factory_Argument_Handler( 'text_content', 'set_text_content' ),
			new Node_Factory_Argument_Handler( 'children', 'append_child', Node_Factory::CALL_TYPE_VALUE_ITEMS ),
		);
		foreach ( $handlers as $handler ) {
			Node_Factory::register_argument( $handler );
		}
	}
}
