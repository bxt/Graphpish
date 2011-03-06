<?php

xdebug_start_trace("simple");

define('NL',"\n");

foo('a');
foo('aa');
foo('aaa');

function foo($a) {

echo strlen($a);

}

print NL;