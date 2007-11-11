--TEST--
Net_Vpopmaild::delVacation()
--FILE--
<?php

require_once 'tests-config.php';

$vp->clogin($sysadminEmail, $sysadminPass);
$vp->setVacation($user, $domain, $vacationSubject, $vacationMessage);
var_dump($vp->delVacation($user, $domain));
var_dump($vp->getVacation($vp->loginUser));
?>
--EXPECT--
bool(true)
NULL
