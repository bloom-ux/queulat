<?php
/**
 * Basic implementation of Node_List_Interface
 * Extends the SPL ArrayIterator class, so it automatically implements most of
 * the required interface methods
 */

declare(strict_types=1);

namespace Queulat\Forms;

class Node_List extends \ArrayIterator implements Node_List_Interface {
	/**
	 * @inheritDoc
	 */
	public function get_item( $index ): Node_Interface {
		return $this->offsetGet( $index );
	}
}
