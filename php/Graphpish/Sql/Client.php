<?php
namespace Graphpish\Sql;
use Graphpish\Util\DeepIniParser;
use Graphpish\Util\ObjectMap\StorePretty;

class Client implements \Graphpish\Cli\PluginI {
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
			$q=sprintf('SELECT %s as id, %s as label FROM %s as nodes ',
				$nodeTypeSqlInfo["id"],$nodeTypeSqlInfo["label"],$nodeTypeSqlInfo["table"]);
			$result=$conn->query($q);
			if(!$result) throw new SqlException($conn,'on ['.$nodeType.']');
			foreach ($result as $sqlNode) {
				$label=$nodeType.'\\'.$sqlNode['label'];
				$id=$nodeType.'\\'.$sqlNode['id'];
				$node=$nodes($id)->increaseWeight(1)->setLabel($label);
				if(isset($nodeTypeSqlInfo['display'])) {
					$node->setRenderOpts($nodeTypeSqlInfo['display']);
				}
			}
		}
		
		foreach($opts["edge"] as $fromNT=>$toList) {
			foreach($toList as $toNT=>$edgeTypeSqlInfo) {
				$q=sprintf('SELECT %s as id1, %s as id2, count(*) as weight FROM %s as edges GROUP BY id1,id2',
					$edgeTypeSqlInfo["id1"],$edgeTypeSqlInfo["id2"],$edgeTypeSqlInfo["table"]);
				$result=$conn->query($q);
			if(!$result) throw new SqlException($conn,'on ['.$fromNT.'-'.$toNT.']');
				foreach ($result as $sqlEdge) {
					$id1=$fromNT.'\\'.$sqlEdge['id1'];
					$id2=$toNT.'\\'.$sqlEdge['id2'];
					$node1=$nodes($id1)->increaseWeight(1);
					$node2=$nodes($id2)->increaseWeight(1);
					$edges($node1,$node2)->increaseWeight($sqlEdge['weight']);
				}
			}
		}
		
		return $this;
	}
	
	public function getGraph() {
		return new \Graphpish\Graph\Graph($this->nodes->dump(),$this->edges->dump());
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
	public function cli(array $argv) {
		if(count($argv)<1) {
			throw new \Graphpish\Cli\ParameterException("Sql Plugin accepts at least 1 argument.");
		}
		foreach ($argv as $qsqlFile) {
			$this->addSource($qsqlFile);
		}
		$this->process();
	}
}
