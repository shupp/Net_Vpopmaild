--TEST--
Net_Vpopmaild::getLimits()
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

$result = $vp->getLimits($domain);
var_dump($result['disable_pop'] == true);
?>
--EXPECT--
bool(true)
