Graphpish - Render Graphs with PHP & Graphviz
=============================================

Graphpish features several:

- parse directory trees into graphs
- parse xml files into graphs
- graphs from mysql/sqlite databases
- parse XDebug's .xt trace files into graphs
- PHP classes handling graphs and easy .dot-File creation
- and maybe some day even more

Getting Started
---------------

After cloning the repository, you can perform various tasks defined in the Makefile. 

	make phar

This will craete a .phar archive of all the needed php files. To use the classes in your php scripts, include the .phar file, which registers an autoloader. 

	make demos

This will create some graph in `demos/out/`

If you just want start graphing stuff, you can use the CLI:

	php graphpish.phar demos/data/simple.xml | dot -Tpng > /demos/out/simple.xml_dot.png

This will render a simple XDebug trace file into a graph. 
