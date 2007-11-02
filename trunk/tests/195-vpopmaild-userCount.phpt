--TEST--
Net_Vpopmaild::userCount()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);

// listUsers()
var_dump($userCount == $vp->userCount($domain));
?>
--EXPECT--
bool(true)
