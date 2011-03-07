<?php
namespace Graphpish\Graph;

require_once 'graphpish.php';

class WeightedTest extends \PHPUnit_Framework_TestCase {
	public function testGetWeight() {
		$w=new Weighted();
		$this->assertEquals(0,$w->getWeight(),"start weight value");
	}
	public function testIncreaseWeight() {
		$w=new Weighted();
		$this->assertEquals(0,$w->getWeight(),"start weight value");
		for($i=1;$i<=100;$i++) {
			$w->increaseWeight();
			$this->assertEquals($i,$w->getWeight(),"increasing by $i");
		}
		
	}
	public function testIncreaseWeightBy() {
		$w=new Weighted();
		$this->assertEquals(0,$w->getWeight(),"start weight value");
		$w->increaseWeight(0);
		$this->assertEquals(0,$w->getWeight(),"increasing by zero");
		for($i=1;$i<=100;$i++) {
			$littleGauss=$i*($i+1)/2;
			$w->increaseWeight($i);
			$this->assertEquals($littleGauss,$w->getWeight(),"loop $i");
		}
		
	}
}
