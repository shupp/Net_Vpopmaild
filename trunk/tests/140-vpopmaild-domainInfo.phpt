--TEST--
Net_Vpopmaild::domainInfo()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->domainInfo($domain);
var_dump($result['domain'] == $domain);
?>
--EXPECT--
bool(true)
