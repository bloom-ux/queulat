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

declare(strict_types=1);

namespace Queulat;

use Queulat\Generator\Admin\CPT_Plugin;
use Queulat\Contracts\Asset_Loader_Interface;
use Queulat\Generator\CLI\CPT_Plugin_Command;
use Queulat\Generator\CLI\REST_Field_Command;

/**
 * Hook Queulat into WordPress
 */
class Bootstrap {

	/**
	 * Bootstrap Queulat
	 *
	 * @param Asset_Loader_Interface $asset_loader An instance of a static assets loader.
	 */
	public function __construct( private Asset_Loader_Interface $asset_loader ) {
	}

	/**
	 * Initialize the plugin functionality.
	 *
	 * Sets up action hooks for admin initialization, asset enqueuing,
	 * and registers default node factory arguments.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'plugins_loaded', array( $this, 'init_generator_admin' ), 100 );
		add_action( 'plugins_loaded', array( $this, 'init_cli_commands' ), 100 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ), 90 );
		add_action( 'init', array( $this, 'load_translations' ) );
	}

	/**
	 * Initialize custom post type generator admin screen
	 *
	 * @return void
	 */
	public function init_generator_admin() {
		$generator_admin = new CPT_Plugin();
		$generator_admin->init();
	}

	/**
	 * Initialize wp-cli commands
	 *
	 * @return void
	 */
	public function init_cli_commands() {
		if ( ! is_callable( array( '\WP_CLI', 'add_command' ) ) ) {
			return;
		}

		$commands = array(
			new CPT_Plugin_Command(),
			new REST_Field_Command(),
		);

		foreach ( $commands as $command ) {
			\WP_CLI::add_command(
				$command->get_name(),
				$command->get_callable(),
				$command->get_args()
			);
		}
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
		$this->asset_loader->enqueue_script( 'admin.css' );
	}
}
