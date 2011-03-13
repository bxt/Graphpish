<?php
namespace Graphpish\Xml;
use Graphpish\Util\ObjectMap\StorePretty;

class Reader implements \Graphpish\Cli\PluginI {
	private $edges;
	private $nodes;
	private $root;
	
	private $options=array(
		"catchAttributes"=>true,
		"aggregateAttributes"=>true,
		"catchAttributeValues"=>false,
		"aggregateAttributeValues"=>true,
		"catchTextnodes"=>false,
		"aggregateTextnodes"=>true,
	);
	
	public function __construct() {
		$this->nodes=new StorePretty(1,'Graphpish\Graph\Node');
		$this->edges=new StorePretty(2,'Graphpish\Graph\Edge');
		$this->root=null;
	}
	
	public function setOptions(array $options) {
		$this->options=array_merge($this->options,$options);
		return $this;
	}
	
	public function read($xmlfile) {
		if($xmlfile instanceof \SimpleXMLElement) {
			$xmldoc=$xmlfile;
		} else {
			$xmldoc=simplexml_load_file($xmlfile);
		}
		$this->read_inner($xmldoc);
		return $this;
	}
	
	public function getGraph() {
		return new \Graphpish\Graph\Graph($this->nodes->dump(),$this->edges->dump(),$this->root);
	}
	
	private function read_inner($xmlparent) {
		$nodes=$this->nodes;
		$edges=$this->edges;
		
		$parent=$this->nodeStyle($xmlparent->getName(), $xmlparent->getName())->increaseWeight();
		if(!$this->root) {
			$this->root=$parent;
		}
		
		foreach($xmlparent->children() as $xmlnode) {
			$node=$nodes($xmlnode->getName());
			$edges($parent,$node)->increaseWeight();
			$this->read_inner($xmlnode);
		}
		
		$this->catchAttributes($parent,$xmlparent);
		$this->catchTextnode($parent,$xmlparent);
	}
	
	private function catchTextnode($parent,$xmlparent) {
		if(!$this->_("catchTextnodes")) return false;
		$edges=$this->edges;
		
		$xmlcddata=trim((string) $xmlparent);
		if($xmlcddata!='') {
			$cddataId=$xmlparent->getName().'/-/'.md5($xmlcddata);
			if($this->_("aggregateTextnodes")) {
				$cddataId='cd-'.md5($xmlcddata);
			}
			$cddata=$this->textStyle($cddataId,$xmlcddata)->increaseWeight();
			$edges($parent,$cddata)->increaseWeight();
		}
	}
	
	private function catchAttributes($parent,$xmlparent) {
		if(!$this->_("catchAttributes")) return false;
		$edges=$this->edges;
		
		foreach($xmlparent->attributes() as $xmlattr=>$xmlattrVal) {
			$attrId=$xmlparent->getName().'/'.$xmlattr;
			if($this->_("aggregateAttributes")) {
				$attrId='attr-'.$xmlattr;
			}
			$attr=$this->attributeStyle($attrId,$xmlattr)->increaseWeight();
			$edges($parent,$attr)->increaseWeight();
			
			$this->catchAttributeValue($parent,$xmlparent,$attr,$xmlattr,$xmlattrVal);
		}
	}
	
	private function catchAttributeValue($parent,$xmlparent,$attr,$xmlattr,$xmlattrVal) {
		if(!$this->_("catchAttributeValues")) return false;
		$edges=$this->edges;
		
		$attrValId=$xmlparent->getName().'/'.$xmlattr.'/'.md5($xmlattrVal);
		if($this->_("aggregateAttributeValues")) {
			$attrValId='attrV-'.md5($xmlattrVal);
		}
		$attrVal=$this->attributeValueStyle($attrValId,$xmlattrVal)->increaseWeight();
		$edges($attr,$attrVal)->increaseWeight();
	}
	
	private function attributeStyle($id, $label) {
		$nodes=$this->nodes;
		return $nodes($id)
			->setLabel('@'.$label)
			->setRenderOpt("shape","diamond")
		;
	}
	private function attributeValueStyle($id, $label) {
		$nodes=$this->nodes;
		return  $nodes($id)
			->setLabel(self::plaintext2Label($label))
			->setRenderOpt("fontsize","7")
		;
	}
	private function textStyle($id, $label) {
		$nodes=$this->nodes;
		return  $nodes($id)
			->setLabel(self::plaintext2Label($label))
			->setRenderOpt("fontsize","7")
		;
	}
	private function nodeStyle($id, $label) {
		$nodes=$this->nodes;
		return  $nodes($id)
			->setRenderOpt("shape","box")
			->setLabel('<'.$label.'>')
		;
	}
	
	private function _($option) {
		return $this->options[$option];
	}
	
	private static function plaintext2Label($text) {
		if(strlen($text)>13) {
			$text=substr($text,0,10).'...';
		}
		return str_replace("\n",'',$text);
	}
	
	
	public function cli(array $argv) {
		if(count($argv)<1) {
			throw new \Graphpish\Cli\ParameterException("Xml Plugin accepts at least 1 argument");
		}
		$files=array();
		$useopts=true;
		while($opt=array_shift($argv)) {
			if($useopts&&$opt[0]=='-'&&$opt[1]=='-') {
				$opt=substr($opt,2);
				$val=true;
				if($opt[0]=='n'&&$opt[1]=='o') {
					$val=false;
					$opt=lcfirst(substr($opt,2));
				}
				if(isset($this->options[$opt])) {
					$this->options[$opt]=true;
				} else {
					throw new \Graphpish\Cli\ParameterException("Xml Plugin does not accept --$opt");
				}
			} else {
				$useopts=false;
				$files[]=$opt;
			}
		}
		if(count($files)==0) {
			throw new \Graphpish\Cli\ParameterException("Specify input files!");
		}
		foreach($files as $file) {
			$this->read($file);
		}
	}
}
