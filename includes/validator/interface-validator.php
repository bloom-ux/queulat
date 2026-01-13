<?php
declare(strict_types=1);

namespace Queulat\Validator;

interface Validator_Interface {
	public function is_valid( $value ): bool;
	public function get_message(): string;
}
