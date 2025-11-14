<?php
/**
 * Call types for the Node Factory argument handler.
 *
 * @package Queulat
 */

declare(strict_types=1);

namespace Queulat\Forms;

/**
 * Enumerates the supported call strategies for Node_Factory arguments.
 */
enum Node_Factory_Call_Type: string {
	case VALUE       = 'CALL_VALUE';
	case ARRAY       = 'CALL_ARRAY';
	case KEY_VALUE   = 'CALL_KEY_VALUE';
	case VALUE_ITEMS = 'CALL_VALUE_ITEMS';
}
