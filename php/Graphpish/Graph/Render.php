<?php
namespace Graphpish\Graph;

class Render {
	const NL="\n";
	function rStart($root=false) {
		echo 'strict digraph G {'.self::NL.self::NL;
		echo '  overlap=false'.self::NL;
		if($root!==false) {
			echo '  root='.$root->getRenderId().self::NL;
		}
		echo '  splines=true'.self::NL;
		echo '  epsilon=0.0000001'.self::NL;
		echo '  sep=.2'.self::NL;
		echo self::NL;
		echo '  node [fontsize=9]'.self::NL;
		echo '  edge [fontsize=7]'.self::NL;
		echo self::NL.self::NL;
		return $this;
	}
	function rNodes($nodes) {
		foreach($nodes as $node) {
			$weight=$node->getWeight();
			echo '  '.$node->getRenderId().' [';
			echo ' penwidth='.self::strokeWith($weight).', ';
			echo ' label = '.self::labelEsc($node->getLabel());
			echo self::attributesFromArray($node->getRenderOpts());
			echo ' ];'.self::NL;
		}
		echo self::NL.self::NL;
		return $this;
	}
	
	function rEdges($edges) {
		foreach($edges as $edge) {
			$weight=$edge->getWeight();
			echo '  '.$edge->getFrom()->getRenderId().' -> '.$edge->getTo()->getRenderId().' [';
			echo ' penwidth='.self::strokeWith($weight).',';
			echo ' weight='.(log($weight,10)*3).',';
			echo ' label='.self::labelEsc($edge->getLabel()).',';
			echo ' color="#'.static::randomColor(true).'99"';
			echo self::attributesFromArray($edge->getRenderOpts());
			echo ' ];'.self::NL;
		}
		echo self::NL.self::NL;
		return $this;
	}
	
	function rEnd() {
	echo '}';
	echo self::NL.self::NL;
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
