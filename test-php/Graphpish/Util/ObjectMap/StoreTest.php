<?php
namespace Graphpish\Util\ObjectMap;

require_once 'graphpish.php';

class StoreTest extends \PHPUnit_Framework_TestCase {
	public function testSimpleGet() {
		$om=new Store(1);
		$a=new A(3,2);
		$om->store($a);
		$a2=$om->get(3);
		$this->assertEquals($a,$a2);
		$this->assertTrue($a==$a2);
	}
	public function testDualGet() {
		$om=new Store(2);
		$a=new A(3,8);
		$om->store($a);
		$a2=$om->get(3,8);
		$this->assertEquals($a,$a2);
		$this->assertTrue($a==$a2);
	}
	public function testConstructStatic() {
		$om=new Store(1);
		$a2=$om->getOrMake(__NAMESPACE__."\\B",3);
		$this->assertEquals(3,$a2->getFoo());
	}
	public function testConstructConstructor() {
		$om=new Store(1);
		$a2=$om->getOrMake(__NAMESPACE__."\\A",3);
		$this->assertEquals(3,$a2->getFoo());
		$this->assertEquals('default-bar',$a2->getBar());
	}
	public function testConstructConstructorDual() {
		$om=new Store(2);
		$a2=$om->getOrMake(__NAMESPACE__."\\A",3,8);
		$this->assertEquals(3,$a2->getFoo());
		$this->assertEquals(8,$a2->getBar());
	}
}

class A {
	/**
	 * @Graphpish\Util\ObjectMap\KeyConstructorA(1)
	 */
	public function __construct($key1,$key2) {
		$this->foo=$key1;
		$this->bar=$key2;
	}
	/**
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