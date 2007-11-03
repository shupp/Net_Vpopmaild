<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Net_Vpopmaild_Exception 
 * 
 * @uses        PEAR_Exception
 * @package     Net_Vpopmaild
 * @category    Net
 */

/**
 *  require_once 'PEAR/Exception.php';
 */
require_once 'PEAR/Exception.php';

/**
 * Net_Vpopmaild_Exception
 *
 * A small layer above 
 * {@link http://pear.php.net/manual/en/core.pear.pear-exception.php PEAR_Exception} with which you can send vpopmaild error message strings
 *
 * @author      Bill Shupp <hostmaster@shupp.org>
 * @package     Net_Vpopmaild
 */
class Net_Vpopmaild_Exception extends PEAR_Exception
{
    /**
     * __construct
     *
     * @access public
     * @param  mixed $message        message or -ERR message from vpopmaild
     * @param  int   $code           An error code
     * @return void
     */
    public function __construct($message, $code = 0)
    {

        if (preg_match('/^[-]ERR /', $message)) {
            $code = preg_replace('/^-ERR ([^ ]+) .*$/', '\1', $message);
            $msg = preg_replace('/^-ERR ([^ ]+) (.*$)/', '\2', $message);
        }

        $this->message  = $msg;
        $this->code     = $code;
    }
}

?>
