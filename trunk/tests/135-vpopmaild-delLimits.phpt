--TEST--
Net_Vpopmaild::delLimits()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->delLimits($domain);
var_dump($result);
?>
--EXPECT--
bool(true)
