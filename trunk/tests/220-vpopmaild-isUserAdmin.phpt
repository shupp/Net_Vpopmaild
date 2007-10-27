--TEST--
Net_Vpopmaild::isUserAdmin()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}

// Create temp user
$vp->authenticate($sysadminEmail, $sysadminPass);
$vp->addUser($domain, $nonExistentUser, 'test');
$vp->quit();

// Login as that user to test isUserAdmin
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}
$vp->authenticate($nonExistentUser . '@' . $domain, 'test');
var_dump($vp->isUserAdmin($user, $domain));
var_dump($vp->isUserAdmin($nonExistentUser, $domain));
$vp->quit();

// Login as sysadmin user to delete test user
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}
$vp->authenticate($sysadminEmail, $sysadminPass);
$vp->delUser($domain, $nonExistentUser);
$vp->quit();
?>
--EXPECT--
bool(false)
bool(true)