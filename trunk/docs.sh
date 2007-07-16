#!/bin/sh
#cd /Users/shupp/web/mailtool || exit 99

DOCSDIR="/home/shupp/shupp.org/Net_Vpopmaild"

phpdoc \
    -s on \
    -ti 'Net_Vpopmaild Documentation' \
    -dn 'Net_Vpopmaild' \
    -t $DOCSDIR.tmp \
    -f ./Vpopmaild.php,Vpopmaild/Exception.php,$PEARDIR/PEAR/Exception.php \
    -o HTML:frames:DOM/earthli

mv $DOCSDIR $DOCSDIR.old
mv $DOCSDIR.tmp $DOCSDIR
rm -rf $DOCSDIR.old

    #-o HTML:frames:DOM/phphtmllib,HTML:Smarty:default,HTML:frames:DOM/earthli,HTML:Smarty:PHP,HTML:Smarty:HandS,HTML:frames:phpedit,HTML:frames:DOM/l0l33t,HTML:frames:DOM/default,HTML:frames:earthli \

#HTML:frames:phpedit
#HTML:frames:l0l33t  apple
#HTML:Smarty:HandS  tan
#HTML:Smarty:default 
#HTML:Smarty:PHP
#HTML:frames:earthli
#phpdoc -s off -ti 'ToasterAdmin Documentation' -dn 'ToasterAdmin' -t doc -f Includes/vpopmail_admin.php
