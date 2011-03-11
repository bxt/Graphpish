<?php
namespace Graphpish\Cli;

abstract class Runner {
	public static function run($argv=array()){
		$scriptname=array_shift($argv);
		switch(count($argv)) {
		case 0:throw new ParameterException("No arguments specified!");
		case 1:
			if (is_dir($argv[0]))
				$plugin='Graphpish\\Filetree\\Traversor';
			elseif(strrchr($argv[0],'.')==".xml")
				$plugin='Graphpish\\Xml\\Reader';
			elseif(strrchr($argv[0],'.')==".xt")
				$plugin='Graphpish\\Trace\\Parser';
			elseif (strrchr($argv[0],'.')==".gsql")
				$plugin='Graphpish\\Sql\\Client';
			else
				throw new ParameterException("Unrecognized file extension");
			break;
		default:
			if( ($argv[0]=="--plugin"||$argv[0]=="-p") && isset($argv[1])) {
				$plugin=$argv[1];
				array_shift($argv);
				array_shift($argv);
			}
			break;
		}
		if(isset($plugin)) {
			$pluginRC=new \ReflectionClass($plugin);
			if(!$pluginRC->implementsInterface("Graphpish\Cli\PluginI")) {
				throw new ParameterException("Tried to load invalid plugin");
			}
			$pluginIns=$pluginRC->newInstance();
			$pluginIns->cli($argv);
			$graph=$pluginIns->getGraph();
			$r=new \Graphpish\Graph\Render();
			$r->r($graph);
		} else {
			throw new ParameterException("No plugin loaded");
		}
	}
}

