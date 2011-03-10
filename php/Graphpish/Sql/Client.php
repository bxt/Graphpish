<?php
namespace Graphpish\Sql;
use Graphpish\Util\DeepIniParser;
use Graphpish\Util\ObjectMap\StorePretty;

class Client {
	private $options;
	private $edges;
	private $nodes;
	public function __construct($file=false) {
		$this->options=new DeepIniParser();
		$this->nodes=new StorePretty(1,'Graphpish\Graph\Node');
		$this->edges=new StorePretty(2,'Graphpish\Graph\Edge');
		$this->options->process(array("node"=>array(),"edge"=>array()));
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
		$nodes=$this->nodes; // :(
		$edges=$this->edges;
		foreach($opts["node"] as $nodeType=>$nodeTypeSqlInfo) {
			$q='SELECT '.$nodeTypeSqlInfo["id"].' as id,'.$nodeTypeSqlInfo["label"].' as label FROM '.$nodeTypeSqlInfo["table"].' as nodes';
			foreach ($conn->query($q) as $sqlNode) {
				//$label=$nodeType.'\\'.$sqlNode['label'];
				$label=$nodeType.'\\'.$sqlNode['id'];
				$nodes($label)->increaseWeight(1);
			}
		}
		
		foreach($opts["edge"] as $fromNT=>$toList) {
			foreach($toList as $toNT=>$edgeTypeSqlInfo) {
				$q='SELECT '.$edgeTypeSqlInfo["id1"].' as id1,'.$edgeTypeSqlInfo["id2"].' as id2, count(*) as weight  FROM '.$edgeTypeSqlInfo["table"].' as edges GROUP BY id1,id2';
				foreach ($conn->query($q) as $sqlEdge) {
					$label1=$fromNT.'\\'.$sqlEdge['id1'];
					$label2=$toNT.'\\'.$sqlEdge['id2'];
					$node1=$nodes($label1)->increaseWeight(1);
					$node2=$nodes($label2)->increaseWeight(1);
					$edges($node1,$node2)->increaseWeight($sqlEdge['weight']);
				}
			}
		}
		
		return $this;
	}
	/*
	Node::aggregateNode($function);
	Edge::aggregateLink($this->_preNodes[$lvl-1],$node);
	*/
	public function getArrays() {
		return array("nodes"=>$this->nodes->dump(),"edges"=>$this->edges->dump());
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
