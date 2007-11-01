--TEST--
Net_Vpopmaild::deleteAlias()
--FILE--
<?php

require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}

$vp->clogin($sysadminEmail, $sysadminPass);
$vp->addAlias($alias, $aliasDestination1);
var_dump($vp->deleteAlias($alias));
try {
    $vp->deleteAlias($alias);
} catch (Net_Vpopmaild_Exception $e) {
}
?>
--EXPECT--
bool(true)
