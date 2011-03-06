#!/usr/bin/sh

(cd data && php simple.php) && (php ../graphpish.phar data/simple.xt | dot -Tpng > out/simple.xt_dot.png)
