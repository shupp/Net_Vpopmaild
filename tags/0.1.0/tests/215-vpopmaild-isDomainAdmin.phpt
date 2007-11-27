--TEST--
Net_Vpopmaild::isDomainAdmin()
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

try {
    $vp->delUser($domain, $nonExistentUser);
} catch (Net_Vpopmaild_Exception $e) {
}

$vp->addUser($domain, $nonExistentUser, 'test');
$info = $vp->userInfo($domain, $nonExistentUser);
var_dump($vp->isDomainAdmin($domain, $info));
$vp->setGidBit($info['gidflags'], 'system_admin_privileges', true);
var_dump($vp->modUser($domain, $nonExistentUser, $info));
$info = $vp->userInfo($domain, $nonExistentUser);
var_dump($vp->isDomainAdmin($domain, $info));
$vp->delUser($domain, $nonExistentUser);
?>
--EXPECT--
bool(false)
bool(true)
bool(true)
