--TEST--
Net_Vpopmaild::getVacation()
--FILE--
<?php

require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}

$vp->clogin($sysadminEmail, $sysadminPass);
$vp->setVacation($user, $domain, $vacationSubject, $vacationMessage);
var_dump($vp->delVacation($user, $domain));
var_dump($vp->getVacation('', $vp->loginUser));
?>
--EXPECT--
bool(true)
NULL
