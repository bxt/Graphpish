<?php
namespace Graphpish\Util;

require_once 'graphpish.php';

class DeepIniParserTest extends \PHPUnit_Framework_TestCase {
	public function testItitialisation() {
		$p=new DeepIniParser();
		$p->process(array("node"=>array(),"edge"=>array()));
		$this->assertEquals(array("node"=>array(),"edge"=>array()),$p->getOpts(),"start options");
	}
	/**
	 * @dataProvider sampleArrays
	 */
	public function testArrayManipulation($input,$expecedOutput) {
		$p=new DeepIniParser();
		$p->process(array("node"=>array(),"edge"=>array()));
		$p->process($input);
		$this->assertEquals($expecedOutput,$p->getOpts());
	}
	public function testSubsequentCalls() {
		$p=new DeepIniParser();
		$p->process(array("node"=>array(),"edge"=>array()));
		$this->assertEquals(array("node"=>array(),"edge"=>array()),$p->getOpts());
		$p->process(array("node"=>array(),"edge"=>array("a"=>"t1"),"foo:bar"=>array("x"=>"y")));
		$this->assertEquals(array("node"=>array(),"edge"=>array("a"=>"t1"),"foo"=>array("bar"=>array("x"=>"y"))),$p->getOpts());
		$p->process(array("edge-a"=>"t2","foo:bar/x"=>"z"));
		$this->assertEquals(array("node"=>array(),"edge"=>array("a"=>"t2"),"foo"=>array("bar"=>array("x"=>"z"))),$p->getOpts());
	}
	
	public function sampleArrays() {
		return array(
			array(array(
				"connection"=>array("opt1"=>"optval1","opt2"=>"optval2")
			),array(
				"node"=>array(),"edge"=>array(),"connection"=>array("opt1"=>"optval1","opt2"=>"optval2")
			)),
			array(array(
				"connection"=>array("opt1"=>"optval1","opt2"=>"optval2"),"node"=>array("nopt1"=>"noptval1","nopt2"=>"noptval2")
			),array(
				"node"=>array("nopt1"=>"noptval1","nopt2"=>"noptval2"),"edge"=>array(),"connection"=>array("opt1"=>"optval1","opt2"=>"optval2")
			)),
			array(array(
				"connection"=>array("opt1"=>"optval1","opt2"=>"optval2"),"node:bla"=>array("nopt1"=>"noptval1","nopt2"=>"noptval2")
			),array(
				"node"=>array("bla"=>array("nopt1"=>"noptval1","nopt2"=>"noptval2")),"edge"=>array(),"connection"=>array("opt1"=>"optval1","opt2"=>"optval2")
			)),
			array(array(
				"connection"=>array("opt1"=>"optval1","opt2"=>"optval2"),"node:bla/blubb"=>array("nopt1"=>"noptval1","nopt2"=>"noptval2")
			),array(
				"node"=>array("bla"=>array("blubb"=>array("nopt1"=>"noptval1","nopt2"=>"noptval2"))),"edge"=>array(),"connection"=>array("opt1"=>"optval1","opt2"=>"optval2")
			)),
			array(array(
				"connection"=>array("opt1"=>"optval1","opt2"=>"optval2"),"node:bla/blubb/clone"=>array("nopt1"=>"noptval1","nopt2"=>"noptval2")
			),array(
				"node"=>array("bla"=>array("blubb"=>array("clone"=>array("nopt1"=>"noptval1","nopt2"=>"noptval2")))),"edge"=>array(),"connection"=>array("opt1"=>"optval1","opt2"=>"optval2")
			)),
			array(array(
				"connection"=>array("opt1"=>"optval1","opt2"=>"optval2"),
				"node:bla/blubb/clone"=>array("nopt1"=>"noptval1","nopt2"=>"noptval2"),
				"edge:home-boy"=>array("eopt1"=>"eoptval1","eopt2"=>"eoptval2"),
			),array(
				"node"=>array("bla"=>array("blubb"=>array("clone"=>array("nopt1"=>"noptval1","nopt2"=>"noptval2")))),
				"edge"=>array("home"=>array("boy"=>array("eopt1"=>"eoptval1","eopt2"=>"eoptval2"))),
				"connection"=>array("opt1"=>"optval1","opt2"=>"optval2"),
			)),
			array(array(
				"connection"=>array("opt1"=>"optval1","opt2"=>"optval2"),
				"node:bla/blubb/clone"=>array("nopt1"=>"noptval1","nopt2"=>"noptval2"),
				"edge:home-boy/ich-kann-es-sehn/du-wuerdest-gerne/sein/wie/ich"=>array("eopt1"=>"eoptval1","eopt2"=>"eoptval2"),
			),array(
				"node"=>array("bla"=>array("blubb"=>array("clone"=>array("nopt1"=>"noptval1","nopt2"=>"noptval2")))),
				"edge"=>array("home"=>array("boy"=>array("ich-kann-es-sehn"=>array("du-wuerdest-gerne"=>array("sein"=>array("wie"=>array("ich"=>array("eopt1"=>"eoptval1","eopt2"=>"eoptval2")))))))),
				"connection"=>array("opt1"=>"optval1","opt2"=>"optval2"),
			)),
			array(array(
				"con::nection"=>array("opt1"=>"optval1","opt2"=>"optval2")
			),array(
				"node"=>array(),"edge"=>array(),"con"=>array("nection"=>array("opt1"=>"optval1","opt2"=>"optval2"))
			)),
			array(array(
				"con-nect:ion"=>array("opt1"=>"optval1","opt2"=>"optval2")
			),array(
				"node"=>array(),"edge"=>array(),"con"=>array("nect:ion"=>array("opt1"=>"optval1","opt2"=>"optval2"))
			)),
		);
	}
}