--TEST--
Net_Vpopmaild::mkDir()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->mkDir($domain, $user, 'NEWDIR');
var_dump($result);
?>
--EXPECT--
bool(true)
