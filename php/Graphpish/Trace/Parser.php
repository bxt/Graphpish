<?php
namespace Graphpish\Trace;
use Graphpish\Graph\Edge;

class Parser extends \Graphpish\Util\FilelinesParser {
	private $_preNodes=array();
	function parse($file) {
		$this->createRootnode();
		return parent::parse($file);
	}
	//private $_preLvl=0;
	function parseLine($l) {
		$l=substr($l,26);
		preg_match('/^( *)(.*)/s',$l,$startWs);
		/* 
		 * One could of course use ltrim($l," ") here, but it turns out 
		 * if you need the indentation level too another strlen for 
		 * calculating the length difference really sums up. 
		 * @see https://gist.github.com/846504
		 */
		if(isset($startWs[0])) {
			preg_match('/^-> (.+)\((.*)\) (.+):(\d+)$/',$startWs[2],$functionCall);
			if(isset($functionCall[0])) {
				$lvl=strlen($startWs[1])/2;
				$function=$functionCall[1];
				$args=$functionCall[2];
				$file=$functionCall[3];
				$line=$functionCall[4];
				$node=Node::aggregateNode($function);
				if(isset($this->_preNodes[$lvl-1])) {
					Edge::aggregateLink($this->_preNodes[$lvl-1],$node);
				}
				$this->_preNodes[$lvl]=$node;
				//$this->_preLvl=$lvl;
			} else {
				//echo "BAD: $startWs[2]\n";
			}
		} else {
			//echo "BADWS: $l\n";
		}
	}
	function createRootnode() {
		$node=Node::aggregateNode("MAIN");
		$this->_preNodes[-1]=$node;
	}
	function getArrays() {
		return array("nodes"=>Node::getAll(),"edges"=>Edge::getAll());
	}
}
