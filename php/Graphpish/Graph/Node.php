<?php
namespace Graphpish\Graph;

class Node extends Element implements HasLabelI {
	private $label;
	
	private function __construct($label) {
		$this->label=$label;
		self::$byLabel[$label]=$this;
	}
	
	public function getLabel() {
		return $this->label;
	}
	/**
	 * Returns an alphanumeric representation of the label
	 */
	public function getId() {
		return "node".substr(md5($this->label),0,8);
	}
	
	private static $byLabel=array();
	
	public static function getByLabel($label) {
		if(isset(self::$byLabel[$label])) {
			return self::$byLabel[$label];
		}
		return new static($label);
	}
	public static function aggregateNode($label) {
		$node=self::getByLabel($label);
		$node->increaseWeight();
		return $node;
	}
	public static function getAll() {
		$array=array();
		foreach(self::$byLabel as $node) {
			$array[]=$node;
		}
		return $array;
	}
}
