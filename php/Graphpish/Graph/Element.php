<?php
namespace Graphpish\Graph;

class Element extends Weighted {
	private $renderOpts=array();
	
	public function getRenderOpts() {
		return $this->renderOpts;
	}
	public function setRenderOpt($name,$val) {
		$this->renderOpts[$name]=$val;
	}
}
