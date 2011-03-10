<?php
namespace Graphpish\Graph;

class Graph extends Element {
	private $nodes;
	private $edges;
	private $root=false;
	
	public function __construct($nodes,$edges,$root=false) {
		$this->nodes=$nodes;
		$this->edges=$edges;
		$this->root=$root;
	}
	
	public function getNodes() {
		return $this->nodes;
	}
	
	public function getEdges() {
		return $this->edges;
	}
	
	public function getRoot() {
		return $this->root;
	}
	
}
