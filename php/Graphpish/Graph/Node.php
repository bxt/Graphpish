<?php
namespace Graphpish\Graph;

class Node extends Element implements HasLabelI {
	private $label;
	
	/**
	 * @Graphpish\Util\ObjectMap\KeyConstructorA(0)
	 */
	public function __construct($label) {
		$this->label=$label;
	}
	
	/**
	 * @Graphpish\Util\ObjectMap\KeyA(0)
	 */
	public function getLabel() {
		return $this->label;
	}
	/**
	 * Returns an alphanumeric representation of the label
	 */
	public function getId() {
		return "node".substr(md5($this->label),0,8);
	}
}