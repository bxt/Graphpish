#!/usr/bin/sh

echo "Graphing an .xt trace file of an early version of Graphpish"
echo "This demo might take a second..."

(php ../graphpish.phar data/own-trace.xt > out/own-trace.xt.dot) && (dot -Tpng out/own-trace.xt.dot > out/own-trace.xt_dot.png)

echo