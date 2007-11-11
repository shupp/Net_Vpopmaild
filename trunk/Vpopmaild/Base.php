<?php

/**
 * Net_Vpopmaild_Base
 * 
 * @package Net_Vpopmaild
 * @category Net
 * @author Bill Shupp <hostmaster@shupp.org> 
 * @author Rick Widmer
 * @license PHP 3.01  {@link http://www.php.net/license/3_01.txt}
 * @todo Finish ezmlm functions, waiting on vpopmaild updates
 * @todo Do not rely on PHP4 packages
 * @todo Robot creation - check for existing accounts first?  or 
 * is it an issue with OS X fs, or vpopmaild?
 * @todo allow readInfo() to support mutlitple items/arrays for listUsers()
 * @todo getQuota() should support maildir++ completely (file count: C)
 */

require_once('Net/Socket.php');
require_once('Log.php');
require_once('Net/Vpopmaild/Exception.php');
require_once('Net/Vpopmaild/FatalException.php');

/**
 * Net_Vpopmaild_Base
 * 
 * A class for talking to vpopmaild
 * 
 * @package Net_Vpopmaild
 * @category Net
 * @author Bill Shupp <hostmaster@shupp.org> 
 * @author Rick Widmer
 * @license PHP 3.01  {@link http://www.php.net/license/3_01.txt}
 */
class Net_Vpopmaild_Base {

    private $socket = null;
    /**
     * debug 
     * 
     * Set to true to enable logging.  Can be set by {@link setDebug()}
     * 
     * @var bool
     * @access private
     * @see function setDebug
     */
    protected $debug = false;
    /**
     * loginUser 
     * 
     * This is an array of the logged in user's info.
     * 
     * @var mixed
     * @access public
     */
    public $loginUser = null;
    /**
     * log 
     * 
     * instance of PEAR Log
     * 
     * @var mixed
     * @access private
     */
    private $log = null;
    /**
     * gidFlagValues 
     * 
     * gid big values for account limits
     * 
     * @var array
     * @access private
     */
    protected $gidFlagValues = array(
        'no_password_change'        => 0x01, 
        'no_pop'                    => 0x02, 
        'no_webmail'                => 0x04, 
        'no_imap'                   => 0x08, 
        'bounce_mail'               => 0x10, 
        'no_relay'                  => 0x20, 
        'no_dialup'                 => 0x40, 
        'user_flag_0'               => 0x080, 
        'user_flag_1'               => 0x100, 
        'user_flag_2'               => 0x200, 
        'user_flag_3'               => 0x400, 
        'no_smtp'                   => 0x800, 
        'domain_admin_privileges'   => 0x1000, 
        'override_domain_limits'    => 0x2000, 
        'no_spamassassin'           => 0x4000, 
        'delete_spam'               => 0x8000, 
        'system_admin_privileges'   => 0x10000, 
        'system_expert_privileges'  => 0x20000, 
        'no_maildrop'               => 0x40000);

    /**
     * vpopmailRobotProgram 
     * 
     * path to autorespond
     * 
     * @var string
     * @access public
     */
    public $vpopmailRobotProgram = '/usr/bin/autorespond';
    /**
     * vpopmailRobotTime 
     * 
     * autorespond time argument
     * 
     * @var int
     * @access public
     */
    public $vpopmailRobotTime = 1000;
    /**
     * vpopmailRobotNumber 
     * 
     * autorespond number argument
     * 
     * @var int
     * @access public
     */
    public $vpopmailRobotNumber = 3;

