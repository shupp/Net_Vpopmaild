--TEST--
Net_Vpopmaild::status*
--FILE--
<?php
require_once('tests-config.php');

var_dump($vp->statusOK('+OK+'));
var_dump($vp->statusOKMore('+OK+'));
var_dump($vp->statusOKNoMore('+OK'));
var_dump($vp->statusErr('-ERR Some Error Message'));
var_dump($vp->dotOnly('.'));
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
