--TEST--
Net_Vpopmaild::findDomain()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->findDomain($domain);
var_dump(!is_null($result));
var_dump($result > 0);
?>
--EXPECT--
bool(true)
bool(true)
