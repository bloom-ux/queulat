<?php

namespace Queulat\Helpers;

class Term_Options extends \ArrayIterator {
	private $terms = array();
	public function __construct( array $args, array $flags = array() ) {
		$args        = wp_parse_args(
			$args,
			array(
				'orderby' => 'name',
				'order'   => 'ASC',
			)
		);
		$this->terms = get_terms( $args );
		if ( isset( $flags['show_option_none'] ) && $flags['show_option_none'] ) {
			array_unshift(
				$this->terms,
				(object) array(
					'name'    => _x( '(None)', 'null term option', 'queulat' ),
					'term_id' => 0,
				)
			);
		}
		parent::__construct( $this->terms );
	}
	public function current() {
		return parent::current()->name;
	}
	public function key() {
		$key = parent::key();
		return $this->terms[ $key ]->term_id;
	}
	public function getArrayCopy() {
		return wp_list_pluck( $this->terms, 'name', 'term_id' );
	}
}
