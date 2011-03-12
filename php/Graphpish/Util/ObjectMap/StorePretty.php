<?php
namespace Graphpish\Util\ObjectMap;

/**
 * Provides __invoke() magic for Storage
 */
class StorePretty extends Storage {
  /**
   * Easy access to dump() and getOrMake()
   * 
   * This function uses its parameter count to determine what
   * to do: 
   *  - 0   arguments calls dump()
   *  - k   arguments calls getOrMake(), use default Class
   *  - k+1 arguments maps directly to getOrMake()
   * k is the Storage's keycnt
   */
	public function __invoke() {
		$args=func_get_args();
		$argc=func_num_args();
		$kc=$this->getKeyCount();
		switch(true) {
			case ($argc==0): return $this->dump();
			case ($argc==$kc): array_unshift($args,false);
			case ($argc==$kc+1): return call_user_func_array(array($this,'getOrMake'),$args);
		}
		throw new \BadMethodCallException("Requested key depth does not match stored key depth");
	}
}