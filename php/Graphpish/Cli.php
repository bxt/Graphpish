<?php
namespace Graphpish;

abstract class Cli {
	public static function run($argv){
		if(count($argv)==2) {
			
			if (is_dir($argv[1])) {
				$p=new Filetree\Traversor();
				$graph=$p->traverse($argv[1])->getGraph();
				
			} elseif(strrchr($argv[1],'.')==".xml") {
				$p=new Xml\Reader();
				$graph=$p->read($argv[1])->getGraph();
				
			} elseif(strrchr($argv[1],'.')==".xt") {
				$p=new Trace\Parser();
				$graph=$p->parse($argv[1])->getGraph();
				
			} elseif (strrchr($argv[1],'.')==".gsql") {
				$p=new Sql\Client();
				$graph=$p->addSource($argv[1])->process()->getGraph();
				
			} else {
				die("Unrecognized file extension. ");
			}
			
			$r=new Graph\Render();
			$r->r($graph);
			
		} else {
			echo 'Usage: php '.__FILE__.' trace.xt|dbconfig.gsql'."\n";
		}
	}
}