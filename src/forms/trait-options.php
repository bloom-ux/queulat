<?php
	/**
	 * @inheritDoc
	 * @suppress PhanTypeMismatchReturn
	 */

declare(strict_types=1);

namespace Queulat\Forms;

trait Options_Trait {
	protected $options;

	public function set_options( $options ): Node_Interface {
		$this->options = $options;
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_options() {
		return $this->options;
	}
}
