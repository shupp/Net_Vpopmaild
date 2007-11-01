--TEST--
Net_Vpopmaild::addAlias()
--FILE--
<?php

require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}

$vp->clogin($sysadminEmail, $sysadminPass);
try {
    $vp->deleteAlias($alias);
} catch (Net_Vpopmaild_Exception $e) {
}

var_dump($vp->addAlias($alias, $aliasDestination1));
var_dump($vp->addAlias($alias, $aliasDestination2));
$vp->deleteAlias($alias);
?>
--EXPECT--
bool(true)
bool(true)
