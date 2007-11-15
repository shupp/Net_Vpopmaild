--TEST--
Net_Vpopmaild::paginateArray()
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

$array = array(
            'one'   => 'one',
            'two'   => 'two',
            'three' => 'three',
            'four'  => 'four',
            'five'  => 'five');

$paginated = $vp->paginateArray($array, 3, 1);
var_dump($paginated['three'] == 'three');

?>
--EXPECT--
bool(true)
