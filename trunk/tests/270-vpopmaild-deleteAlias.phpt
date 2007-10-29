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
var_dump($vp->deleteAlias($alias));
?>
--EXPECT--
bool(true)
bool(false)
