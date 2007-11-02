--TEST--
Net_Vpopmaild::domainCount()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);
var_dump($vp->domainCount() == $domainCount);
?>
--EXPECT--
bool(true)
