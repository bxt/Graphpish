<?php
namespace Graphpish\Graph;

abstract class Element extends Weighted {
	private $renderOpts=array();
	
	public function getRenderOpts() {
		return $this->renderOpts;
	}
	public function setRenderOpt($name,$val) {
		$this->renderOpts[$name]=(string)$val;
	}
	public function setRenderOpts(array $opts) {
		foreach($opts as $rOpt=>$rOptVal) {
			if(!is_array($rOptVal)) {
				$this->setRenderOpt($rOpt,$rOptVal);
			}
		}
	}
}
