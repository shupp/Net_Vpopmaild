--TEST--
Net_Vpopmaild::parseAliases()
--FILE--
<?php

require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}

$vp->clogin($sysadminEmail, $sysadminPass);
$vp->robotSet($domain, $robot, $subject, $message, $forward);
$vp->deleteAlias($alias);
$vp->addAlias($alias, $aliasDestination1);
$vp->addAlias($alias, $aliasDestination2);
$aliases = $vp->listAlias($domain);
$array = $vp->parseAliases($aliases, 'responders');
var_dump(array_key_exists($robot . '@' . $domain, $array));
var_dump(array_key_exists($alias, $array));
$array = $vp->parseAliases($aliases, 'forwards');
var_dump(array_key_exists($alias, $array));
$vp->deleteAlias($alias);
$vp->robotDel($domain, $robot);

?>
--EXPECT--
bool(true)
bool(false)
bool(true)
