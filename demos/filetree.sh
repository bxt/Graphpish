#!/usr/bin/sh

echo "Graphing the Graphpish directory tree..."

(php ../graphpish.phar ../ > out/filetree.dot) && (twopi -Tpng out/filetree.dot > out/filetree_twopi.png)

echo