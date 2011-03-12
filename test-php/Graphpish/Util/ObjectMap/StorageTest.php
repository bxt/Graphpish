<?php
namespace Graphpish\Util\ObjectMap;

require_once 'graphpish.php';

require_once 'test-php/Graphpish/Util/ObjectMap/testclasses.php';

class StorageTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @dataProvider keys
	 */
	public function testSimpleGet($key1,$key2) {
		$om=new Storage(1);
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
		$om=new Storage(2);
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
		$om=new Storage(2);
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
		$om=new Storage(1);
		$a2=$om->getOrMake(__NAMESPACE__."\\B",$key1);
		$this->assertEquals($key1,$a2->getFoo());
	}
	/**
	 * @dataProvider keys
	 */
	public function testConstructConstructor($key1,$key2) {
		$om=new Storage(1);
		$a2=$om->getOrMake(__NAMESPACE__."\\A",$key1);
		$this->assertEquals($key1,$a2->getFoo());
		$this->assertEquals('default-bar',$a2->getBar());
	}
	/**
	 * @dataProvider keys
	 */
	public function testConstructConstructorDefaultClass($key1,$key2) {
		$om=new Storage(1,__NAMESPACE__."\\A");
		$a2=$om->getOrMake(false,$key1);
		$this->assertEquals($key1,$a2->getFoo());
		$this->assertEquals('default-bar',$a2->getBar());
	}
	/**
	 * @expectedException BadMethodCallException
	 */
	public function testConstructConstructorDefaultClassWithoutActuallyHavingOne() {
		$om=new Storage(1);
		$a2=$om->getOrMake(false,100);
	}
	/**
	 * @expectedException \Graphpish\Util\ObjectMap\MissingAnnotationsException
	 */
	public function testStoringAnnotationlessObject() {
		$c=new C;
		$om=new Storage(1);
		$om->store($c);
	}
	/**
	 * @expectedException \Graphpish\Util\ObjectMap\MissingAnnotationsException
	 */
	public function testBuildingAnnotationlessObject() {
		$om=new Storage(1);
		$om->getOrMake('Graphpish\\Util\\ObjectMap\\C',0);
	}
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructWithSillyParam() {
		$om=new Storage(0);
	}
	/**
	 * @dataProvider keys
	 */
	public function testConstructConstructorDual($key1,$key2) {
		$om=new Storage(2);
		$a2=$om->getOrMake(__NAMESPACE__."\\A",$key1,$key2);
		$this->assertEquals($key1,$a2->getFoo());
		$this->assertEquals($key2,$a2->getBar());
	}
	/**
	 * @dataProvider keys
	 */
	public function testMixedClasses($key1,$key2) {
		$om=new Storage(1);
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
		$om=new Storage(1);
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
		$om=new Storage(1);
		$a=new A($key1,2);
		$b=new B();
		$b->foo=$key2;
		$om->store($a);
		$om->store($b);
		$this->assertEquals(array($a,$b),$om->dump());
	}
	/**
	 * @expectedException BadMethodCallException
	 */
	public function testGetException() {
		$om=new Storage(1);
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
