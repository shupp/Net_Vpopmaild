--TEST--
Net_Vpopmaild::authenticate()
--FILE--
<?php
require_once('tests-config.php');
var_dump($vp->authenticate($sysadminEmail, $sysadminPass));
var_dump($vp->loginUser['clear_text_password'] == $sysadminPass);
?>
--EXPECT--
bool(true)
bool(true)
