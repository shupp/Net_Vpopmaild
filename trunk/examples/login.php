<?php

/*
 * A simple login example
 */


require_once('Net/Vpopmaild.php');
$vpop = new Net_Vpopmaild();

try {
    // The host, port, and timeout below are the defaults and can be omitted
    $vpop->connect('localhost', '89', '30');
} catch (Net_Vpopmaild_FatalException $e) {
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
