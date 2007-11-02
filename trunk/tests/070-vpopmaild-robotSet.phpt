--TEST--
Net_Vpopmaild::robotSet()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);
$result = $vp->robotSet($domain, $robot, $subject, $message, $forward);
var_dump($result);
?>
--EXPECT--
bool(true)
