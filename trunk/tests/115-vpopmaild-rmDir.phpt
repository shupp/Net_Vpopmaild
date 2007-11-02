--TEST--
Net_Vpopmaild::rmDir()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->rmDir($domain, $user, 'NEWDIR');
var_dump($result);
?>
--EXPECT--
bool(true)
