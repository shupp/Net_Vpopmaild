--TEST--
Net_Vpopmaild::acceptLog(), recordio()
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

// Create dummy class
if (file_exists($logFile)) {
    unlink($logFile);
}
class TestLog extends Log
{
}
$testLog = TestLog::factory('file', $logFile);
$vp->acceptLog($testLog);
// Turn on debugging
$vp->setDebug();
$vp->recordio("testing log file");
var_dump(file_exists($logFile));
var_dump(preg_match('/testing log file/', file_get_contents($logFile)));
?>
--CLEAN--
<?php
require_once('tests-config.php');
if (file_exists($logFile)) {
    unlink($logFile);
}
?>
--EXPECT--
bool(true)
int(1)
