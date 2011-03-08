<?php
namespace Graphpish;

abstract class Cli {
	public static function run($argv){
		if(count($argv)==2) {
			
			if(strrchr($argv[1],'.')==".xt") {
				$p=new Trace\Parser();
				$result=$p->parse($argv[1])->getArrays();
				$r=new Graph\Render();
				$r->rStart()->rNodes($result["nodes"])->rEdges($result["edges"])->rEnd();
				
			} elseif (strrchr($argv[1],'.')==".gsql") {
				$p=new Sql\Client();
				$result=$p->addSource($argv[1])->process()->getArrays();
				//var_dump($result);
				$r=new Graph\Render();
				$r->rStart()->rNodes($result["nodes"])->rEdges($result["edges"])->rEnd();
				
			} else {
				die("Unrecognized file extension. ");
			}
			
		} else {
			echo 'Usage: php '.__FILE__.' trace.xt|dbconfig.gsql'.NL;
		}
	}
}