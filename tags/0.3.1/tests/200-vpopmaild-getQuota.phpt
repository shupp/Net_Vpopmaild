--TEST--
Net_Vpopmaild::getQuota()
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
