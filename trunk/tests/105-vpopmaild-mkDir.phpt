--TEST--
Net_Vpopmaild::mkDir()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo "Error connecting to vpopmaild\n";
}
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->mkDir($domain, $user, 'NEWDIR');
var_dump($result);
?>
--EXPECT--
bool(true)
