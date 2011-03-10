<?php
namespace Graphpish\Util;

/**
 * Provides additional methods on arrays
 * 
 * Uses arrays like objects, passing them by reference
 */
class ArrayDecorator {
	/**
	 * Holds the decorated array
	 * @var array
	 */
	private $array;
	
	/**
	 * Initialize with the decorated array
	 */
	public function __construct(array &$array) {
		$this->array=&$array;
	}
	/**
	 * Returns the underlaying array
	 */
	public function &get() {
		return $this->array;
	}
	/**
	 * Sotres a value into a deep path specified by keys
	 */
	public function store_deep($keys,$value) {
		$keys=array_values($keys);
		$unit=&$this->array;
		for($i=0;$i<count($keys);$i++) {
			$unit=&$unit[$keys[$i]];
		}
		$unit=$value;
		return $this;
	}
	/**
	 * Retrieves a value from a deep path specified by keys
	 */
	public function get_deep($keys) {
		$keys=array_values($keys);
		$unit=&$this->array;
		for($i=0;$i<count($keys);$i++) {
			$unit=&$unit[$keys[$i]];
		}
		return $unit;
	}
	/**
	 * Works like array_values() but recursively
	 */
	public function flatten() {
		$flat=new \stdClass;
		$flat->a=array();
		array_walk_recursive($this->array,"\\Graphpish\\Util\\ArrayDecorator::flatten_inner",$flat);
		return $flat->a;
	}
	/**
	 * Helper callback method for flatten()
	 */
	private static function flatten_inner($val,$key,$flat) {
		$flat->a[]=$val;
	}
}