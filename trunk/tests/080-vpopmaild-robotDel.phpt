--TEST--
Net_Vpopmaild::robotDel()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo "Error connecting to vpopmaild\n";
}
$vp->clogin($sysadminEmail, $sysadminPass);
$result = $vp->robotDel($domain, $robot);
var_dump($result);
?>
--EXPECT--
bool(true)
