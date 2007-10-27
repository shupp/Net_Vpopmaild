--TEST--
Net_Vpopmaild::readInfo()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}

// clogin uses readInfo to populate $this->loginUser
$vp->clogin($sysadminEmail, $sysadminPass);
var_dump($vp->loginUser['name'] == $user);
?>
--EXPECT--
bool(true)
