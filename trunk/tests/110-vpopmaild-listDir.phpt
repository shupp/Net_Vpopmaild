--TEST--
Net_Vpopmaild::listDir()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->listDir($domain, $user, 'NEWDIR');
var_dump(empty($result));
?>
--EXPECT--
bool(true)
