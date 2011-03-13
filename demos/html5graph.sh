#!/usr/bin/sh

echo "Downloading and graphing an HTML5 page"

(php ../graphpish.phar  -p "Graphpish\Xml\RemoteHtml" "http://www.virtuti.info/"  > out/html5.dot) && (twopi -Tpng out/html5.dot > out/html5_twopi.png)

echo