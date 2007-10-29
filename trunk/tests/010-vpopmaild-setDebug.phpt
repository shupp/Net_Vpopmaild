--TEST--
Net_Vpopmaild::setDebug()
--FILE--
<?php
require_once('tests-setpath.php');
require_once('Net/Vpopmaild.php');

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
$vp->setDebug(0);
var_dump($vp->getDebug());
?>
--EXPECT--
int(1)
int(0)
