#!/bin/sh

# This should be run from the Net/ directory
# phpdoc required


usage() {
    echo "usage: $0 <destination_dir>"
}

if [ -z $1 ] ; then
    echo "Error: no destination_dir argument given"
    usage
    exit
fi

DOCSDIR=$0

phpdoc \
    -s on \
    -ti 'Net_Vpopmaild Documentation' \
    -dn 'Net_Vpopmaild' \
    -t $DOCSDIR.tmp \
    -f ./Vpopmaild.php,Vpopmaild/Exception.php \
    -o HTML:frames:DOM/earthli

mv $DOCSDIR $DOCSDIR.old
mv $DOCSDIR.tmp $DOCSDIR
rm -rf $DOCSDIR.old
