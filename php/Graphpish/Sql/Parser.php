<?php
namespace Graphpish\Sql;

/**
 * Parse multi-dimensional ini-Data from cascading files
 * 
 * @see \Graphpish\Sql\Parser::process()
 */
class Parser {
	/**
	 * Store current options
	 */
	private $options=array();
	/**
	 * Parse ini-Data array into multi-level array
	 * 
	 * This method is made to make (well nearly) infinite
	 * levels of configuration option stacking in .ini files
	 * possible, by not only using sections "[foo]" but a set of
	 * delimiters too: 
	 * 
	 * [foo:bar-baz/bam]
	 * 
	 * This section would create 4 dimensions. Notice that while
	 * you can repeat the level delimiters, you can't change their
	 * order currently. Lower priority delimiters will then be
	 * ignored. Empty path pieces will be ignored too. 
	 * 
	 * Subsequent calls to this method on the same instance will
	 * override older values. 
	 *
	 * @param array Like the ones returned by parse_ini_file()/string()
	 * @return \Graphpish\Sql\Parser this (chainable)
	 */
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
	/**
	 * Access the parsed data
	 *
	 * @return array (assoc)
	 */
	function getOpts() {
		return $this->options;
	}
}
