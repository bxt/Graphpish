Graphpish - Render Graphs with PHP & Graphviz
=============================================

Graphpish features several:
- A collection of PHP classes and functions for easy .dot-File creation
- A script to parse XDebug's .xt trace files into graphs
- and maybe some day even more

Getting Started
---------------

	make phar

This will craten a .phar archive of all the needed php files. To use the classes in your php scripts, include the .phar file, which registers an autoloader. 

	make demos

This will create some graph .pngs in `demos/out/` and execute something like this:

	php graphpish.phar demos/data/simple.xt | dot -Tpng > /demos/out/simple.xt_dot.png

This will render a simple XDebug trace file. 
