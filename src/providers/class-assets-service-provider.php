<?php
/**
 * Assets service provider.
 *
 * @package Queulat
 */

declare(strict_types=1);

namespace Queulat\Providers;

use Queulat\Helpers\Webpack_Asset_Loader;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Register asset loading services.
 */
class Assets_Service_Provider {

	/**
	 * Register the services in the container.
	 *
	 * @param ContainerBuilder $container Symfony container.
	 * @return void
	 */
	public function register( ContainerBuilder $container ): void {
		$plugin_root = $container->hasParameter( 'queulat.plugin_root' ) ? $container->getParameter( 'queulat.plugin_root' ) : dirname( __DIR__, 2 );
		$plugin_file = $container->hasParameter( 'queulat.plugin_file' ) ? $container->getParameter( 'queulat.plugin_file' ) : $plugin_root . '/queulat.php';
		$dist_dir    = $container->hasParameter( 'queulat.dist_dir' ) ? $container->getParameter( 'queulat.dist_dir' ) : $plugin_root . '/dist';
		$dist_url    = plugins_url( 'dist', $plugin_file );

		$container
			->register( 'queulat.assets.loader', Webpack_Asset_Loader::class )
			->setArguments(
				array(
					'queulat',
					$dist_dir,
					$dist_url,
				)
			)
			->setPublic( true );

		$container->setParameter( 'queulat.assets.styles.default', 'dist/admin.css' );
	}
}
