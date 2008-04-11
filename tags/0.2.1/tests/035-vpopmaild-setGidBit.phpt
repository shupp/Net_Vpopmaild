--TEST--
Net_Vpopmaild::setGidBit()
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
$vp->setGidBit($vp->loginUser['gidflags'], 'no_imap', true);
var_dump($vp->getGidBit($vp->loginUser['gidflags'], 'no_imap'));
?>
--EXPECT--
bool(true)
