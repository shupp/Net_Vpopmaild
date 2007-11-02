--TEST--
Net_Vpopmaild::getAliasContents()
--FILE--
<?php

require_once('tests-config.php');

$vp->clogin($sysadminEmail, $sysadminPass);
try {
    $vp->deleteAlias($alias);
} catch (Net_Vpopmaild_Exception $e) {
}
$vp->addAlias($alias, $aliasDestination1);
$vp->addAlias($alias, $aliasDestination2);
$result = $vp->listAlias($domain, $aliasUser);
$contents = $vp->getAliasContents($result[$alias]);
$string = $aliasDestination1 . ', ' . $aliasDestination2;
var_dump($contents == $string);
$vp->deleteAlias($alias);
?>
--EXPECT--
bool(true)
