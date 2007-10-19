--TEST--
Net_Vpopmaild::listLists()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo "Error connecting to vpopmaild\n";
}
$vp->clogin($sysadminEmail, $sysadminPass);
$result = $vp->listLists($domain);
var_dump(empty($result));
?>
--EXPECT--
bool(true)
