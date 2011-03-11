#!/usr/bin/sh

echo "Graphing an XHTML page"

(php ../graphpish.phar data/homepage.html.xml > out/homepage.dot) && (twopi -Tpng out/homepage.dot > out/homepage_twopi.png)

echo