--TEST--
Net_Vpopmaild::readFile()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->readFile($domain, $user, $testFile);
var_dump($result == $testFileContents);
?>
--EXPECT--
bool(true)
