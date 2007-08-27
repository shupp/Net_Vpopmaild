<?php

ini_set('include_path', '.:/Users/shupp/pear/lib');

require_once 'Net/Vpopmaild.php';
$vpopmaildHost = '192.168.1.1';
$vp = new Net_Vpopmaild;
$vp->address = $vpopmaildHost;
$vp->timeout = 5;

$sysadminEmail = 'test@test.com';
$sysadminPass = 'test';
?>
