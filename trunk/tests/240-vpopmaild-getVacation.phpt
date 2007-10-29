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
$vacation = $vp->getVacation('', $vp->loginUser);
var_dump($vacation['vacation_subject'] == $vacationSubject);
var_dump($vacation['vacation_body'] == $vacationMessage);
var_dump($vacation['vacation'] == ' checked');
var_dump($vp->delVacation($user, $domain));
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
