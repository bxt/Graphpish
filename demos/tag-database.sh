#!/usr/bin/sh

(php ../graphpish.phar data/tags.gsql > out/tags.gsql.dot) && (dot -Tpng out/tags.gsql.dot > out/tags.gsql_dot.png)
