<?php
namespace Graphpish\Util;

abstract class FilelinesParser {
	public function parse($file) {
		$handle = @fopen($file, "r");
		if ($handle) {
			while (($buffer=fgets($handle, 4096))!==false) {
				$this->parseLine($buffer);
			}
			if (!feof($handle)) {
				throw new \Exception("Unexpected fgets() fail");
			}
			fclose($handle);
		} else {
			throw new \Exception("Can't open file for reading: {$file}");
		}
		return $this;
	}
	abstract public function parseLine($l);
}