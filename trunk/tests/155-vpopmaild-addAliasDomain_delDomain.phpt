--TEST--
Net_Vpopmaild::addAliasDomain(), Net_Vpopmaild::delDomain()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);

$aliasdomain = 'alias' . $domain;
$result = $vp->addAliasDomain($domain, $aliasdomain);
var_dump($result);
$result = $vp->delDomain($aliasdomain);
var_dump($result);
?>
--EXPECT--
bool(true)
bool(true)
