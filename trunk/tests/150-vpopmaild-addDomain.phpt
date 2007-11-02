--TEST--
Net_Vpopmaild::addDomain()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);

try {
    $vp->delDomain($nonExistentDomain);
} catch (Net_Vpopmaild_Exception $e) {
}
$result = $vp->addDomain($nonExistentDomain, $sysadminPass);
var_dump($result);

try {
    $vp->delDomain($nonExistentDomain);
} catch (Net_Vpopmaild_Exception $e) {
}
?>
--EXPECT--
bool(true)
