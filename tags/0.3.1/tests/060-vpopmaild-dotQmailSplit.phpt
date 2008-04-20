--TEST--
Net_Vpopmaild::dotQmailSplit()
--SKIPIF--
<?php
$string = array();
if (!is_readable('tests-setpath.php')) {
    $string[] = 'tests-setpath.php not readable';
}
if (!is_readable('tests-config.php')) {
    $string[] = 'tests-config.php not readable';
}
if (!empty($string)) {
    $dir      = dirname(__FILE__);
    $fullname = __FILE__;
    $file     = ereg_replace($dir . '/(.*).skip.php', '\1', $fullname);
    print "skip $file: " . implode(', ', $string) . "\n";
}
?>
--FILE--
<?php
require_once('tests-config.php');
$vp->clogin($sysadminEmail, $sysadminPass);
$array = $vp->dotQmailSplit($dotQmailFile);
var_dump($array['Forward'][0] == $dotQmailFile['Forward']);
var_dump($array['Delivery'][0] == $dotQmailFile['Delivery']);
var_dump($array['Comment'][0] == $dotQmailFile['Comment']);
var_dump($array['Program'][0] == $dotQmailFile['Program']);
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
