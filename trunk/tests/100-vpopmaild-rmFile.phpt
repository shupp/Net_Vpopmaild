--TEST--
Net_Vpopmaild::rmFile()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->rmFile($domain, $user, $testFile);
var_dump($result);
?>
--EXPECT--
bool(true)
