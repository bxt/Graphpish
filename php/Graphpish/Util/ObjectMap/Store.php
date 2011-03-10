<?php
namespace Graphpish\Util\ObjectMap;
use Graphpish\Util\ArrayDecorator;

class Store {
	/**
	 * @var int
	 */
	private $keycnt;
	private $data=array();
	private $annotCache=array();
	private $annotReader;
	private $defaultClass;
	const KEY_ANNOT='Graphpish\\Util\\ObjectMap\\KeyA';
	const CONSTR_ANNOT='Graphpish\\Util\\ObjectMap\\KeyConstructorA';
	public function __construct($keycnt,$defaultClass=false,$annotReader=false) {
		if($keycnt<1) throw new \Exception("Invalid Keydepth: $keycnt");
		$this->keycnt=$keycnt;
		$this->annotReader=$annotReader ?: new \Doctrine\Common\Annotations\AnnotationReader;
		$this->defaultClass=$defaultClass;
		/* 
		 * pre-autoload set of annotations, this prevents docblock parser
		 * from loading classes for every @bla documentation
		 */
		class_exists(static::KEY_ANNOT,true);
		class_exists(static::CONSTR_ANNOT,true);
	}
	public function store($obj) {
		$keys=$this->getKeys($obj,$this->keycnt);
		$data=new ArrayDecorator($this->data);
		$data->store_deep($keys,$obj);
		return $this;
	}
	public function dump() {
		$data=new ArrayDecorator($this->data);
		return $data->flatten();
	}
	public function get() {
		$args=func_get_args();
		if(count($args)>$this->keycnt) {
			throw new \Exception("requested key depth bigger than stored key depth");
		}
		for($i=0,$len=count($args);$i<$len;$i++) {
			$args[$i]=$this->getSuitableKey($args[$i]);
		}
		$data=new ArrayDecorator($this->data);
		return $data->get_deep($args);
	}
	public function getOrMake($class=false) {
		$args=func_get_args();
		array_shift($args);
		$possible=call_user_func_array(array($this,'get'),$args);
		if($possible) return $possible;
		
		$new=call_user_func_array(array($this,'make'),func_get_args());
		$this->store($new);
		return $new;
	}
	public function getKeyCount() {
		return $this->keycnt;
	}
	private function make($class=false) {
		$args=func_get_args();
		array_shift($args);
		if(!$class) {
			if($this->defaultClass) {
				$class=$this->defaultClass;
			} else {
				throw new \Exception("Need a class to build! Set defaultClass. ");
			}
		}
		$info=$this->getAnnotinfo($class,$this->keycnt);
		if($info["builder"]->isConstructor()) {
			$newClass=new \ReflectionClass($class);
			$new=$newClass->newInstanceArgs($args);
		} else {
			$new=$info["builder"]->invokeArgs(null,$args);
		}
		return $new;
	}
	private function getAnnotinfo($class,$depth) {
		if(!isset($this->annotCache[$depth][$class])) {
			$info=array();
			$rC=new \ReflectionClass($class);
			$candidates=$rC->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_STATIC);
			foreach($candidates as $candidate) {
				$candidateAnnots=$this->annotReader->getMethodAnnotations($candidate);
				if(isset($candidateAnnots[static::KEY_ANNOT])) {
					$lvl=$candidateAnnots[static::KEY_ANNOT]->value;
					if($lvl<$depth) {
						$info["key"][$lvl]=$candidate;
					}
				}
				if(isset($candidateAnnots[static::CONSTR_ANNOT])) {
					$lvl=$candidateAnnots[static::CONSTR_ANNOT]->value;
					if(
							$lvl==($depth-1) &&
							$candidate->isPublic() &&
							($candidate->isStatic()||$candidate->isConstructor())
							) {
						$info["builder"]=$candidate;
					}
				}
			}
			$good=true;
			$good=$good&&isset($info["builder"]);
			for($i=0;$i<$depth;$i++) {
				$good=$good&&isset($info["key"][$i]);
			}
			if(!$good) {
				throw new \Exception("Class $class is not suitable for mapping keycnt {$depth}. Mising annotations?");
			}
			$this->annotCache[$depth][$class]=$info;
		}
		return $this->annotCache[$depth][$class];
	}
	private function getKeys($obj,$depth) {
		$info=$this->getAnnotinfo(get_class($obj),$depth);
		$keys=array();
		for($i=0;$i<$depth;$i++) {
			$keys[]=$this->getSuitableKey($info["key"][$i]->invokeArgs($obj,array()));
		}
		return $keys;
	}
	private function getSuitableKey($key) {
		if(is_int($key)) return $key;
		if(is_string($key)) return $key;
		if(is_object($key)) { 
			$keys=$this->getKeys($key,1);
			// Recursive!!!
			return $this->getSuitableKey($keys[0]);
		}
		return (string)$key;
		// this is not too good, but is it better than failing?
	}
}