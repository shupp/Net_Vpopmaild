--TEST--
Net_Vpopmaild::getQuota()
--FILE--
<?php
require_once('tests-config.php');
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
