<?php

// SETTINGS
ini_set('include_path', '.:/Users/shupp/pear/lib');
$vpopmaildHost = '192.168.1.1';
$logFile = '/tmp/billshupp.log';
$sysadminEmail = 'test@test.com';
$sysadminPass = 'test';
$domainCount = 2005;



require_once 'Net/Vpopmaild.php';
$vp = new Net_Vpopmaild;
$vp->address = $vpopmaildHost;
$vp->timeout = 5;
$vp->logFile = $logFile;

?>
