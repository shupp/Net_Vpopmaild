--TEST--
Net_Vpopmaild::setLimits()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo "Error connecting to vpopmaild\n";
}
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->setLimits($domain, array('disable_pop' => 1));
var_dump($result);
--EXPECT--
bool(true)
