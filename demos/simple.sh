#!/usr/bin/sh

echo "Running a simple php script, tracing its function calls, and graphing the trace"

(cd data && php simple.php) && (mv data/simple.xt out/simple.xt) && (php ../graphpish.phar out/simple.xt | dot -Tpng > out/simple.xt_dot.png)

echo