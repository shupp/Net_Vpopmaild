--TEST--
Net_Vpopmaild::robotSet()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo "Error connecting to vpopmaild\n";
}
$vp->clogin($sysadminEmail, $sysadminPass);
$result = $vp->robotSet($domain, $robot, $subject, $message, $forward);
var_dump($result);
?>
--EXPECT--
bool(true)
