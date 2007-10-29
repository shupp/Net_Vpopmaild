--TEST--
Net_Vpopmaild::aliasesToArray()
--FILE--
<?php

require_once 'tests-setpath.php';
require_once 'Net/Vpopmaild.php';

class TestClass extends Net_Vpopmaild
{
    public function test($array)
    {
        return $this->aliasesToArray($array);
    }
}
$class = 'TestClass';

require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}

// clogin uses readInfo to populate $this->loginUser
$vp->clogin($sysadminEmail, $sysadminPass);
$array[] = "$alias $aliasDestination1";
$array[] = "$alias $aliasDestination2";
$result = $vp->test($array);

var_dump(array_key_exists($alias, $result));
var_dump(in_array($aliasDestination1, $result[$alias]));
var_dump(in_array($aliasDestination2, $result[$alias]));
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
