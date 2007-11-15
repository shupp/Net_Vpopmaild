--TEST--
Net_Vpopmaild::robotGet()
--SKIPIF--
<?php
$string = array();
if (!is_readable('tests-setpath.php')) {
    $string[] = 'tests-setpath.php not readable';
}
if (!is_readable('tests-config.php')) {
    $string[] = 'tests-config.php not readable';
}
if (!empty($string)) {
    $dir      = dirname(__FILE__);
    $fullname = __FILE__;
    $file     = ereg_replace($dir . '/(.*).skip.php', '\1', $fullname);
    print "skip $file: " . implode(', ', $string) . "\n";
}
?>
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
