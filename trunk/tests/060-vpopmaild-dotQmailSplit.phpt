--TEST--
Net_Vpopmaild::dotQmailSplit()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);
$array = $vp->dotQmailSplit($dotQmailFile);
var_dump($array['Forward'][0] == $dotQmailFile['Forward']);
var_dump($array['Delivery'][0] == $dotQmailFile['Delivery']);
var_dump($array['Comment'][0] == $dotQmailFile['Comment']);
var_dump($array['Program'][0] == $dotQmailFile['Program']);
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
