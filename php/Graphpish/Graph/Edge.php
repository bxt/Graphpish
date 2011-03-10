<?php
namespace Graphpish\Graph;

class Edge extends Element {
	private $from;
	private $to;
	
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
	
}
