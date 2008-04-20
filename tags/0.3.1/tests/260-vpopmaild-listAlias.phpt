--TEST--
Net_Vpopmaild::listAlias()
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
    $vp->deleteAlias($alias);
} catch (Net_Vpopmaild_Exception $e) {
}
$vp->addAlias($alias, $aliasDestination1);
$vp->addAlias($alias, $aliasDestination2);
$result = $vp->listAlias($domain, $aliasUser);

var_dump(array_key_exists($alias, $result));
var_dump(in_array($aliasDestination1, $result[$alias]));
var_dump(in_array($aliasDestination2, $result[$alias]));

$vp->deleteAlias($alias);
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
