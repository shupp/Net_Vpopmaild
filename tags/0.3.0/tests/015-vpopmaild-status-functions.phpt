--TEST--
Net_Vpopmaild::status*
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

class TestClass extends Net_Vpopmaild
{
    public function statusFunctions()
    {
        var_dump($this->statusOK('+OK+'));
        var_dump($this->statusOKMore('+OK+'));
        var_dump($this->statusOKNoMore('+OK'));
        var_dump($this->statusErr('-ERR Some Error Message'));
        var_dump($this->dotOnly('.'));
    }
}
$class = 'TestClass';

require_once('tests-config.php');


$vp->statusFunctions();
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
