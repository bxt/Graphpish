<?php
namespace Graphpish\Util;

require_once 'graphpish.php';

class ArrayDecoratorTest extends \PHPUnit_Framework_TestCase {
	public function testGet() {
		$our_array=array("b"=>4);
		$decorated_array=new ArrayDecorator($our_array);
		$our_array_again=&$decorated_array->get();
		$our_array_again["x"]=6;
		$this->assertEquals(array("b"=>4,"x"=>6),$our_array);
	}
	public function testDeepInsert() {
		$our_array=array("b"=>4);
		$decorated_array=new ArrayDecorator($our_array);
		$decorated_array->store_deep(array("a","k"),3);
		$this->assertEquals(array("b"=>4,"a"=>array("k"=>3)),$our_array);
		$this->assertEquals($our_array,$decorated_array->get());
	}
	public function testReallyDeepInsert() {
		$our_array=array("b"=>4);
		$decorated_array=new ArrayDecorator($our_array);
		$decorated_array->store_deep(array("a","k","l","m","n","o","p"),3);
		$this->assertEquals(array("b"=>4,"a"=>array("k"=>array("l"=>array("m"=>array("n"=>array("o"=>array("p"=>3))))))),$our_array);
		$this->assertEquals($our_array,$decorated_array->get());
	}
	public function testReallyDeepInsertTwice() {
		$our_array=array("b"=>4);
		$decorated_array=new ArrayDecorator($our_array);
		$decorated_array->store_deep(array("a","k","l","m","n","o","p"),3);
		$decorated_array->store_deep(array("a","k","l","m","n","o","p"),6);
		$this->assertEquals(array("b"=>4,"a"=>array("k"=>array("l"=>array("m"=>array("n"=>array("o"=>array("p"=>6))))))),$our_array);
		$this->assertEquals($our_array,$decorated_array->get());
	}
}