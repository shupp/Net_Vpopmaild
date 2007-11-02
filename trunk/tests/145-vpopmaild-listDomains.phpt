--TEST--
Net_Vpopmaild::listDomains()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);

$count = $vp->domainCount();
$array = $vp->listDomains();
var_dump($count == count($array));
?>
--EXPECT--
bool(true)
