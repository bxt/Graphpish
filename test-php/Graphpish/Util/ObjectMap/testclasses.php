<?php
namespace Graphpish\Util\ObjectMap;

class A {
	/**
	 * @Graphpish\Util\ObjectMap\KeyConstructorA(1)
	 */
	public function __construct($key1,$key2) {
		$this->foo=$key1;
		$this->bar=$key2;
	}
	/**
	 * Here could go method description
	 * 
	 * @return mixed The Fooh
	 * 
	 * @Graphpish\Util\ObjectMap\KeyA(0)
	 */
	public function getFoo() {
		return $this->foo;
	}
	/**
	 * @Graphpish\Util\ObjectMap\KeyA(1)
	 */
	public function getBar() {
		return $this->bar;
	}
	/**
	 * @Graphpish\Util\ObjectMap\KeyConstructorA(0)
	 */
	public static function newByFoo($key1) {
		$a=new self($key1,'default-bar');
		return $a;
	}
}
class B {
	/**
	 * @Graphpish\Util\ObjectMap\KeyA(0)
	 */
	public function getFoo() {
		return $this->foo;
	}
	/**
	 * @Graphpish\Util\ObjectMap\KeyConstructorA(0)
	 */
	public static function newByKey($key) {
		$b=new self();
		$b->foo=$key;
		return $b;
	}
}
class C {}