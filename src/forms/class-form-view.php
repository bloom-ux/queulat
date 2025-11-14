<?php
	/**
	 * Return the string representation of the form
	 *
	 * @return string HTML markup for the form
	 */

declare(strict_types=1);

namespace Queulat\Forms;

abstract class Form_View implements View_Interface {
	protected $form;

	public function __construct( Element\Form $form ) {
		$this->form = $form;
	}

	abstract public function __toString();
}
