--TEST--
Net_Vpopmaild::getAliasContents()
--FILE--
<?php

require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}

// clogin uses readInfo to populate $this->loginUser
$vp->clogin($sysadminEmail, $sysadminPass);
$vp->deleteAlias($alias);
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
