<?php
namespace Graphpish\Graph;

class Node extends Element implements HasLabelI {
	private $id;
	private $label=false;
	const LABEL_FMT="%s (%s)";
	
	/**
	 * @Graphpish\Util\ObjectMap\KeyConstructorA(0)
	 */
	public function __construct($id) {
		$this->id=$id;
	}
	
	/**
	 * Used as internal ID to identify the node
	 * @Graphpish\Util\ObjectMap\KeyA(0)
	 */
	public function getId() {
		return $this->id;
	}
		
	/**
	 * Used as ID for renders, need an alphanumeric
	 */
	public function getRenderId() {
		return "node".substr(md5($this->getId()),0,8);
	}
	
	public function setLabel($label=false) {
		$this->label=$label;
		return $this;
	}
	
	/**
	 * Used as Label for renders, defaults to Id
	 */
	public function getLabel() {
		if($this->label===false) return sprintf(static::LABEL_FMT,$this->getId(),$this->getWeight());
		return sprintf(static::LABEL_FMT,$this->label,$this->getWeight());
	}

}