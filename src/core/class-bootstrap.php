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

use Queulat\Contracts\CLI_Command_Interface;
use Queulat\Helpers\Container_Factory;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Hook Queulat into WordPress
 */
class Bootstrap {

	/**
	 * Service container instance.
	 *
	 * @var ContainerBuilder
	 */
	private $container;

	/**
	 * Whether the container has been booted.
	 *
	 * @var bool
	 */
	private $container_booted = false;

	/**
	 * Initialize the bootstrapper.
	 *
	 * @param null|ContainerBuilder $container Optional pre-configured container.
	 */
	public function __construct( ?ContainerBuilder $container = null ) {
		if ( $container instanceof ContainerBuilder ) {
			$this->container        = $container;
			$this->container_booted = true;
		}
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
		add_action( 'plugins_loaded', array( $this, 'boot_container' ), 90 );
		add_action( 'plugins_loaded', array( $this, 'init_generator_admin' ), 100 );
		add_action( 'plugins_loaded', array( $this, 'init_cli_commands' ), 100 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ), 90 );
		add_action( 'init', array( $this, 'load_translations' ) );
	}

	/**
	 * Retrieve the service container.
	 *
	 * @return ContainerBuilder
	 */
	public function get_container(): ContainerBuilder {
		return $this->ensure_container();
	}

	/**
	 * Boot the service container during the plugins_loaded hook.
	 *
	 * @return void
	 */
	public function boot_container(): void {
		$this->ensure_container();
	}

	/**
	 * Initialize custom post type generator admin screen
	 *
	 * @return void
	 */
	public function init_generator_admin() {
		$container = $this->ensure_container();

		if ( ! $container->has( 'queulat.generator.admin.cpt' ) ) {
			return;
		}

		$admin = $container->get( 'queulat.generator.admin.cpt' );

		if ( method_exists( $admin, 'init' ) ) {
			$admin->init();
		}
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

		$container = $this->ensure_container();

		if ( ! $container->hasParameter( 'queulat.generator.cli.commands' ) ) {
			return;
		}

		$command_services = (array) $container->getParameter( 'queulat.generator.cli.commands' );

		foreach ( $command_services as $service_id ) {
			if ( ! $container->has( $service_id ) ) {
				continue;
			}

			$command = $container->get( $service_id );

			if ( ! $command instanceof CLI_Command_Interface ) {
				continue;
			}

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
		$container = $this->ensure_container();

		if ( ! $container->has( 'queulat.assets.loader' ) ) {
			return;
		}

		$loader      = $container->get( 'queulat.assets.loader' );
		$style_entry = $container->hasParameter( 'queulat.assets.styles.default' ) ? $container->getParameter( 'queulat.assets.styles.default' ) : 'dist/admin.css';

		if ( is_callable( array( $loader, 'enqueue_style' ) ) ) {
			$loader->enqueue_style( $style_entry );
		}
	}

	/**
	 * Ensure the container is available.
	 *
	 * @return ContainerBuilder
	 */
	private function ensure_container(): ContainerBuilder {
		if ( ! $this->container_booted ) {
			$this->container        = Container_Factory::build();
			$this->container_booted = true;
		}
		return $this->container;
	}
}
