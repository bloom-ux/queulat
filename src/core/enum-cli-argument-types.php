<?php
/**
 * Allowed command line argument types
 *
 * Beware that flag arguments can be negated with --no-flag.
 *
 * @package Queulat
 */

namespace Queulat;

/**
 * Allowed command line argument types
 */
enum CLI_Argument_Types: string {
	case positional = 'positional';
	case assoc      = 'assoc';
	case flag       = 'flag';
}
