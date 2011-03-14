<?php
namespace Graphpish\Cli;

abstract class Runner {
	public static function run($argv=array()){
		$scriptname=array_shift($argv);
		$renderer='Graphpish\\Render\\Dot';
		
		if(count($argv)==0) throw new ParameterException("No arguments specified!");
		while (count($argv)>1) {
			if( ($argv[0]=="--plugin"||$argv[0]=="-p") && isset($argv[1]) ) {
				$plugin=$argv[1];
				array_shift($argv);
				array_shift($argv);
			} elseif( ($argv[0]=="--renderer"||$argv[0]=="-r") && isset($argv[1]) ) {
				$renderer=$argv[1];
				array_shift($argv);
				array_shift($argv);
			} else {
				break;
			}
		}
		
		if(!isset($plugin)&&isset($argv[0])) {
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
		}
		if(!isset($plugin)) {
			throw new ParameterException("No plugin loaded");
		}
		
		$pluginRC=new \ReflectionClass($plugin);
		if(!$pluginRC->implementsInterface("Graphpish\Cli\PluginI")) {
			throw new ParameterException("Tried to load invalid plugin");
		}
		$renderRC=new \ReflectionClass($renderer);
		if(!$renderRC->implementsInterface("Graphpish\Cli\RendererI")) {
			throw new ParameterException("Tried to load invalid renderer");
		}
		
		$pluginIns=$pluginRC->newInstance();
		$pluginIns->cli($argv);
		$graph=$pluginIns->getGraph();
		$r=$renderRC->newInstance();
		$r->render($graph);
	}
}

