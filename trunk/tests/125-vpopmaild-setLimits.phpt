--TEST--
Net_Vpopmaild::setLimits()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);

$result = $vp->setLimits($domain, array('disable_pop' => 1));
var_dump($result);
--EXPECT--
bool(true)
