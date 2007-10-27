--TEST--
Net_Vpopmaild::addUser(), modUser(), delUser()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}
$vp->clogin($sysadminEmail, $sysadminPass);
// Remove first just to be sure
$result = $vp->delUser($domain, $nonExistentUser);

// addUser()
var_dump($vp->addUser($domain, $nonExistentUser, 'test'));

// modUser()
$info = $vp->userInfo($domain, $nonExistentUser);
$vp->setGidBit($info['gidflags'], 'no_webmail', true);
var_dump($vp->modUser($domain, $nonExistentUser, $info));

// verify modUser()
$info = $vp->userInfo($domain, $nonExistentUser);
var_dump($vp->getGidBit($info['gidflags'], 'no_webmail') == true);

// delUser()
var_dump($vp->delUser($domain, $nonExistentUser));
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
