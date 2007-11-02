--TEST--
Net_Vpopmaild::listUsers()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);

// listUsers()
$users = $vp->listUsers($domain);
var_dump(array_key_exists($user, $users));
?>
--EXPECT--
bool(true)
