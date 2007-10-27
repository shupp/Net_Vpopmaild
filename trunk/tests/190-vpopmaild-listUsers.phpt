--TEST--
Net_Vpopmaild::listUsers()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}
$vp->clogin($sysadminEmail, $sysadminPass);

// listUsers()
$users = $vp->listUsers($domain);
var_dump(array_key_exists($user, $users));
?>
--EXPECT--
bool(true)
