<?php
/**
 * Generator service provider.
 *
 * @package Queulat
 */

declare(strict_types=1);

namespace Queulat\Providers;

use Queulat\Generator\Admin\CPT_Plugin;
use Queulat\Generator\CLI\CPT_Plugin_Command;
use Queulat\Generator\CLI\REST_Field_Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Register generator admin and CLI services.
 */
class Generator_Service_Provider {

	/**
	 * Register services with the container.
	 *
	 * @param ContainerBuilder $container Symfony container.
	 * @return void
	 */
	public function register( ContainerBuilder $container ): void {
		$container
			->register( 'queulat.generator.admin.cpt', CPT_Plugin::class )
			->setPublic( true );

		$container
			->register( 'queulat.generator.cli.cpt', CPT_Plugin_Command::class )
			->setPublic( true );

		$container
			->register( 'queulat.generator.cli.rest_field', REST_Field_Command::class )
			->setPublic( true );

		$container->setParameter(
			'queulat.generator.cli.commands',
			array(
				'queulat.generator.cli.cpt',
				'queulat.generator.cli.rest_field',
			)
		);
	}
}
