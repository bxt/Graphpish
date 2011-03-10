<?php
namespace Graphpish\Graph;

class Weighted {
	private $weight=0;
	public function getWeight() {
		return $this->weight;
	}
	public function increaseWeight($by=1) {
		$this->weight+=$by;
		return $this;
	}
}
