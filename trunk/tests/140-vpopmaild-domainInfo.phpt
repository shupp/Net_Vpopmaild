--TEST--
Net_Vpopmaild::domainInfo()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo "Error connecting to vpopmaild\n";
}
$vp->setDebug();
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->domainInfo($domain);
var_dump($result['domain'] == $domain);
?>
--EXPECT--
bool(true)
