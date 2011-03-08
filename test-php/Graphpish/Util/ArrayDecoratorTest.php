<?php
namespace Graphpish\Util;

require_once 'graphpish.php';

class ArrayDecoratorTest extends \PHPUnit_Framework_TestCase {
	/**
	 * @dataProvider numbers
	 */
	public function testGet($n1,$n2,$n3) {
		$our_array=array("b"=>$n1);
		$decorated_array=new ArrayDecorator($our_array);
		$our_array_again=&$decorated_array->get();
		$our_array_again["x"]=$n2;
		$this->assertEquals(array("b"=>$n1,"x"=>$n2),$our_array);
	}
	/**
	 * @dataProvider numbers
	 */
	public function testDeepInsert($n1,$n2,$n3) {
		$our_array=array("b"=>$n1);
		$decorated_array=new ArrayDecorator($our_array);
		$decorated_array->store_deep(array("a","k"),$n2);
		$this->assertEquals(array("b"=>$n1,"a"=>array("k"=>$n2)),$our_array);
		$this->assertEquals($our_array,$decorated_array->get());
	}
	/**
	 * @dataProvider numbers
	 */
	public function testReallyDeepInsert($n1,$n2,$n3) {
		$our_array=array("b"=>$n1);
		$decorated_array=new ArrayDecorator($our_array);
		$decorated_array->store_deep(array("a","k","l","m","n","o","p"),$n2);
		$this->assertEquals(array("b"=>$n1,"a"=>array("k"=>array("l"=>array("m"=>array("n"=>array("o"=>array("p"=>$n2))))))),$our_array);
		$this->assertEquals($our_array,$decorated_array->get());
	}
	/**
	 * @dataProvider numbers
	 */
	public function testReallyDeepInsertTwice($n1,$n2,$n3) {
		$our_array=array("b"=>$n1);
		$decorated_array=new ArrayDecorator($our_array);
		$decorated_array->store_deep(array("a","k","l","m","n","o","p"),$n2);
		$decorated_array->store_deep(array("a","k","l","m","n","o","p"),$n3);
		$this->assertEquals(array("b"=>$n1,"a"=>array("k"=>array("l"=>array("m"=>array("n"=>array("o"=>array("p"=>$n3))))))),$our_array);
		$this->assertEquals($our_array,$decorated_array->get());
	}
	/**
	 * @dataProvider numbers
	 * @depends testReallyDeepInsert
	 */
	public function testGetDeep($n1,$n2,$n3) {
		$our_array=array("b"=>$n1);
		$decorated_array=new ArrayDecorator($our_array);
		$decorated_array->store_deep(array("a","k","l","m","n","o","p"),$n2);
		$back=$decorated_array->get_deep(array("a","k","l","m","n","o","p"));
		$this->assertEquals($n2,$back);
	}
	/**
	 * @dataProvider numbers
	 * @depends testReallyDeepInsert
	 */
	public function testFlatten($n1,$n2,$n3) {
		$our_array=array("b"=>$n1);
		$decorated_array=new ArrayDecorator($our_array);
		$decorated_array->store_deep(array("a","k","l","m","n","o","p"),$n2);
		$decorated_array->store_deep(array("a","k","l","m","n","o","q"),$n3);
		$back=$decorated_array->flatten();
		$this->assertEquals(array($n1,$n2,$n3),$back);
	}
	
	public function numbers() {
		return array(
			array(1,1,1),
			array(1,2,1),
			array(2,1,1),
			array(1,1,2),
			array(1,2,3),
			array(4,6,3),
			array(234,342523,9323),
		);
	}
}