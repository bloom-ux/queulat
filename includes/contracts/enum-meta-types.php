<?php
/**
 * Meta field types
 *
 * @package Queulat
 */

declare(strict_types=1);

namespace Queulat;

/**
 * Allowed meta field types
 */
enum Meta_Type: string {
	case string  = 'string';
	case boolean = 'boolean';
	case integer = 'integer';
	case number  = 'number';
	case array   = 'array';
	case object  = 'object';
	case null    = 'null';
}
