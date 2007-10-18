--TEST--
Net_Vpopmaild::robotGet()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo "Error connecting to vpopmaild\n";
}
$vp->clogin($sysadminEmail, $sysadminPass);
$result = $vp->robotGet($domain, $robot);
var_dump($result['Number']  == $vp->vpopmail_robot_number);
var_dump($result['Time']    == $vp->vpopmail_robot_time);
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