    /**
     * ezmlmOpts 
     * 
     * This will be an array of the default ezmlm command 
     * line options. Use 1 for "on" or "yes"
     * 
     * @var mixed
     * @access private
     */
    protected $ezmlmOpts = array(
            'a' => 1, /* Archive */
            'b' => 1, /* Moderator-only access to archive */
            'c' => 0, /* ignored */
            'd' => 0, /* Digest */
            'e' => 0, /* ignored */
            'f' => 1, /* Prefix */
            'g' => 1, /* Guard Archive */
            'h' => 0, /* Subscribe doesn't require conf */
            'i' => 0, /* Indexed */
            'j' => 0, /* Unsubscribe doesn't require conf */
            'k' => 0, /* Create a blocked sender list */
            'l' => 0, /* Remote admins can access subscriber list */
            'm' => 0, /* Moderated */
            'n' => 0, /* Remote admins can edit text files */
            'o' => 0, /* Others rejected (for Moderated lists only */
            'p' => 1, /* Public */
            'q' => 1, /* Service listname-request */
            'r' => 0, /* Remote Administration */
            's' => 0, /* Subscriptions are moderated */
            't' => 0, /* Add Trailer to outgoing messages */
            'u' => 1, /* Only subscribers can post */
            'v' => 0, /* ignored */
            'w' => 0, /* special ezmlm-warn handling (ignored) */
            'x' => 0, /* enable some extras (ignored) */
            'y' => 0, /* ignored */
            'z' => 0);/* ignored */

    /**
     * connected 
     * 
     * set to true only when connected.  This is used only by __destruct()
     * 
     * @var bool
     * @see __destruct()
     * @access private
     */
    private $connected = false;

    /**
     * __construct 
     * 
     * Create socket.
     * 
     * @access public
     * @return void
     */
    public function  __construct()
    {
        $this->socket = new Net_Socket();
    }

    /**
     * setDebug 
     * 
     * Set {@link $debug} (1 by default).
     * Call this to set {@link $debug} to 1 and enable logging.
     * 
     * @param bool $value 
     * @param string $logFile path to local log file, defaults to '/tmp/vpopmaild.log'
     * @access public
     * @return void
     */
    public function setDebug($value = true, $logFile = '/tmp/vpopmaild.log')
    {
        // Set debug value
        $this->debug = (bool)$value;
        // Instantiate Log object if necessary
        if ($this->debug && is_null($this->log)) {
            $this->log = Log::factory('file', $logFile);
            if (is_null($this->log)) {
                throw new Net_Vpopmaild_Exception("Error creating Log object");
            }
        }
    }

    /**
     * acceptLog
     * 
     * Assign {@link $log} an external instance of Log
     * 
     * @var object $log 
     * @access public
     * @return void
     */
    public function acceptLog(&$log)
    {
        if ($log instanceof Log) {
            $this->log = & $log;
        }
    }

    /**
     * connect 
     * 
     * Make a connection to vpopmaild
     * 
     * @param string $address 
     * @param int $port 
     * @param int $timeout 
     * @access public
     * @throws Net_Vpopmaild_FatalException if connection or initial status fails
     * @return void
     */
    public function connect($address = 'localhost', $port = 89, $timeout = 30)
    {
        $result = $this->socket->connect($address, $port, null, $timeout);
        if (PEAR::isError($result)) {
            throw new Net_Vpopmaild_FatalException($result);
        }
        $this->connected = true;
        $in = $this->sockRead();
        if (!$this->statusOk($in)) {
            throw new Net_Vpopmaild_Exception("Error: initial status: $in");
        }
    }
    

    /**
     * recordio 
     * 
     * Record i/o to {@link $log}
     * 
     * @param string $data 
     * @access public
     * @return void
     */
    public function recordio($data)
    {
        if ($this->debug) {
            $this->log->log($data);
        }
    }

    /**
     * statusOk 
     * 
     *  $data contains +OK
     * 
     * @param string $data 
     * @access private
     * @return true on success, false on failure
     */
    protected function statusOk($data)
    {
        if (preg_match('/^[+]OK/', $data)) {
            return true;
        }
        return false;
    }

    /**
     * statusOkMore 
     * 
     *  $data is is exactly +OK+
     *  (more to come)
     * 
     * @param string $data 
     * @access private
     * @return true on success, false on failure
     */
    protected function statusOkMore($data)
    {
        if (preg_match('/^[+]OK[+]$/', $data)) {
            return true;
        }
        return false;
    }

    /**
     * statusOkNoMore 
     * 
     * $data is exactly +OK
     * 
     * @param string $data 
     * @access private
     * @return true on success, false on failure
     */
    protected function statusOkNoMore($data)
    {
        if (preg_match('/^[+]OK$/', $data)) {
            return true;
        }
        return false;
    }

