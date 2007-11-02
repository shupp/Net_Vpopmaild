--TEST--
Net_Vpopmaild::acceptLog(), recordio()
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
