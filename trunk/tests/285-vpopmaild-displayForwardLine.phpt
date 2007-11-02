--TEST--
Net_Vpopmaild::displayForwardLine()
--FILE--
<?php

require_once('tests-config.php');

$vp->clogin($sysadminEmail, $sysadminPass);
var_dump($aliasDestination1 == $vp->displayForwardLine('&' . $aliasDestination1));

?>
--EXPECT--
bool(true)
