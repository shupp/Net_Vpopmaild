--TEST--
Net_Vpopmaild::status*
--FILE--
<?php
require_once('tests-setpath.php');
require_once('Net/Vpopmaild.php');

class TestClass extends Net_Vpopmaild
{
    public function statusFunctions()
    {
        var_dump($this->statusOK('+OK+'));
        var_dump($this->statusOKMore('+OK+'));
        var_dump($this->statusOKNoMore('+OK'));
        var_dump($this->statusErr('-ERR Some Error Message'));
        var_dump($this->dotOnly('.'));
    }
}
$class = 'TestClass';

require_once('tests-config.php');


$vp->statusFunctions();
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
