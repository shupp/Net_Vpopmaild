--TEST--
Net_Vpopmaild::setDebug()
--FILE--
<?php
require_once('tests-setpath.php');
require_once('Net/Vpopmaild.php');

$skipConnect = true;

class TestClass extends Net_Vpopmaild
{
    public function getDebug()
    {
        return $this->debug;
    }
}
$class = 'TestClass';

require_once('tests-config.php');


$vp->setDebug();
var_dump($vp->getDebug());
$vp->setDebug(false);
var_dump($vp->getDebug());
?>
--EXPECT--
bool(true)
bool(false)
