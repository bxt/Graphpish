#!/usr/bin/sh

echo Graphing Enty-Tag relationshiips from MySQL

(php ../graphpish.phar data/tags.gsql > out/tags.gsql.dot) && (dot -Tpng out/tags.gsql.dot > out/tags.gsql_dot.png)

echo