<?php

// SETTINGS
ini_set('include_path', '.:/Users/shupp/pear/lib');
$vpopmaildHost = 'localhost';
$logFile = '/tmp/vpopmaild.log';

// Sysadmin user info
$user = 'test';
$domain = 'test.com';
$sysadminEmail = $user. '@' . $domain;
$sysadminPass = 'test';

// Total number of domains which domainCount will return
$domainCount = 2501;

// Existing IP map should be EMPTY
// Domains test.com and test2.com need to exist
$ip1 = "1.2.3.4";
$domain1 = "test.com";
$ip2 = "2.3.4.5";
$domain2 = "test2.com";

// testing robots
$robot   = 'robot';
$subject = 'Test Subject';
$forward = 'forward@example.com';
$message = 'out of the office';

$testFile = 'TESTFILE';
$testFileContents = array('CONTENTS');

// Sample dot qmail file
$dotQmailFile = array(
    'Program' => '|/usr/local/autorespond',
    'Comment' => '# This is a comment',
    'Forward' => '&someforward@remotehost.com',
    'Delivery' => '/path/to/Maildir/'
);

// Sample home .qmail files
$homeDotQmailFileDelete = array(
    '# delete',
);


// Init for tests
require_once 'Net/Vpopmaild.php';
$vp = new Net_Vpopmaild;
$vp->address = $vpopmaildHost;
$vp->timeout = 5;
$vp->logFile = $logFile;

?>
