<?php
namespace Graphpish\Util\ObjectMap;

class KeyConstructorA extends \Doctrine\Common\Annotations\Annotation {
	/**
	 * Key level
	 * 
	 * How many keys this constructor sets. 
	 * 
	 * @var int
	 */
	public $value;
}