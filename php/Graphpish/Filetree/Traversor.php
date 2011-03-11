<?php
namespace Graphpish\Filetree;
use Graphpish\Util\DeepIniParser;
use Graphpish\Util\ObjectMap\StorePretty;

class Traversor implements \Graphpish\Cli\PluginI {
	private $options;
	private $edges;
	private $nodes;
	private $root;
	public function __construct() {
		$this->nodes=new StorePretty(1,'Graphpish\Filetree\Node');
		$this->edges=new StorePretty(2,'Graphpish\Graph\Edge');
		$this->root=null;
	}
	public function traverse($dir) {
		$nodes=$this->nodes;
		$edges=$this->edges;
		
		$parent=$nodes($dir);
		if(!$this->root) $this->root=$parent;
		
		$files=scandir($dir);
		foreach($files as $file) {
			if($file[0]=='.') continue;
			$path=$dir.'/'.$file;
			$node=$nodes($path)->increaseWeight()->setLabel($file);
			$edges($parent,$node)->increaseWeight();
			if(is_dir($path)) {
				$node->setRenderOpt("shape","box");
				$this->traverse($path);
			}
		}
		
		return $this;
	}
	
	public function getGraph() {
		return new \Graphpish\Graph\Graph($this->nodes->dump(),$this->edges->dump(),$this->root);
	}
	public function cli(array $argv){
		if(count($argv)!=1) {
			throw new \Graphpish\Cli\ParameterException("File Plugin accepts exactly 1 argument.");
		}
		$this->traverse($argv[0]);
	}
}
