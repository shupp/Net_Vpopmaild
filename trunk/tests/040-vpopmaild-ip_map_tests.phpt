--TEST--
Net_Vpopmaild::addIPMap(), getIPMap(), delIPMap(), showIPMap()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo "Error connecting to vpopmaild\n";
}
$vp->clogin($sysadminEmail, $sysadminPass);
var_dump($vp->addIPMap($ip1, $domain1));
var_dump($vp->addIPMap($ip2, $domain2));
var_dump($vp->showIPMap() == array($ip1 => $domain1, $ip2 => $domain2));
var_dump( $vp->getIPMap($ip1) == $domain1);
var_dump( $vp->getIPMap($ip2) == $domain2);
var_dump($vp->delIPMap($ip1, $domain1));
var_dump($vp->delIPMap($ip2, $domain2));
var_dump( $vp->getIPMap($ip1));
var_dump( $vp->getIPMap($ip2));
?>
--EXPECT--
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
bool(true)
NULL
NULL
