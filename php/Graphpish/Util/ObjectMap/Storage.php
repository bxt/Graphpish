<?php
namespace Graphpish\Util\ObjectMap;
use Graphpish\Util\ArrayDecorator;

class Storage {
	/**
	 * How many keys will be used
	 * 
	 * $keycnt-1 is the max key level
	 * Key level is the value of the annotations
	 * @var int
	 */
	private $keycnt;
	
	/**
	 * The actual database
	 */
	private $data=array();
	
	/**
	 * Caching parsed annotation data for classes here
	 */
	private $annotCache=array();

	/**
	 * Object that will return annotations for reflection methods
	 */
	private $annotReader;
	
	/**
	 * Qualified name srting of the class used by getOrMake() by default
	 */
	private $defaultClass;
	
	/**
	 * Qualified name srting of the annotation that marks a method which returns a key
	 */
	const KEY_ANNOT='Graphpish\\Util\\ObjectMap\\KeyA';
	
	/**
	 * Qualified name srting of the annotation that marks the constructor
	 * 
	 * You can mark the actual constructor or a public static method that
	 * should return an instance of the class. 
	 */
	const CONSTR_ANNOT='Graphpish\\Util\\ObjectMap\\KeyConstructorA';
	
	/**
	 * Initialize
	 * 
	 * We do use a fixed key count across the whole instance, this
	 * has to be set. 
	 */
	public function __construct($keycnt,$defaultClass=false,$annotReader=false) {
		if($keycnt<1) throw new \InvalidArgumentException("Invalid (<1) Keydepth: $keycnt");
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
	/**
	 * Save an object into our keyed database
	 */
	public function store($obj) {
		$keys=$this->getKeys($obj,$this->keycnt);
		$data=new ArrayDecorator($this->data);
		$data->store_deep($keys,$obj);
		return $this;
	}
	/**
	 * Get all ever saved objects in a flat array
	 */
	public function dump() {
		$data=new ArrayDecorator($this->data);
		return $data->flatten();
	}
	/**
	 * Retrieve an object by its keys
	 * 
	 * Use keys as parameters. 
	 */
	public function get() {
		$args=func_get_args();
		if(count($args)>$this->keycnt) {
			throw new \BadMethodCallException("requested key depth bigger than stored key depth");
		}
		for($i=0,$len=count($args);$i<$len;$i++) {
			$args[$i]=$this->getSuitableKey($args[$i]);
		}
		$data=new ArrayDecorator($this->data);
		return $data->get_deep($args);
	}
	/**
	 * Retrieve an object by its keys, or construct
	 * 
	 * Use a class name to construct and after that keys as parameters. 
	 */
	public function getOrMake($class=false) {
		$args=func_get_args();
		array_shift($args);
		$possible=call_user_func_array(array($this,'get'),$args);
		if($possible) return $possible;
		
		$new=call_user_func_array(array($this,'make'),func_get_args());
		$this->store($new);
		return $new;
	}
	/**
	 * How many keys this storage uses
	 */
	public function getKeyCount() {
		return $this->keycnt;
	}
	/**
	 * The instance building part of getOrMake()
	 */
	private function make($class=false) {
		$args=func_get_args();
		array_shift($args);
		if(!$class) {
			if($this->defaultClass) {
				$class=$this->defaultClass;
			} else {
				throw new \BadMethodCallException("Need a class to build! Set defaultClass. ");
			}
		}
		$builder=$this->getBuilder($class,$this->keycnt-1);
		if($builder->isConstructor()) {
			$newClass=new \ReflectionClass($class);
			$new=$newClass->newInstanceArgs($args);
		} else {
			$new=$builder->invokeArgs(null,$args);
		}
		return $new;
	}
	/**
	 * Gather all the (relevant) attached information about a class
	 * 
	 * This reads the annotations, checks which methods to
	 * use as key generators and looks for constructors. 
	 */
	private function getAnnotinfo($class) {
		if(!isset($this->annotCache[$class])) {
			$info=array();
			$rC=new \ReflectionClass($class);
			$candidates=$rC->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_STATIC);
			foreach($candidates as $candidate) {
				$candidateAnnots=$this->annotReader->getMethodAnnotations($candidate);
				if(isset($candidateAnnots[static::KEY_ANNOT])) {
					$level=$candidateAnnots[static::KEY_ANNOT]->value;
					$info["key"][$level]=$candidate;
				}
				if(isset($candidateAnnots[static::CONSTR_ANNOT])) {
					$level=$candidateAnnots[static::CONSTR_ANNOT]->value;
					if(
							$candidate->isPublic() &&
							($candidate->isStatic()||$candidate->isConstructor())
							) {
						$info["builder"][$level]=$candidate;
					}
				}
			}
			$this->annotCache[$class]=$info;
		}
		return $this->annotCache[$class];
	}
	/**
	 * Get the builder function
	 */
	private function getBuilder($class,$level) {
		$info=$this->getAnnotinfo($class);
		if(!isset($info["builder"][$level])) {
			throw new MissingAnnotationsException ("Class {$class} has no constructor for level {$level}");
		}
		return $info["builder"][$level];
	}
	/**
	 * Get the key generator method
	 */
	private function getKeymethod($class,$level) {
		$info=$this->getAnnotinfo($class);
		if(!isset($info["key"][$level])) {
			throw new MissingAnnotationsException ("Class {$class} has no key for level {$level}");
		}
		return $info["key"][$level];
	}
	
	/**
	 * Get the list of keys of an object
	 */
	private function getKeys($obj,$depth) {
		$keys=array();
		for($i=0;$i<$depth;$i++) {
			$keys[]=$this->getSuitableKey($this->getKeymethod(get_class($obj),$i)->invokeArgs($obj,array()));
		}
		return $keys;
	}
	/**
	 * Return keys that can be used by our php array store
	 * 
	 * We can use string and int directly, and convert anything else (e.g. float)
	 * to strings. When using object we try to use their key.
	 */
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