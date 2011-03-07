<?php
namespace Graphpish\Sql;
use Graphpish\Graph\Edge;
use Graphpish\Graph\Node;

class Client {
	private $options;
	public function __construct($file=false) {
		$this->options=new Parser();
		if($file) {
			$this->addSource($file);
		}
	}
	public function addSource($file) {
		$this->options->process(parse_ini_file($file,true));
		return $this;
	}
	public function process() {
		$opts=$this->options->getOpts();
		$conn=$this->connect();
		foreach($opts["node"] as $nodeType=>$nodeTypeSqlInfo) {
			$q='SELECT '.$nodeTypeSqlInfo["id"].' as id,'.$nodeTypeSqlInfo["label"].' as label FROM '.$nodeTypeSqlInfo["table"].' as nodes';
			var_dump($q);
			foreach ($conn->query($q) as $sqlNode) {
				$node=Node::aggregateNode($nodeType.'\\'.$sqlNode['label']);
			}
		}
		
		return $this;
	}
	/*
	Node::aggregateNode($function);
	Edge::aggregateLink($this->_preNodes[$lvl-1],$node);
	*/
	public function getArrays() {
		return array("nodes"=>Node::getAll(),"edges"=>Edge::getAll());
	}
	private function connect() {
		$opts=$this->options->getOpts();
		if(isset($opts["connection"]["user"])&&isset($opts["connection"]["password"])) {
			$conn=new \PDO($opts["connection"]["dsn"],$opts["connection"]["user"],$opts["connection"]["password"]);
		} else {
			$conn=new \PDO($opts["connection"]["dsn"]);
		}
		return $conn;
	}
}
