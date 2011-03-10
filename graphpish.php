<?php
namespace Graphpish;

//xdebug_start_trace("demos/data/own-trace");

init();

if(isset($argv[0])&&basename(__FILE__)==basename($argv[0])) {
	Cli::run($argv);
}

function init(){
	if (version_compare('5.3.0', PHP_VERSION, '>')) {
			die("graphpish requires PHP 5.3.0 or newer. \n");
	}
	if (!extension_loaded('phar')) {
			die("graphpish requires PHAR Extension. \n");
	}
	if(!\phar::canCompress()) {
			die("graphpish requires PHAR Extension with compression support. \n");
	}
	
	if(strrchr(__FILE__, '.')==".phar") {
		$path='phar://'.__FILE__.'/';
	} else {
		$path=__DIR__.'/';
	}
	
	if(!class_exists("SplClassLoader")) {
		require $path.'lib/SplClassLoader.php';
	}
	$l_own=new SplClassLoader(__NAMESPACE__,$path.'php/');
	$l_own->register();
	$l_lib=new SplClassLoader(null,$path.'lib/');
	$l_lib->register();
}

__HALT_COMPILER(); ?>