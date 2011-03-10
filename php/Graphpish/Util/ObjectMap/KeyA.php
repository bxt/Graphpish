<?php
namespace Graphpish\Util\ObjectMap;

class KeyA extends \Doctrine\Common\Annotations\Annotation {
	/**
	 * Key level
	 * 
	 * Used for key ordering. 
	 * @var int
	 */
	public $value;
}