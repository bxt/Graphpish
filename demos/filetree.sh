#!/usr/bin/sh

(php ../graphpish.phar ../ > out/filetree.dot) && (twopi -Tpng out/filetree.dot > out/filetree_twopi.png)
