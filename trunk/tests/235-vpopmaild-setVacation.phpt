--TEST--
Net_Vpopmaild::setVacation()
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
?>
--EXPECT--
