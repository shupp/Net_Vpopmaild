--TEST--
Net_Vpopmaild::getGidBit()
--FILE--
<?php
require_once('tests-config.php');
var_dump($vp->clogin($sysadminEmail, $sysadminPass));
var_dump($vp->getGidBit($vp->loginUser['gidflags'], 'system_admin_privileges'));
?>
--EXPECT--
bool(true)
bool(true)
