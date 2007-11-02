--TEST--
Net_Vpopmaild::connect()/__construct()/__destruct()/quit()
--FILE--
<?php
$skipConnect = true;
require_once('tests-config.php');
try {
    $vp->connect($vpopmaildHost, $vpopmaildPort, $vpopmaildTimeout);
} catch (Net_Vpopmaild_Exception $e) {
    echo "Error connecting to vpopmaild\n";
}
?>
--EXPECT--
