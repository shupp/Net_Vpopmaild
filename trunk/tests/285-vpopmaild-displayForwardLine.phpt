--TEST--
Net_Vpopmaild::displayForwardLine()
--FILE--
<?php

require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}

$vp->clogin($sysadminEmail, $sysadminPass);
var_dump($aliasDestination1 == $vp->displayForwardLine('&' . $aliasDestination1));

?>
--EXPECT--
bool(true)
