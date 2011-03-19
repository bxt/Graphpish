Graphpish - Render Graphs with PHP & Graphviz
=============================================

Version 1.0

Graphpish features several:

- Parse directory trees into graphs
- Parse xml files into graphs
- Graphs from mysql or sqlite3 databases
- Download and graph HTML websites
- Parse XDebug's .xt trace files into graphs
- Output .dot for Graphviz or JS-Code for springy
- PHP classes handling graphs and easy .dot-File creation
- And maybe some day even more...

Getting Started
---------------

After cloning the repository, you could first perform various tasks defined in the Makefile. 

	make phar

This will craete a .phar archive of all the needed php files. To use the classes in your php scripts, include the .phar file, which registers an autoloader. 

	make demos

This will show off all features and create graphs in `demos/out/`

If you just want start graphing stuff, you can use the CLI:

	php graphpish.phar demos/data/simple.xml | dot -Tpng > /demos/out/simple.xml_dot.png

This will render a simple xml file into a graph. 
