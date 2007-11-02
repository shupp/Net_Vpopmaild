--TEST--
Net_Vpopmaild::robotGet()
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);
$result = $vp->robotGet($domain, $robot);
var_dump($result['Number']  == $vp->vpopmailRobotNumber);
var_dump($result['Time']    == $vp->vpopmailRobotTime);
var_dump($result['Subject'] == $subject);
var_dump($result['Forward'] == $forward);
var_dump($result['Message'] == split("\n", $message));
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
