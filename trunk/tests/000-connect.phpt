--TEST--
Net_Vpopmaild::connect()/__construct()/__destruct()/quit()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo "Error connecting to vpopmaild\n";
}
?>
--EXPECT--
