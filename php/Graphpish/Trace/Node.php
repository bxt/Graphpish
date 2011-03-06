<?php
namespace Graphpish\Trace;

class Node extends \Graphpish\Graph\Node implements \Graphpish\Graph\hasLabelI {
		public function getRenderOpts() {
		if(!self::$functionColors) {
			self::loadFunctionColors();
		}
		$label=$this->getLabel();
		if(self::$functionColors&&isset(self::$functionColors[$label])) {
			$this->setRenderOpt("color",self::$functionColors[$label]);
		}
		return parent::getRenderOpts();
	}
	private static $functionColors=false;
	protected static function loadFunctionColors() {
		self::$functionColors=array();
		$allFunctions=get_defined_functions();
		foreach($allFunctions['internal'] as $phpfunc) {
			self::$functionColors[$phpfunc]="#00000033";
		}
	}
}
