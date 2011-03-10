#!/usr/bin/sh

(php ../graphpish.phar data/makefile.gsql > out/makefile.gsql.dot) && (dot -Tpng out/makefile.gsql.dot > out/makefile.gsql_dot.png)
