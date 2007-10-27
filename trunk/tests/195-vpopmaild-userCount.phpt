--TEST--
Net_Vpopmaild::userCount()
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
var_dump($userCount == $vp->userCount($domain));
?>
--EXPECT--
bool(true)
