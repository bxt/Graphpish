<?php
namespace Graphpish\Util\ObjectMap;

class StorePretty extends Store {
	public function __invoke() {
		$args=func_get_args();
		$argc=func_num_args();
		$kc=$this->getKeyCount();
		switch(true) {
			case ($argc==0): return $this->dump();
			case ($argc==$kc): array_unshift($args,false);
			case ($argc==$kc+1): return call_user_func_array(array($this,'getOrMake'),$args);
		}
		throw new \Exception("Requested key depth does not match stored key depth");
	}
}