--TEST--
Net_Vpopmaild::getLimits()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->getLimits($domain);
var_dump($result['disable_pop'] == true);
?>
--EXPECT--
bool(true)
