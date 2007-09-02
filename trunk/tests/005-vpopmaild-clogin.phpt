--TEST--
Net_Vpopmaild::clogin()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}
var_dump($vp->clogin($sysadminEmail, $sysadminPass));
var_dump($vp->loginUser['clear_text_password'] == $sysadminPass);
?>
--EXPECT--
bool(true)
bool(true)
