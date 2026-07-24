<?php
declare(strict_types=1);

namespace Queulat\Forms;

abstract class HTML_Form_Element extends HTML_Element implements Form_Node_Interface {
	use Form_Control_Trait;
}
