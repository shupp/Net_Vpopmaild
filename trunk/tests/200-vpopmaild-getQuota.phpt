--TEST--
Net_Vpopmaild::getQuota()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}
$vp->clogin($sysadminEmail, $sysadminPass);

$newQuota = '20MB';

$info = $vp->userInfo($domain, $user);
$info['quota'] = $newQuota;
$vp->modUser($domain, $user, $info);
$info = $vp->userInfo($domain, $user);

// getQuota();
var_dump($vp->getQuota($info['quota']) == $newQuota);
?>
--EXPECT--
bool(true)
