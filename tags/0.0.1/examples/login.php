<?php

/*
 * A simple login example
 */


require_once('Net/Vpopmaild.php');
$vpop = new Net_Vpopmaild();

// Let's customize the host/port
$vpop->address = '192.168.1.1';
$vpop->port = 589;
try {
    $vpop->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo 'Error connecting to vpopmaild: ' . $e->getMessage();
    exit;
}

// If we get here, authenticate
if (!$vpop->authenticate('user@example.com', 'password')) {
    echo "Error: login failed";
    exit;
}

// Display user info
print_r($vpop->loginUser);
exit;

?>
