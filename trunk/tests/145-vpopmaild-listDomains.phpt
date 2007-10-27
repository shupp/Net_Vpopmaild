--TEST--
Net_Vpopmaild::listDomains()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo "Error connecting to vpopmaild\n";
}
$vp->clogin($sysadminEmail, $sysadminPass);

$count = $vp->domainCount();
$array = $vp->listDomains();
var_dump($count == count($array));
?>
--EXPECT--
bool(true)
