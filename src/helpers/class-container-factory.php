<?php
/**
 * Build and configure the Symfony service container for Queulat.
 *
 * @package Queulat
 */

declare(strict_types=1);

namespace Queulat\Helpers;

use Queulat\Providers\Assets_Service_Provider;
use Queulat\Providers\Forms_Service_Provider;
use Queulat\Providers\Generator_Service_Provider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * Helper to build the Symfony container used across the plugin.
 */
class Container_Factory {

	private static $instance;

	public static function get_instance() {
		return static::$instance;
	}

	/**
	 * Build the service container.
	 *
	 * @param array $providers Optional list of provider instances.
	 * @param array $parameters Optional extra parameters for the container.
	 * @return ContainerBuilder
	 */
	public static function build( array $providers = array(), array $parameters = array() ): ContainerBuilder {
		$defaults = array(
			'queulat.plugin_root' => dirname( __DIR__, 2 ),
			'queulat.plugin_file' => dirname( __DIR__, 2 ) . '/queulat.php',
			'queulat.dist_dir'    => dirname( __DIR__, 2 ) . '/dist',
		);

		$parameter_bag = new ParameterBag( array_merge( $defaults, $parameters ) );
		$container     = new ContainerBuilder( $parameter_bag );

		if ( empty( $providers ) ) {
			$providers = array(
				new Assets_Service_Provider(),
				new Forms_Service_Provider(),
				new Generator_Service_Provider(),
			);
		}

		/**
		 * Allow third-parties to modify the provider list before registration.
		 *
		 * @param array              $providers Array of provider instances.
		 * @param ContainerBuilder   $container Service container.
		 */
		$providers = apply_filters( 'queulat_service_providers', $providers, $container );

		foreach ( $providers as $provider ) {
			if ( method_exists( $provider, 'register' ) ) {
				$provider->register( $container );
			}
		}

		/**
		 * Let third-parties adjust the container before compilation.
		 *
		 * @param ContainerBuilder $container Service container.
		 */
		do_action( 'queulat_container_configure', $container );

		$container->compile( true );

		static::$instance = $container;

		return $container;
	}
}
