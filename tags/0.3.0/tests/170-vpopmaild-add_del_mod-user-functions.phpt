--TEST--
Net_Vpopmaild::addUser(), modUser(), delUser()
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
// Remove first just to be sure
try {
    $result = $vp->delUser($domain, $nonExistentUser);
} catch (Net_Vpopmaild_Exception $e) {
}

// addUser()
var_dump($vp->addUser($domain, $nonExistentUser, 'test'));

// modUser()
$info = $vp->userInfo($domain, $nonExistentUser);
$vp->setGidBit($info['gidflags'], 'no_webmail', true);
var_dump($vp->modUser($domain, $nonExistentUser, $info));

// verify modUser()
$info = $vp->userInfo($domain, $nonExistentUser);
var_dump($vp->getGidBit($info['gidflags'], 'no_webmail') == true);

// delUser()
var_dump($vp->delUser($domain, $nonExistentUser));
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
