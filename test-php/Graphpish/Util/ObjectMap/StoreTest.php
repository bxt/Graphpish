<?php
namespace Graphpish\Util\ObjectMap;

require_once 'graphpish.php';

class StoreTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @dataProvider keys
	 */
	public function testSimpleGet($key1,$key2) {
		$om=new Store(1);
		$a=new A($key1,$key2);
		$om->store($a);
		$a2=$om->get($key1);
		$this->assertEquals($a,$a2);
		$this->assertTrue($a==$a2);
	}
	/**
	 * @dataProvider keys
	 */
	public function testDualGet($key1,$key2) {
		$om=new Store(2);
		$a=new A($key1,$key2);
		$om->store($a);
		$a2=$om->get($key1,$key2);
		$this->assertEquals($a,$a2);
		$this->assertTrue($a==$a2);
	}
	/**
	 * @dataProvider keys
	 */
	public function testDualGetLooped($key1,$key2) {
		$om=new Store(2);
		$a=new A($key1,$key2);
		$om->store($a);
		for($i=0;$i<100;$i++) {
			$a2=$om->get($key1,$key2);
			$this->assertEquals($a,$a2);
			$this->assertTrue($a==$a2);
		}
	}
	/**
	 * @dataProvider keys
	 */
	public function testConstructStatic($key1,$key2) {
		$om=new Store(1);
		$a2=$om->getOrMake(__NAMESPACE__."\\B",$key1);
		$this->assertEquals($key1,$a2->getFoo());
	}
	/**
	 * @dataProvider keys
	 */
	public function testConstructConstructor($key1,$key2) {
		$om=new Store(1);
		$a2=$om->getOrMake(__NAMESPACE__."\\A",$key1);
		$this->assertEquals($key1,$a2->getFoo());
		$this->assertEquals('default-bar',$a2->getBar());
	}
	/**
	 * @dataProvider keys
	 */
	public function testConstructConstructorDefaultClass($key1,$key2) {
		$om=new Store(1,__NAMESPACE__."\\A");
		$a2=$om->getOrMake(false,$key1);
		$this->assertEquals($key1,$a2->getFoo());
		$this->assertEquals('default-bar',$a2->getBar());
	}
	/**
	 * @expectedException Exception
	 */
	public function testConstructConstructorDefaultClassWithoutActuallyHavingOne() {
		$om=new Store(1);
		$a2=$om->getOrMake(false,100);
	}
	/**
	 * @expectedException Exception
	 */
	public function testStoringAnnotationlessObject() {
		$c=new C;
		$om=new Store(1);
		$om->store($c);
	}
	/**
	 * @expectedException Exception
	 */
	public function testConstructWithSillyParam() {
		$om=new Store(0);
	}
	/**
	 * @dataProvider keys
	 */
	public function testConstructConstructorDual($key1,$key2) {
		$om=new Store(2);
		$a2=$om->getOrMake(__NAMESPACE__."\\A",$key1,$key2);
		$this->assertEquals($key1,$a2->getFoo());
		$this->assertEquals($key2,$a2->getBar());
	}
	/**
	 * @dataProvider keys
	 */
	public function testMixedClasses($key1,$key2) {
		$om=new Store(1);
		$a=new A($key1,2);
		$b=new B();
		$b->foo=$key2;
		$om->store($a);
		$om->store($b);
		$a2=$om->get($key1);
		$b2=$om->get($key2);
		$this->assertEquals($a,$a2);
		$this->assertTrue($a==$a2);
		$this->assertEquals($b,$b2);
		$this->assertTrue($b==$b2);
	}
	/**
	 * @dataProvider keys
	 */
	public function testOverride($key1,$key2) {
		$om=new Store(1);
		$b=new B();
		$b->foo=$key1;
		$b->other="bad";
		$bo=new B();
		$bo->foo=$key1;
		$bo->other="other";
		$om->store($b);
		$om->store($bo);
		$b2=$om->get($key1);
		$this->assertEquals("other",$b2->other);
		$this->assertEquals($bo,$b2);
		$this->assertTrue($bo==$b2);
	}
	/**
	 * @dataProvider keys
	 * @depends testMixedClasses
	 */
	public function testDump($key1,$key2) {
		$om=new Store(1);
		$a=new A($key1,2);
		$b=new B();
		$b->foo=$key2;
		$om->store($a);
		$om->store($b);
		$this->assertEquals(array($a,$b),$om->dump());
	}
	/**
	 * @expectedException Exception
	 */
	public function testGetException() {
		$om=new Store(1);
		$a=new A(1,2);
		$om->store($a);
		$a2=$om->get(1,2);
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