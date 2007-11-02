--TEST--
Net_Vpopmaild::parseHomeDotqmail()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);
$array = $vp->parseHomeDotQmail($homeDotQmailFileDelete, $vp->loginUser);
var_dump($array['routing'] == 'routing_deleted');
?>
--EXPECT--
bool(true)
