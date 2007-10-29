--TEST--
Net_Vpopmaild::formatBasePath()
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
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}

$vp->clogin($sysadminEmail, $sysadminPass);
var_dump($vp->test($domain, $user, '', 'file') == "$user@$domain");
var_dump($vp->test($domain, $user, 'test', 'file') == "$user@$domain/test");
var_dump($vp->test($domain, $user, 'test', 'dir') == "$user@$domain/test/");
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
