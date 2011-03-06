<?php
namespace Graphpish\Graph;

class Edge extends Element {
	private $from;
	private $to;
	
	private function __construct($from,$to) {
		$this->from=$from;
		$this->to=$to;
		if(!isset(self::$byLabels[$from->getLabel()])) {
			self::$byLabels[$from->getLabel()]=array();
		}
		self::$byLabels[$from->getLabel()][$to->getLabel()]=$this;
	}
	
	public function getFrom() {
		return $this->from;
	}
	public function getTo() {
		return $this->to;
	}
	
	private static $byLabels=array();
	
	public static function getByLabels(HasLabelI $from,HasLabelI $to) {
		if(
				isset(self::$byLabels[$from->getLabel()]) &&
				isset(self::$byLabels[$from->getLabel()][$to->getLabel()])
				) {
			return self::$byLabels[$from->getLabel()][$to->getLabel()];
		}
		return new self($from,$to);
	}
	public static function getAll() {
		$array=array();
		foreach(self::$byLabels as $x) {
			foreach($x as $edge) {
				$array[]=$edge;
			}
		}
		return $array;
	}
	public static function aggregateLink($from,$to) {
		$edge=self::getByLabels($from,$to);
		$edge->increaseWeight();
		return $edge;
	}
}
