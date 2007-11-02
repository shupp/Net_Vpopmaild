--TEST--
Net_Vpopmaild::robotDel()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);
$result = $vp->robotDel($domain, $robot);
var_dump($result);
?>
--EXPECT--
bool(true)
