<?php
namespace Graphpish\Trace;
use Graphpish\Util\ObjectMap\StorePretty;

class ClassParser extends \Graphpish\Util\FilelinesParser implements \Graphpish\Cli\PluginI {
	private $_preNodes=array();
	const ROOT="MAIN";
	private $nodes;
	private $edges;
	public function __construct() {
		$this->nodes=new StorePretty(1,'Graphpish\Trace\Node');
		$this->edges=new StorePretty(2,'Graphpish\Graph\Edge');
	}
	public function parse($file) {
		$this->createRootnode();
		return parent::parse($file);
	}
	//private $_preLvl=0;
	public function parseLine($l) {
		$nodes=$this->nodes;
		$edges=$this->edges;
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
				$class=substr($function,0,strrpos($function,'->'));
				/*
				$args=$functionCall[2];
				$file=$functionCall[3];
				$line=$functionCall[4];
				*/
				if($class!=''&&strpos($class,'SplClassLoader')===false) {
					$node=$nodes($class)->increaseWeight(1);
					if(isset($this->_preNodes[$lvl-1])) {
						$edges($this->_preNodes[$lvl-1],$node)->increaseWeight(1);;
					}
					$this->_preNodes[$lvl]=$node;
					//$this->_preLvl=$lvl;
				}
			} else {
				//echo "BAD: $startWs[2]\n";
			}
		} else {
			//echo "BADWS: $l\n";
		}
	}
	private function createRootnode() {
		$nodes=$this->nodes;
		$node=$nodes(static::ROOT)->increaseWeight(1);
		$this->_preNodes[-1]=$node;
	}
	public function getGraph() {
		return new \Graphpish\Graph\Graph($this->nodes->dump(),$this->edges->dump(),$this->nodes->get(static::ROOT));
	}
	public function cli(array $argv) {
		if(count($argv)!=1) {
			throw new \Graphpish\Cli\ParameterException("XDebug Plugin accepts exactly 1 argument.");
		}
		$this->parse($argv[0]);
	}
}
