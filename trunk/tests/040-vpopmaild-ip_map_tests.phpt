--TEST--
Net_Vpopmaild::addIPMap(), getIPMap(), delIPMap(), showIPMap()
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
var_dump($vp->addIPMap($ip1, $domain1));
var_dump($vp->addIPMap($ip2, $domain2));
var_dump($vp->showIPMap() == array($ip1 => $domain1, $ip2 => $domain2));
var_dump( $vp->getIPMap($ip1) == $domain1);
var_dump( $vp->getIPMap($ip2) == $domain2);
var_dump($vp->delIPMap($ip1, $domain1));
var_dump($vp->delIPMap($ip2, $domain2));
var_dump( $vp->getIPMap($ip1));
var_dump( $vp->getIPMap($ip2));
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
NULL
NULL
