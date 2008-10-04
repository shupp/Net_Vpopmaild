--TEST--
Net_Vpopmaild::setDebug()
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
require_once('tests-setpath.php');
require_once('Net/Vpopmaild.php');

$skipConnect = true;

class TestClass extends Net_Vpopmaild
{
    public function getDebug()
    {
        return $this->debug;
    }
}
$class = 'TestClass';

require_once('tests-config.php');


$vp->setDebug();
var_dump($vp->getDebug());
$vp->setDebug(false);
var_dump($vp->getDebug());
?>
--EXPECT--
bool(true)
bool(false)
