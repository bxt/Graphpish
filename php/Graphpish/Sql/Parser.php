<?php
namespace Graphpish\Sql;

class Parser {
	private $options=array();
	function __construct() {
		$this->options["node"]=array();
		$this->options["edge"]=array();
	}
	function process($rawData) {
		foreach($rawData as $rawSection=>$sectionData) {
			preg_match("/^(.+?)((:(.+?))*)((-(.+?))*)((\/.+?)*)$/",$rawSection,$m);
			$m2=array_merge(array($m[1]),explode(':',$m[2]),explode('-',$m[5]),explode('/',$m[8]));
			$unit=&$this->options;
			for($i=0;$i<count($m2);$i++) {
				if($m2[$i]!='') {
					$unit=&$unit[$m2[$i]];
				}
			}
			$unit=$sectionData;
		}
		return $this;
	}
	function getOpts() {
		return $this->options;
	}
}
