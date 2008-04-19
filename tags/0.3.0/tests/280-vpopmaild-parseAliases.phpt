--TEST--
Net_Vpopmaild::parseAliases()
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
try {
    $vp->robotSet($domain, $robot, $subject, $message, $forward);
} catch (Net_Vpopmaild_Exception $e) {
}
try {
    $vp->deleteAlias($alias);
} catch (Net_Vpopmaild_Exception $e) {
}
$vp->addAlias($alias, $aliasDestination1);
$vp->addAlias($alias, $aliasDestination2);
$aliases = $vp->listAlias($domain);
$array = $vp->parseAliases($aliases, 'responders');
var_dump(array_key_exists($robot . '@' . $domain, $array));
var_dump(array_key_exists($alias, $array));
$array = $vp->parseAliases($aliases, 'forwards');
var_dump(array_key_exists($alias, $array));
$vp->deleteAlias($alias);
$vp->robotDel($domain, $robot);

?>
--EXPECT--
bool(true)
bool(false)
bool(true)
