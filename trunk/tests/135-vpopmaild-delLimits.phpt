--TEST--
Net_Vpopmaild::delLimits()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo "Error connecting to vpopmaild\n";
}
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->delLimits($domain);
var_dump($result);
?>
--EXPECT--
bool(true)
