--TEST--
Net_Vpopmaild::accept(), recordio()
--FILE--
<?php
require_once('tests-config.php');

// Create dummy class
if (file_exists($vp->logFile)) {
    unlink($vp->logFile);
}
class TestLog extends Log
{
}
$testLog = TestLog::factory('file', $vp->logFile);
$vp->accept($testLog);
// Turn on debugging
$vp->setDebug();
$vp->recordio("testing log file");
var_dump(file_exists($vp->logFile));
var_dump(preg_match('/testing log file/', file_get_contents($vp->logFile)));
?>
--CLEAN--
<?php
require_once('tests-config.php');
if (file_exists($vp->logFile)) {
    unlink($vp->logFile);
}
?>
--EXPECT--
bool(true)
int(1)
