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
			 if (preg_match("/^(.*?):(.*?)-(.*?)((\/.*?)*)$/",$rawSection,$m)>0) {
				if(count($m)==5) {
					$this->options[$m[1]][$m[2]][$m[3]]=$sectionData;
				} else {
					$m2=explode('/',$m[4]);
					$unit=&$this->options[$m[1]][$m[2]][$m[3]];
					for($i=1;$i<count($m2);$i++) {
						$unit=&$unit[$m2[$i]];
					}
					$unit=$sectionData;
				}
			} elseif (preg_match("/^node:(.*?)((\/.*?)*)$/",$rawSection,$m)>0) {
				if(count($m)==3) {
					$this->options["node"][$m[1]]=$sectionData;
				} else {
					$m2=explode('/',$m[2]);
					$unit=&$this->options["node"][$m[1]];
					for($i=1;$i<count($m2);$i++) {
						$unit=&$unit[$m2[$i]];
					}
					$unit=$sectionData;
				}
			} else { // "connection" is expected here
				$this->options[$rawSection]=$sectionData;
			}
		}
		return $this;
	}
	function getOpts() {
		return $this->options;
	}
}
