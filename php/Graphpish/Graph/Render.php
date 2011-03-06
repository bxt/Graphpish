<?php
namespace Graphpish\Graph;

class Render {
	function rStart() {
		echo 'strict digraph G {'.NL.NL;
		echo '  overlap=false'.NL;
		echo '  root=node186495f7'.NL;
		echo '  splines=true'.NL;
		echo '  epsilon=0.0000001'.NL;
		echo '  sep=.2'.NL;
		echo NL;
		echo '  node [fontsize=9]'.NL;
		echo '  edge [fontsize=7]'.NL;
		echo NL.NL;
		return $this;
	}
	function rNodes($nodes) {
		foreach($nodes as $node) {
			$weight=$node->getWeight();
			echo '  '.$node->getId().' [';
			echo ' penwidth='.self::strokeWith($weight).', ';
			echo ' label = '.self::labelEsc($node->getLabel().' ('.$weight.')');
			echo self::attributesFromArray($node->getRenderOpts());
			echo ' ];'.NL;
		}
		echo NL.NL;
		return $this;
	}
	
	function rEdges($edges) {
		foreach($edges as $edge) {
			$weight=$edge->getWeight();
			echo '  '.$edge->getFrom()->getId().' -> '.$edge->getTo()->getId().' [';
			echo ' penwidth='.self::strokeWith($weight).',';
			echo ' weight='.(log($weight,10)*3).',';
			echo ' label='.self::labelEsc($weight).',';
			echo ' color="#'.static::randomColor(true).'99"';
			echo self::attributesFromArray($edge->getRenderOpts());
			echo ' ];'.NL;
		}
		echo NL.NL;
		return $this;
	}
	
	function rEnd() {
	echo '}';
	echo NL.NL;
	return $this;
	}
	protected static function strokeWith($weight) {
		return max(log($weight,10)+($weight/10000)+0.5,1);
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
		return '"'.str_replace(array('"','\\'),array('\\','\\\\'),$label).'"';
	}
	protected static function attributesFromArray($attr_array) {
		$attr_string='';
		$attr_string_list=array();
		foreach ($attr_array as $attr_name=>$attr_val) {
			$attr_string.=', '.$attr_name.'='.self::labelEsc($attr_val);
		}
		return $attr_string;
	}
}
