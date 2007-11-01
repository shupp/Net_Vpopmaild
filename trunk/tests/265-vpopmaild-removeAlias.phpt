--TEST--
Net_Vpopmaild::removeAlias()
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
$vp->addAlias($alias, $aliasDestination1);
$vp->addAlias($alias, $aliasDestination2);

$result = $vp->listAlias($domain, $aliasUser);
var_dump(in_array($aliasDestination2, $result[$alias]));

$vp->removeAlias($alias, $aliasDestination2);
$result = $vp->listAlias($domain, $aliasUser);
var_dump(in_array($aliasDestination2, $result[$alias]));

$vp->deleteAlias($alias);
?>
--EXPECT--
bool(true)
bool(false)
