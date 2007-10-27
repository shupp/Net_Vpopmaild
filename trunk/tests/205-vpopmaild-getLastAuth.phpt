--TEST--
Net_Vpopmaild::getLastAuth()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}
$vp->clogin($sysadminEmail, $sysadminPass);

// getLastAuth()
$array = $vp->getLastAuth($domain, $user);
var_dump(array_key_exists('ip', $array));
var_dump(array_key_exists('time', $array));
?>
--EXPECT--
bool(true)
bool(true)
