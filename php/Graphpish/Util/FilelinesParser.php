<?php
namespace Graphpish\Util;

/**
 * Providing basic functionality for line-by-line parsing
 */
abstract class FilelinesParser {
	/**
	 * Iterate the file and call parseLine($l) on each line
	 */
	public function parse($file) {
		$handle = @fopen($file, "r");
		if ($handle) {
			while (($buffer=fgets($handle, 4096))!==false) {
				$this->parseLine($buffer);
			}
			if (!feof($handle)) {
				throw new \RuntimeException("Unexpected fgets() fail");
			}
			fclose($handle);
		} else {
			throw new \FileNotAccessibleException("Can't open file for reading: {$file}");
		}
		return $this;
	}
	/**
	 * Called for each line, override to add you own logic here
	 */
	abstract public function parseLine($l);
}