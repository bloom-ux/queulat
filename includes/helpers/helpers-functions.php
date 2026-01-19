<?php
/**
 * General helper functions
 *
 * @package Queulat
 */

use Queulat\Contracts\Hookable_Interface;

/**
 * Call the "init" method on a hookable object, to register WordPress filters and actions
 *
 * @param Hookable_Interface $hookable A hookable object instance.
 * @return void
 */
function queulat_register_hooks( Hookable_Interface $hookable ) {
	$hookable->init();
}
