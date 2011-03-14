<?php
namespace Graphpish\Render;

class Springy implements \Graphpish\Cli\RendererI {
	const NL="\n";
	const JQ_URL="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js";
	const SPRINGY_URL="https://github.com/tub/springy/raw/master/springy.js";
	const SPRINGYUI_URL="https://github.com/tub/springy/raw/master/springyui.js";
	function __construct() {
		
	}
	function render(\Graphpish\Graph\Graph $graph) {
		$this->rStart($graph->getRoot(),$graph)->rNodes($graph->getNodes())->rEdges($graph->getEdges())->rEnd();
	}
	function rStart($root=false,$graph=null) {
		echo '<html>'.self::NL;
		echo '<body>'.self::NL;
		echo '  <script src="'.self::JQ_URL.'"></script>'.self::NL;
		echo '  <script src="'.self::SPRINGY_URL.'"></script>'.self::NL;
		echo '  <script src="'.self::SPRINGYUI_URL.'"></script>'.self::NL;
		echo '  <script>'.self::NL;
		echo '    var graph = new Graph();'.self::NL;
		echo self::NL.self::NL;
		return $this;
	}
	function rNodes($nodes) {
		foreach($nodes as $node) {
			echo '    var '.$node->getRenderId().' = graph.newNode({label: '.self::labelEsc($node->getLabel()).'});'.self::NL;
		}
		echo self::NL.self::NL;
		return $this;
	}
	
	function rEdges($edges) {
		foreach($edges as $edge) {
			echo '    graph.newEdge('.$edge->getFrom()->getRenderId().', '.$edge->getTo()->getRenderId().', {color: \'#'.static::randomColor(true).'\'});'.self::NL;
		}
		echo self::NL.self::NL;
		return $this;
	}
	
	function rEnd() {
		echo '    jQuery(document).ready(function(){'.self::NL;
		echo '        jQuery(\'#springyspace\').springy({ \'graph\': graph });'.self::NL;
		echo '    });'.self::NL;
		echo '  </script>'.self::NL;
		echo '  <canvas id="springyspace" width="640" height="480" />'.self::NL;
		echo '</body>'.self::NL;
		echo '</html>'.self::NL;
		echo self::NL.self::NL;
		return $this;
	}
	protected static function randomColor($dark=false) {
		if($dark) {
			$r1=rand(0,150);
			$r2=rand(0,150);
			$r3=rand(0,150);
			return sprintf('%02x%02x%02x',$r1,$r2,$r3);
		} else {
			return sprintf('%02x%02x%02x',rand(0,255),rand(0,255),rand(0,255));
		}
	}
	protected static function labelEsc($label) {
		return '\''.str_replace(array('\'','\\'),array('\\\'','\\\\'),$label).'\'';
	}
	protected static function attributesFromArray($attr_array) {
		return json_encode($attr_array);
	}
}
