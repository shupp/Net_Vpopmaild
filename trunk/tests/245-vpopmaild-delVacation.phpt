--TEST--
Net_Vpopmaild::delVacation()
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

require_once 'tests-config.php';

$vp->clogin($sysadminEmail, $sysadminPass);
$vp->setVacation($user, $domain, $vacationSubject, $vacationMessage);
var_dump($vp->delVacation($user, $domain));
var_dump($vp->getVacation($vp->loginUser));
?>
--EXPECT--
bool(true)
NULL
