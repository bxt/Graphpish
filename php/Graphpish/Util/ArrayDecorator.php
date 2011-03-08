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
	function get_deep($keys) {
		$keys=array_values($keys);
		$unit=&$this->array;
		for($i=0;$i<count($keys);$i++) {
			$unit=&$unit[$keys[$i]];
		}
		return $unit;
	}
	function flatten() {
		$flat=new \stdClass;
		$flat->a=array();
		array_walk_recursive($this->array,"\\Graphpish\\Util\\ArrayDecorator::flatten_inner",$flat);
		return $flat->a;
	}
	private static function flatten_inner($val,$key,$flat) {
		$flat->a[]=$val;
	}
}