    /**
     * statusErr 
     * 
     * $data starts with "-ERR "
     * 
     * @param string $data 
     * @access private
     * @return true on success, false on failure
     */
    protected function statusErr($data)
    {
        if (preg_match('/^[-]ERR /', $data)) {
            return true;
        }
        return false;
    }

    /**
     * dotOnly 
     * 
     * $data is exactly "."
     * 
     * @param string $data 
     * @access private
     * @return true on success, false on failure
     */
    protected function dotOnly($data)
    {
        if (preg_match('/^[.]$/', $data)) {
            return true;
        }
        return false;
    }

    /**
     * sockWrite 
     * 
     * Write $data to socket
     * 
     * @param mixed $data 
     * @access private
     * @return mixed
     * @throws Net_Vpopmaild_FatalException if Net_Socket::writeLine() returns PEAR_Error
     */
    protected function sockWrite($data)
    {
        $this->recordio("sockWrite send: $data");
        $result = $this->socket->writeLine($data);
        if (PEAR::isError($result)) {
            throw new Net_Vpopmaild_FatalException($result);
        }
        return true;
    }

    /**
     * sockRead 
     * 
     * Read line from socket
     * 
     * @access private
     * @return string line
     * @throws Net_Vpopmaild_FatalException if Net_Socket::readLine() returns PEAR_Error
     */
    protected function sockRead()
    {
        $in = $this->socket->readLine();
        $this->recordio("sockRead Read: $in");
        if (PEAR::isError($in)) {
            throw new Net_Vpopmaild_FatalException($in);
        }
        return $in;
    }

    /**
     * quit 
     * 
     * send quit command to vpopmaild.
     * Called by {@link __destruct()}
     * 
     * 
     * @access public
     * @return void
     */
    public function quit()
    {
        $this->sockWrite("quit\n");
    }


    /**
     * formatBasePath 
     * 
     * @param mixed $domain 
     * @param string $user 
     * @param string $path 
     * @param string $type 
     * @access private
     * @return var $basePath
     */
    protected function formatBasePath($domain, $user = '', $path = '', $type = 'file')
    {
        $basePath = $domain;
        if (!empty($user)) {
            $basePath  = "$user@$basePath";
        }
        if (!empty($path)) {
            $basePath .= "/" . $path;
        }
        if ($type == 'dir') {
            $basePath.= '/';
        }
        $basePath = preg_replace('/\/\//', '/', $basePath);
        return $basePath;
    }
    /**
     * readInfo 
     * 
     * Collect user/dom info into an Array and return.
     * NOTE:  +OK has already been read.
     * 
     * @access private
     * @return mixed info array
     */
    protected function readInfo()
    {
        $this->recordio("<<--  Start readInfo  -->>");
        $infoArray = array();
        $in = $this->sockRead();
        while (!$this->dotOnly($in) && !$this->statusOk($in) && !$this->statusErr($in)) {
            $this->recordio("<<--  Start readInfo  -->>");
            if ($in != '') {
                unset($value);
                list($name, $value) = explode(' ', $in, 2);
                $value = trim($value);
                $infoArray[$name] = $value;
            }
            $in = $this->sockRead();
        }
        $this->recordio("readInfo collected: ");
        $this->recordio(print_r($infoArray, 1));
        $this->recordio("<<--  Finish readInfo  -->>");
        return $infoArray;
    }

    /**
     * rmFile 
     * 
     * @param mixed $domain 
     * @param string $user 
     * @param string $path 
     * @access public
     * @throws Net_Vpopmaild_Exception on failure
     * @return mixed true on success
     */
    public function rmFile($domain, $user = '', $path = '')
    {
        $basePath = $this->formatBasePath($domain, $user, $path);
        $status = $this->sockWrite("rm_file $basePath");
        $status = $this->sockRead();
        if (!$this->statusOk($status)) {
            throw new Net_Vpopmaild_Exception($status);
        }
        return true;
    }

