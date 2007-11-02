--TEST--
Net_Vpopmaild::listLists()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);
$result = $vp->listLists($domain);
var_dump(empty($result));
?>
--EXPECT--
bool(true)
