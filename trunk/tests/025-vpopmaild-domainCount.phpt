--TEST--
Net_Vpopmaild::domainCount()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}
$vp->clogin($sysadminEmail, $sysadminPass);
var_dump($vp->domainCount() == $domainCount);
?>
--EXPECT--
bool(true)