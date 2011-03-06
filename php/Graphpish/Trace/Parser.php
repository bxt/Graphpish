<?php
namespace Graphpish\Trace;
use Graphpish\Graph\Edge;

class Parser {
	protected $file;
	private $_preNodes=array();
	function __construct($file) {
		$this->file=$file;
	}
	function parse() {
		$this->createRootnode();
		$handle = @fopen($this->file, "r");
		if ($handle) {
			while (($buffer=fgets($handle, 4096))!==false) {
				$this->parseLine($buffer);
			}
			if (!feof($handle)) {
				throw new \Exception("Unexpected fgets() fail");
			}
			fclose($handle);
		} else {
			throw new \Exception("Can't open file for reading: {$this->file}");
		}
		return $this;
	}
	//private $_preLvl=0;
	function parseLine($l) {
		$l=substr($l,26);
		preg_match('/^( *)(.*)/s',$l,$startWs);
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
