<?php
namespace Graphpish\Util;

/**
 * Provides additional methods on arrays
 * 
 * Uses arrays like objects, passing them by reference
 */
class ArrayDecorator {
	public $array;
	function __construct(&$array) {
		$this->array=&$array;
	}
	function &get() {
		return $this->array;
	}
	function store_deep($keys,$value) {
		$keys=array_values($keys);
		$unit=&$this->array;
		for($i=0;$i<count($keys);$i++) {
			$unit=&$unit[$keys[$i]];
		}
		$unit=$value;
		return $this;
	}
}
