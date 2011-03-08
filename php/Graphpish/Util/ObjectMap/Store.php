<?php
namespace Graphpish\Util\ObjectMap;
use Graphpish\Util\ArrayDecorator;

class Store {
	/**
	 * @var int
	 */
	private $keydepth;
	private $data=array();
	private $annotCache=array();
	private $annotReader;
	private $defaultClass;
	const KEY_ANNOT='Graphpish\\Util\\ObjectMap\\KeyA';
	const CONSTR_ANNOT='Graphpish\\Util\\ObjectMap\\KeyConstructorA';
	function __construct($keydepth,$defaultClass=false,$annotReader=false) {
		$this->keydepth=$keydepth;
		$this->annotReader=$annotReader;
		if(!$this->annotReader) {
			$this->annotReader=new \Doctrine\Common\Annotations\AnnotationReader();
		}
		$this->defaultClass=$defaultClass;
		/* 
		 * pre-autoload set of annotations, this prevents docblock parser
		 * from loading classes for every @bla documentation
		 */
		class_exists('\\'.static::KEY_ANNOT,true);
		class_exists('\\'.static::CONSTR_ANNOT,true);
	}
	function store($obj) {
		$info=$this->getAnnotinfo(get_class($obj));
		$keys=array();
		for($i=0;$i<$this->keydepth;$i++) {
			$keys[]=$info["key"][$i]->invokeArgs($obj,array());
		}
		$data=new ArrayDecorator($this->data);
		$data->store_deep($keys,$obj);
		return $this;
	}
	function get() {
		$args=func_get_args();
		if(count($args)>$this->keydepth) {
			throw new \Exception("requested key depth bigger than stored key depth");
		}
		$data=new ArrayDecorator($this->data);
		return $data->get_deep($args);
	}
	function getOrMake($class=false) {
		$args=func_get_args();
		array_shift($args);
		$possible=call_user_func_array(array($this,'get'),$args);
		if($possible) return $possible;
		
		$new=call_user_func_array(array($this,'make'),func_get_args());
		$this->store($new);
		return $new;
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
		$info=$this->getAnnotinfo($class);
		if($info["builder"]->isConstructor()) {
			$newClass=new \ReflectionClass($class);
			$new=$newClass->newInstanceArgs($args);
		} else {
			$new=$info["builder"]->invokeArgs(null,$args);
		}
		return $new;
	}
	private function getAnnotinfo($class) {
		if(!isset($this->annotCache[$class])) {
			$info=array();
			$rC=new \ReflectionClass($class);
			$candidates=$rC->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_STATIC);
			foreach($candidates as $candidate) {
				$candidateAnnots=$this->annotReader->getMethodAnnotations($candidate);
				if(isset($candidateAnnots[static::KEY_ANNOT])) {
					$lvl=$candidateAnnots[static::KEY_ANNOT]->value;
					if($lvl<$this->keydepth) {
						$info["key"][$lvl]=$candidate;
					}
				}
				if(isset($candidateAnnots[static::CONSTR_ANNOT])) {
					$lvl=$candidateAnnots[static::CONSTR_ANNOT]->value;
					if(
							$lvl==($this->keydepth-1) &&
							$candidate->isPublic() &&
							($candidate->isStatic()||$candidate->isConstructor())
							) {
						$info["builder"]=$candidate;
					}
				}
			}
			$good=true;
			$good=$good&&isset($info["builder"]);
			for($i=0;$i<$this->keydepth;$i++) {
				$good=$good&&isset($info["key"][$i]);
			}
			if(!$good) {
				var_dump($info);
				throw new \Exception("Class $class is not suitable for mapping keydepth {$this->keydepth}. Mising annotations?");
			}
			$this->annotCache[$class]=$info;
		}
		return $this->annotCache[$class];
	}
}