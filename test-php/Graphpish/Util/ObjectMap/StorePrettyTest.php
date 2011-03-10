<?php
namespace Graphpish\Util\ObjectMap;

require_once 'graphpish.php';

require_once 'test-php/Graphpish/Util/ObjectMap/testclasses.php';

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
	 * @expectedException BadMethodCallException
	 */
	public function testTooFewArgs() {
		$om=new StorePretty(2);
		$a2=$om(__NAMESPACE__."\\B",'needed another key ;( ');
		$this->assertEquals($key1,$a2->getFoo());
	}
	/**
	 * @expectedException BadMethodCallException
	 */
	public function testTooManyArgs() {
		$om=new StorePretty(1);
		$a2=$om(__NAMESPACE__."\\B",'this is needed',' another key ;( ');
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
