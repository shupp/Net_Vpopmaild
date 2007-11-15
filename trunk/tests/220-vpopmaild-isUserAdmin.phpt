--TEST--
Net_Vpopmaild::isUserAdmin()
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

// Create temp user
$vp->authenticate($sysadminEmail, $sysadminPass);
$vp->addUser($domain, $nonExistentUser, 'test');
$vp->quit();

// Login as that user to test isUserAdmin
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}
$vp->authenticate($nonExistentUser . '@' . $domain, 'test');
var_dump($vp->isUserAdmin($user, $domain));
var_dump($vp->isUserAdmin($nonExistentUser, $domain));
$vp->quit();

// Login as sysadmin user to delete test user
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}
$vp->authenticate($sysadminEmail, $sysadminPass);
$vp->delUser($domain, $nonExistentUser);
$vp->quit();
?>
--EXPECT--
bool(false)
bool(true)
