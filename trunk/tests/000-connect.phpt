--TEST--
Net_Vpopmaild::connect()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo "Error connection to vpopmaild\n";
}
?>
--EXPECT--
