<?php
namespace Graphpish\Util\ObjectMap;

require_once 'graphpish.php';

class StorePrettyTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @dataProvider keys
	 */
	public function testConstructStatic($key1,$key2) {
		$om=new StorePretty(1);
		$a2=$om(__NAMESPACE__."\\B",$key1);
		$this->assertEquals($key1,$a2->getFoo());
	}
	/**
	 * @dataProvider keys
	 */
	public function testConstructConstructor($key1,$key2) {
		$om=new StorePretty(1);
		$a2=$om(__NAMESPACE__."\\A",$key1);
		$this->assertEquals($key1,$a2->getFoo());
		$this->assertEquals('default-bar',$a2->getBar());
	}
	/**
	 * @dataProvider keys
	 */
	public function testConstructConstructorDefaultClass($key1,$key2) {
		$om=new StorePretty(1,__NAMESPACE__."\\A");
		$a2=$om($key1);
		$this->assertEquals($key1,$a2->getFoo());
		$this->assertEquals('default-bar',$a2->getBar());
	}
	/*
	 * Note that this is a huge WTF-caveat! Method<>Property-Duality
	 * -> PHP Fatal error:  Call to undefined method Graphpish\Util\ObjectMap\StorePrettyTest::om()
	 * Which can't be catched (not even by phpunit...) so your app is badly screwed.
	 * 
	public function testConstructConstructorDefaultClassOnProperty($key1,$key2) {
		$this->om=new StorePretty(1,__NAMESPACE__."\\A");
		$a2=$this->om($key1);
		$this->assertEquals($key1,$a2->getFoo());
		$this->assertEquals('default-bar',$a2->getBar());
	}
	 *
	 */
	/**
	 * @expectedException Exception
	 */
	public function testConstructConstructorDefaultClassWithoutActuallyHavingOne() {
		$om=new StorePretty(1);
		$a2=$om(100);
	}
	/**
	 * @dataProvider keys
	 */
	public function testConstructConstructorDual($key1,$key2) {
		$om=new StorePretty(2);
		$a2=$om(__NAMESPACE__."\\A",$key1,$key2);
		$this->assertEquals($key1,$a2->getFoo());
		$this->assertEquals($key2,$a2->getBar());
	}
	public function keys() {
		return array(
			array(3,5),
			array(7,8),
			array(9,8),
			array(452325,23452345),
			array("stringkey1","stringkey2"),
			array("stringkey",4352),
			array(4.6,4.9),
			array(new A(7,4),new A(8,4)),
		);
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