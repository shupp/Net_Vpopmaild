--TEST--
Net_Vpopmaild::paginateArray()
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
