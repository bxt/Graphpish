#!/usr/bin/sh

echo "Graphing Makefile dependencies of Graphpishs Makefile as stroed in an SQLite-Database"

(php ../graphpish.phar data/makefile.gsql > out/makefile.gsql.dot) && (dot -Tpng out/makefile.gsql.dot > out/makefile.gsql_dot.png)

echo