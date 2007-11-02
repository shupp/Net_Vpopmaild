--TEST--
Net_Vpopmaild::isDomainAdmin()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);

try {
    $vp->delUser($domain, $nonExistentUser);
} catch (Net_Vpopmaild_Exception $e) {
}

$vp->addUser($domain, $nonExistentUser, 'test');
$info = $vp->userInfo($domain, $nonExistentUser);
var_dump($vp->isDomainAdmin($domain, $info));
$vp->setGidBit($info['gidflags'], 'system_admin_privileges', true);
var_dump($vp->modUser($domain, $nonExistentUser, $info));
$info = $vp->userInfo($domain, $nonExistentUser);
var_dump($vp->isDomainAdmin($domain, $info));
$vp->delUser($domain, $nonExistentUser);
?>
--EXPECT--
bool(false)
bool(true)
bool(true)
