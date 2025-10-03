<?php
/**
 * Meta field types
 *
 * @package Queulat
 */

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
}
