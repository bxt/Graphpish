<?php
namespace Graphpish\Graph;

class Edge extends Element implements HasLabelI {
	private $from;
	private $to;
	private $label=false;
	
	/**
	 * @Graphpish\Util\ObjectMap\KeyConstructorA(1)
	 */
	public function __construct($from,$to) {
		$this->from=$from;
		$this->to=$to;
	}
	
	/**
	 * @Graphpish\Util\ObjectMap\KeyA(0)
	 */
	public function getFrom() {
		return $this->from;
	}
	
	/**
	 * @Graphpish\Util\ObjectMap\KeyA(1)
	 */
	public function getTo() {
		return $this->to;
	}
	
	public function setLabel($label=false) {
		$this->label=$label;
		return $this;
	}
	
	public function getLabel() {
		if($this->label===false) return $this->getWeight();
		return $this->label.': '.$this->getWeight();
	}

}
