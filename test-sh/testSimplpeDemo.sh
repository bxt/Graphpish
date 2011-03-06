 #!/usr/bin/sh

echo "Testing graphpish.phar with demo file demos/simple.xt..."

mkdir -p tmp-test

php "graphpish.phar" "demos/simple.xt" > tmp-test/simple.dot

echo "  compare result..."

cmp -n 430 - tmp-test/simple.dot <<EXPECTED_DOTFILE
strict digraph G {

  overlap=false
  root=node186495f7
  splines=true
  epsilon=0.0000001
  sep=.2

  node [fontsize=9]
  edge [fontsize=7]


  node186495f7 [ penwidth=1,  label = "MAIN (1)" ];
  nodef1edc500 [ penwidth=1,  label = "define (1)" ];
  nodeacbd18db [ penwidth=1,  label = "foo (3)" ];
  node73d3a702 [ penwidth=1,  label = "strlen (3)" ];


  node186495f7 -> nodef1edc500 [ penwidth=1, weight=0, label="1", color="#625d9399" ];
  node186495f7 -> nodeacbd18db [ penwidth=1, weight=1,431363764159, label="3", color="#05825899" ];
  nodeacbd18db -> node73d3a702 [ penwidth=1, weight=1,431363764159, label="3", color="#5b574199" ];


}


EXPECTED_DOTFILE

rm -Rvf tmp-test