    /**
     * writeFile 
     * 
     * @param mixed $contents 
     * @param mixed $domain 
     * @param string $user 
     * @param string $path 
     * @access public
     * @throws Net_Vpopmaild_Exception on failure
     * @return true on success
     */
    public function writeFile($contents, $domain, $user = '', $path = '')
    {
        if (!is_array($contents)) {
            throw new Net_Vpopmaild_Exception('$contents argument must be an array');
        }
        $basePath = $this->formatBasePath($domain, $user, $path);
        $status = $this->sockWrite("write_file $basePath");
        reset($contents);
        while (list(, $line) = each($contents)) {
            $status = $this->sockWrite($line);
        }
        $status = $this->sockWrite(".");
        $status = $this->sockRead();
        if (!$this->statusOk($status)) {
            throw new Net_Vpopmaild_Exception($status);
        }
        return true;
    }

    /**
     * readFile 
     * 
     * @param mixed $domain 
     * @param string $user 
     * @param string $path 
     * @access public
     * @throws Net_Vpopmaild_Exception on failure
     * @return mixed file contents as array on success
     */
    public function readFile($domain, $user = '', $path = '')
    {
        $basePath = $this->formatBasePath($domain, $user, $path);
        $status = $this->sockWrite("read_file $basePath");
        $status = $this->sockRead();
        if (!$this->statusOk($status)) {
            throw new Net_Vpopmaild_Exception($status);
        }
        $fileContents = array();
        $in = $this->sockRead();
        while (!$this->dotOnly($in) && !$this->statusOk($in) && !$this->statusErr($in)) {
            $fileContents[] = $in;
            $in = $this->sockRead();
        }
        return $fileContents;
    }

    /**
     * listDir 
     * 
     * @param mixed $domain 
     * @param string $user 
     * @param string $path 
     * @access public
     * @throws Net_Vpopmaild_Exception on failure
     * @return array of directory contents on success
     */
    public function listDir($domain, $user = '', $path = '')
    {
        $basePath = $this->formatBasePath($domain, $user, $path, 'dir');
        $status = $this->sockWrite("list_dir $basePath");
        $status = $this->sockRead();
        if (!$this->statusOk($status)) {
            throw new Net_Vpopmaild_Exception($status);
        }
        $directoryContents = array();
        $in = $this->sockRead();
        while (!$this->dotOnly($in) && !$this->statusOk($in) && !$this->statusErr($in)) {
            list($dirName, $type) = explode(' ', $in);
            $directoryContents[$dirName] = $type;
            $in = $this->sockRead();
        }
        ksort($directoryContents);
        return $directoryContents;
    }
    /**
     * rmDir 
     * 
     * @param mixed $domain 
     * @param string $user 
     * @param string $path 
     * @access public
     * @throws new Net_Vpopmaild_Excpetion on failure
     * @return bool true on success
     */
    public function rmDir($domain, $user = '', $path = '')
    {
        $basePath = $this->formatBasePath($domain, $user, $path);
        $status = $this->sockWrite("rm_dir $basePath");
        $status = $this->sockRead();
        if (!$this->statusOk($status)) {
            throw new Net_Vpopmaild_Exception($status);
        }
        return true;
    }


    /**
     * mkDir 
     * 
     * @param mixed $domain 
     * @param string $user 
     * @param string $path 
     * @access public
     * @throws Net_Vpopmaild_Exception on failure
     * @return mixed true on success
     */
    public function mkDir($domain, $user = '', $path = '')
    {
        $basePath = $this->formatBasePath($domain, $user, $path, 'dir');
        $status = $this->sockWrite("mk_dir $basePath");
        $status = $this->sockRead();
        if (!$this->statusOk($status)) {
            throw new Net_Vpopmaild_Exception($status);
        }
        return true;
    }

    /**
     * __destruct 
     * 
     * Send {@link quit()}, Close {@link $socket}.
     * 
     * @access public
     * @return void
     */
    public function __destruct()
    {
        if ($this->connected) {
            $this->quit();
            $this->socket->disconnect();
        }
    }

}
?>