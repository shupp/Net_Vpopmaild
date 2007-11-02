--TEST--
Net_Vpopmaild::readInfo()
--FILE--
<?php
require_once('tests-config.php');

// clogin uses readInfo to populate $this->loginUser
$vp->clogin($sysadminEmail, $sysadminPass);
var_dump($vp->loginUser['name'] == $user);
?>
--EXPECT--
bool(true)
