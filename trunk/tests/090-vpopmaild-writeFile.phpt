--TEST--
Net_Vpopmaild::writeFile()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->writeFile($testFileContents, $domain, $user, $testFile);
var_dump($result);
?>
--EXPECT--
bool(true)
