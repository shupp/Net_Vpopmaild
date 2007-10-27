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
$vp->setDebug();
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->addDomain($nonExistentDomain, $sysadminPass);
var_dump($result);
?>
--EXPECT--
bool(true)
