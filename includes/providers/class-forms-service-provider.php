<?php
/**
 * Forms service provider.
 *
 * @package Queulat
 */

declare(strict_types=1);

namespace Queulat\Providers;

use Queulat\Forms\Node_Factory;
use Queulat\Forms\Node_Factory_Argument_Handler;
use Queulat\Forms\Node_Factory_Call_Type;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Configure form-related services.
 */
class Forms_Service_Provider {

	/**
	 * Register form services.
	 *
	 * @param ContainerBuilder $container Symfony container.
	 * @return void
	 */
	public function register( ContainerBuilder $container ): void {
		$this->ensure_default_node_arguments();

		$container
			->register( 'queulat.forms.node_factory', Node_Factory::class )
			->setPublic( true );
	}

	/**
	 * Register default Node Factory argument handlers once.
	 *
	 * @return void
	 */
	private function ensure_default_node_arguments(): void {
		if ( ! empty( Node_Factory::get_registered_arguments() ) ) {
			return;
		}

		$handlers = array(
			new Node_Factory_Argument_Handler( 'attributes', 'set_attribute', Node_Factory_Call_Type::KEY_VALUE ),
			new Node_Factory_Argument_Handler( 'label', 'set_label' ),
			new Node_Factory_Argument_Handler( 'name', 'set_name' ),
			new Node_Factory_Argument_Handler( 'options', 'set_options', Node_Factory_Call_Type::VALUE ),
			new Node_Factory_Argument_Handler( 'properties', 'set_property', Node_Factory_Call_Type::KEY_VALUE ),
			new Node_Factory_Argument_Handler( 'value', 'set_value' ),
			new Node_Factory_Argument_Handler( 'text_content', 'set_text_content' ),
			new Node_Factory_Argument_Handler( 'children', 'append_child', Node_Factory_Call_Type::VALUE_ITEMS ),
		);

		foreach ( $handlers as $handler ) {
			Node_Factory::register_argument( $handler );
		}
	}
}
