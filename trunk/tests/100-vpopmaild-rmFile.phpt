--TEST--
Net_Vpopmaild::rmFile()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo "Error connecting to vpopmaild\n";
}
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->rmFile($domain, $user, $testFile);
var_dump($result);
?>
--EXPECT--
bool(true)
