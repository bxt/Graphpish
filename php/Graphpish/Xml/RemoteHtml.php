<?php
namespace Graphpish\Xml;
use Graphpish\Util\ObjectMap\StorePretty;
use Graphpish\Util\HttpClient;

class RemoteHtml implements \Graphpish\Cli\PluginI {
	
	public function __construct($reader=null) {
		$this->reader=$reader ?: new Reader();
		$this->reader->setOptions(array(
			"catchAttributes"=>true,
			"aggregateAttributes"=>true,
			"catchAttributeValues"=>false,
			"aggregateAttributeValues"=>true,
			"catchTextnodes"=>false,
			"aggregateTextnodes"=>true,
		));
	}
	
	public function fetch($url) {
		$html=HttpClient::fetch($url);
		// DOMDocument doeasn't understand these correctly and adds tags
		// around whole document etc. so well just ignore
		$html=preg_replace('/^\s*<\?xml version="(.*?)" \?>/','',$html);
		$html=preg_replace('/^\s*<!DOCTYPE html(.*?)>/','',$html);
		$error_reporting=error_reporting(0); //markup is never ever valid, is it?
		$domdoc=\DOMDocument::loadHTML($html);
		error_reporting($error_reporting);
		$simplexmldoc=simplexml_import_dom($domdoc);
		$this->reader->read($simplexmldoc);
		return $this;
	}
	
	public function getGraph() {
		return $this->reader->getGraph();
	}
	
	public function cli(array $argv) {
		if(count($argv)!=1) {
			throw new \Graphpish\Cli\ParameterException("Remote Html Plugin accepts exactly 1 argument");
		}
		return $this->fetch($argv[0]);
	}
}
