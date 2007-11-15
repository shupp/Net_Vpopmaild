--TEST--
Net_Vpopmaild::formatBasePath()
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

require_once 'tests-setpath.php';
require_once 'Net/Vpopmaild.php';

class TestClass extends Net_Vpopmaild
{
    public function test($domain, $user, $path, $type)
    {
        return $this->formatBasePath($domain, $user, $path, $type);
    }
}

$class = 'TestClass';
require_once('tests-config.php');

$vp->clogin($sysadminEmail, $sysadminPass);
var_dump($vp->test($domain, $user, '', 'file') == "$user@$domain");
var_dump($vp->test($domain, $user, 'test', 'file') == "$user@$domain/test");
var_dump($vp->test($domain, $user, 'test', 'dir') == "$user@$domain/test/");
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
