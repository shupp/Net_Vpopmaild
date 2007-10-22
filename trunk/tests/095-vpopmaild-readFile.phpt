--TEST--
Net_Vpopmaild::readFile()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo "Error connecting to vpopmaild\n";
}
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->readFile($domain, $user, $testFile);
var_dump($result == $testFileContents);
?>
--EXPECT--
bool(true)
