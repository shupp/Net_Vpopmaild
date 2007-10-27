--TEST--
Net_Vpopmaild::userInfo()
--FILE--
<?php
require_once('tests-config.php');
try {
    $vp->connect();
} catch (Net_Vpopmaild_Exception $e) {
    echo $e->getMessage();
}
$vp->clogin($sysadminEmail, $sysadminPass);

// userInfo()
$info = $vp->userInfo($domain, $user);
var_dump(!empty($info['gidflags']));
var_dump(!empty($info['clear_text_password'])
    && $info['clear_text_password'] == $sysadminPass);

?>
--EXPECT--
bool(true)
bool(true)
