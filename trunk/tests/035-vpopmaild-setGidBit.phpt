--TEST--
Net_Vpopmaild::setGidBit()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo "Error connecting to vpopmaild\n";
}
$vp->clogin($sysadminEmail, $sysadminPass);
$vp->setGidBit($vp->loginUser['gidflags'], 'no_imap', true);
var_dump($vp->getGidBit($vp->loginUser['gidflags'], 'no_imap'));
?>
--EXPECT--
bool(true)
