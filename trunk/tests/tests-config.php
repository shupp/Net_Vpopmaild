<?php

// SETTINGS
ini_set('include_path', '.:/Users/shupp/pear/lib');
$vpopmaildHost = 'localhost';
$logFile = '/tmp/billshupp.log';
$sysadminEmail = 'test@test.com';
$sysadminPass = 'test';
$domainCount = 2001;
// Existing IP map should be EMPTY
// Domains test.com and test2.com need to exist
$ip1 = "1.2.3.4";
$domain1 = "test.com";
$ip2 = "2.3.4.5";
$domain2 = "test2.com";

// Sample dot qmail file
$dotQmailFile = array(
    'Program' => '|/usr/local/autorespond',
    'Comment' => '# This is a comment',
    'Forward' => '&someforward@remotehost.com',
    'Delivery' => '/path/to/Maildir/'
);

require_once 'Net/Vpopmaild.php';
$vp = new Net_Vpopmaild;
$vp->address = $vpopmaildHost;
$vp->timeout = 5;
$vp->logFile = $logFile;

?>
