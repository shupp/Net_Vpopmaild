--TEST--
Net_Vpopmaild::addDomain()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo "Error connecting to vpopmaild\n";
}
$vp->clogin($sysadminEmail, $sysadminPass);

$vp->delDomain($nonExistentDomain);
$result = $vp->addDomain($nonExistentDomain, $sysadminPass);
var_dump($result);
$vp->delDomain($nonExistentDomain);
?>
--EXPECT--
bool(true)
