#!/usr/bin/sh

echo "Graphing a simple XML file, aggregating text values"

(php ../graphpish.phar -p "Graphpish\Xml\Reader" --catchAttributeValues --catchTextnodes data/simple.xml > out/simple.xml.dot) && (twopi -Tpng out/simple.xml.dot > out/simple.xml_twopi.png)

echo