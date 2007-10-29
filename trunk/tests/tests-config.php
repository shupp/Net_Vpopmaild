<?php

// SETTINGS
require_once 'tests-setpath.php';
$vpopmaildHost = 'localhost';
$logFile = '/tmp/vpopmaild.log';

// Sysadmin user info
$user = 'test';
$domain = 'test.com';
$sysadminEmail = $user. '@' . $domain;
$sysadminPass = 'test';

// Total number of domains which domainCount will return
$domainCount = 2501;

// Total number of users in $domain which domainCount will return
$userCount = 2;

// Existing IP map should be EMPTY
// Domains test.com and test2.com need to exist
$ip1 = "1.2.3.4";
$domain1 = "test.com";
$ip2 = "2.3.4.5";
$domain2 = "test2.com";

$nonExistentDomain = 'nonexistentdomain.com';
$nonExistentUser = 'bogususer';

// Aliases
$aliasUser = 'testalias';
$alias = $aliasUser . '@' . $domain;
$aliasDestination1 = 'user@remoteexample.com';
$aliasDestination2 = 'user2@remoteexample.com';

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

// Vacation
$vacationSubject = 'On Vcation';
$vacationMessage = 'Do Not Bother Me!';


// Init for tests
require_once 'Net/Vpopmaild.php';
$class = isset($class) ? $class : 'Net_Vpopmaild';
$vp = new $class;
$vp->address = $vpopmaildHost;
$vp->timeout = 5;
$vp->logFile = $logFile;
$vp->setDebug();

?>
