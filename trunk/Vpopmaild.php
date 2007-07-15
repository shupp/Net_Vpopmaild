<?php

/**
 * Net_Vpopmaild 
 * 
 * @package Net_Vpopmaild
 * @category Net
 * @author Bill Shupp <hostmaster@shupp.org> 
 * @author Rick Widmer
 * @license PHP 3.01  {@link http://www.php.net/license/3_01.txt}
 */

require_once('Net/Socket.php');
require_once('Log.php');
require_once('Validate.php');
require_once('Net/Vpopmaild/Exception.php');

/**
 * Net_Vpopmaild 
 * 
 * A class for talking to vpopmaild
 * 
 * @package Net_Vpopmaild
 * @category Net
 * @author Bill Shupp <hostmaster@shupp.org> 
 * @author Rick Widmer
 * @license PHP 3.01  {@link http://www.php.net/license/3_01.txt}
 */
class Net_Vpopmaild {

    /**
     * address 
     * 
     * Address of vpopmaild host
     * 
     * @var mixed
     * @access public
     */
    public $address = 'localhost';
    /**
     * port 
     * 
     * port of vpopmaild host (deaults to 89)
     * 
     * @var mixed
     * @access public
     */
    public $port = 89;
    /**
     * Socket 
     * 
     * Actual socket from Net_Socket
     * 
     * @var mixed
     * @access public
     */
    public $socket = null;
    /**
     * debug 
     * 
     * Set to 1 to enable logging
     * 
     * @var mixed
     * @access public
     */
    public $debug = 0;
    /**
     * loginUser 
     * 
     * @var mixed
     * @access public
     */
    public $loginUser = null;
    /**
     * log 
     * 
     * @var mixed
     * @access public
     */
    public $log = null;
    /**
     * logFile
     * 
     * @var mixed
     * @access public
     */
    public $logFile = '/tmp/vpopmaild.log';
    /**
     * gidFlagValues 
     * 
     * @var array
     * @access public
     */
    public $gidFlagValues = array(
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
        'no_maildrop'               => 0x40000);

    /**
     * vpopmail_robot_program 
     * 
     * @var string
     * @access public
     */
    public $vpopmail_robot_program = '/usr/bin/autorespond';
    /**
     * vpopmail_robot_time 
     * 
     * @var int
     * @access public
     */
    public $vpopmail_robot_time = 1000;
    /**
     * vpopmail_robot_number 
     * 
     * @var int
     * @access public
     */
    public $vpopmail_robot_number = 3;

    /**
     * ezmlmOpts 
     * 
     * This will be an array of the default ezmlm command 
     * line options. Use 1 for "on" or "yes"
     * 
     * @var mixed
     * @access public
     */
    public $ezmlmOpts = array(
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
     * __construct 
     * 
     * Turn on logger if debug is 1.  Create socket.
     * 
     * @access public
     * @return void
     */
    public function  __construct()
    {
        if ($this->debug > 0 && is_null($this->log)) {
            $this->log = Log::factory('file', $this->logFile);
            if (is_null($this->log)) {
                throw new Net_Vpopmaild_Exception("Error creating Log object");
            }
        }
        $this->socket = new Net_Socket();
        $result = $this->socket->connect($this->address, $this->port, null, 30);
        if (PEAR::isError($result)) {
            throw new Net_Vpopmaild_Exception($result->getMessage());
        }
        $in = $this->sockRead();
        if (!$this->statusOk($in)) {
            throw new Net_Vpopmaild_Exception("Error: initial status: $in");
        }
    }

    /**
     * recordio 
     * 
     * Log i/o to Log instance
     * 
     * @param string $data 
     * @access public
     * @return void
     */
    public function recordio($data)
    {
        if ($this->debug > 0) {
            $this->log->log($data);
        }
    }

    /**
     * statusOk 
     * 
     *  $data contains +OK
     * 
     * @param string $data 
     * @access public
     * @return bool
     */
    public function statusOk($data)
    {
        if (ereg('^[+]OK', $data)) {
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
     * @access public
     * @return bool
     */
    public function statusOkMore($data)
    {
        if (ereg('^[+]OK[+]$', $data)) {
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
     * @access public
     * @return bool
     */
    public function statusOkNoMore($data)
    {
        if (ereg('^[+]OK$', $data)) {
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
     * @access public
     * @return bool
     */
    public function statusErr($data)
    {
        if (ereg('^[-]ERR ', $data)) {
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
     * @access public
     * @return bool
     */
    public function dotOnly($data)
    {
        if (ereg('^[.]$', $data)) {
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
     * @access public
     * @return mixed
     */
    public function sockWrite($data)
    {
        $this->recordio("sockWrite send: $data");
        $result = $this->socket->writeLine($data);
        if (PEAR::isError($result)) {
            return $result;
        }
        return true;
    }

    /**
     * sockRead 
     * 
     * Read line from socket
     * 
     * @access public
     * @return mixed
     */
    public function sockRead()
    {
        $in = $this->socket->readLine();
        $this->recordio("sockRead Read: $in");
        return $in;
    }

    /**
     * rawSockRead 
     * 
     * @param int $maxLen 
     * @access public
     * @return mixed
     */
    public function rawSockRead($maxLen = 2048)
    {
        $in = $this->socket->read($maxLen);
        if (PEAR::isError($in)) {
            return $in;
        }
        $this->recordio("rawSockRead Read: $in");
        return $in = trim($in);
    }

    /**
     * quit 
     * 
     * send quit command to vpopmaild
     * 
     * @access protected
     * @return void
     */
    protected function quit()
    {
        $this->sockWrite("quit\n");
    }

    /**
     * __destruct 
     * 
     * Close socket, logger
     * 
     * @access public
     * @return void
     */
    public function __destruct()
    {
        if ($this->socket instanceof Net_Socket) {
            $this->quit();
            $this->socket->disconnect();
        }
    }







    /**
     * clogin 
     * 
     * @param mixed $email 
     * @param mixed $password 
     * @access public
     * @return mixed true on success, PEAR_Error on failure
     */
    public function clogin($email, $password)
    {
        $status = $this->sockWrite("clogin $email $password");
        if (PEAR::isError($status)) {
            return $status;
        }
        $in = $this->sockRead();
        if (PEAR::isError($in)) {
            return $in;
        }
        if (!$this->StatusOk($in)) {
            return PEAR::raiseError("Login failed - " . $in);
        }
        $this->loginUser = $this->readInfo();
        if (PEAR::isError($this->loginUser)) {
            return $this->loginUser;
        }
        return true;
    }
    /**
     * getGidBit 
     * 
     * @param mixed $bitmap 
     * @param mixed $bit 
     * @param mixed $flip 
     * @access public
     * @return mixed true or false, or PEAR_Error on failure
     */
    public function getGidBit($bitmap, $bit, $flip = false)
    {
        if (!isset($this->gidFlagValues[$bit])) {
            return PEAR::raiseError("Error - unknown GID Bit value specified. $bit");
        }
        $bitValue = $this->gidFlagValues[$bit];
        if ($flip) {
            return ($bitmap&$bitValue) ? false : true;
        }
        return ($bitmap&$bitValue) ? true : false;
    }

    /**
     * setGidBit 
     * 
     * @param mixed $bitmap 
     * @param mixed $bit 
     * @param mixed $value 
     * @param mixed $flip 
     * @access public
     * @return mixed PEAR_Error on unknown bit value, true on success
     */
    public function setGidBit(&$bitmap, $bit, $value, $flip = false)
    {
        if (!isset($this->gidFlagValues[$bit])) {
            return PEAR::raiseError("Unknown GID Bit value specified. $bit");
        }
        $bitValue = $this->gidFlagValues[$bit];
        if ($flip) {
            $value = ('t' == $value{0}) ? 0 : $bitValue;
        } else {
            $value = ('t' == $value{0}) ? $bitValue : 0;
        }
        $bitmap = (int)$value|(~(int)$bitValue&(int)$bitmap);
        return true;
    }

    /**
     * getIPMap 
     * 
     * @param mixed $ip 
     * @access public
     * @return mixed PEAR_Error on failure, IP Map on success
     */
    public function getIPMap($ip)
    {
        if ($status = $this->sockWrite("get_ip_map $ip")) {
            return PEAR::raiseError("Error - write to socket failed! $status");
        }
        $status = $this->sockRead();
        if ($this->statusErr($status)) {
            return PEAR::raiseError($status);
        }
        $lists = array();
        $in = $this->sockRead();
        while (!$this->statusErr($in) && !$this->statusOk($in) && !$this->dotOnly($in)) {
            $lists[] = $in;
            $in = $this->sockRead();
        }
        $exploded = explode(" ", $lists[0]);
        return $exploded[1];
    }

    /**
     * addIPMap 
     * 
     * @param mixed $domain 
     * @param mixed $ip 
     * @access public
     * @return mixed true on success, PEAR_Error on failure
     */
    public function addIPMap($domain, $ip)
    {
        $status = $this->sockWrite("add_ip_map $ip $domain");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        return true;
    }
    /**
     * delIPMap 
     * 
     * @param mixed $domain 
     * @param mixed $ip 
     * @access public
     * @return mixed true on success, PEAR_Error on failure
     */
    public function delIPMap($domain, $ip)
    {
        $status = $this->sockWrite("del_ip_map $ip $domain");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        return true;
    }
    /**
     * showIPMap 
     * 
     * return sorted ip map list
     * 
     * @access public
     * @return mixed PEAR_Error on failure, ip map array on success
     */
    public function showIPMap()
    {
        $status = $this->sockWrite("show_ip_map");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        $lists = array();
        $in = $this->sockRead();
        if (PEAR::isError($in)) {
            return $in;
        }
        while (!$this->dotOnly($in) && !$this->statusOk($in) && !$this->statusError($in)) {
            list($ip, $domain) = explode(' ', $in);
            if (!empty($lists[$ip])) {
                $lists[$ip].= ", ".$domain;
            } else { #  Not duplicate
                $lists[$ip] = $domain;
            }
            $in = $this->sockRead();
            if (PEAR::isError($in)) {
                return $in;
            }
        }
        ksort($lists);
        return $lists;
    }

    /**
     * formatBasePath 
     * 
     * @param mixed $domain 
     * @param string $user 
     * @param string $path 
     * @param string $type 
     * @access public
     * @return var $basePath
     */
    public function formatBasePath($domain, $user = '', $path = '', $type = 'file')
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
        $basePath = ereg_replace('//', '/', $basePath);
        return $basePath;
    }
    /**
     * readInfo 
     * 
     * Collect user/dom info into an Array and return.
     * NOTE:  +OK has already been read.
     * 
     * @access public
     * @return mixed info array on success, PEAR_Error on failure
     */
    public function readInfo()
    {
        $this->recordio("<<--  Start readInfo  -->>");
        $infoArray = array();
        $in = $this->sockRead();
        if (PEAR::isError($in)) {
            return $in;
        }
        while (!$this->dotOnly($in) && !$this->statusOk($in) && !$this->statusErr($in)) {
            if ('' != $in) {
                unset($value);
                list($name, $value) = explode(' ', $in, 2);
                $value = trim($value);
                $infoArray[$name] = $value;
            }
            $in = $this->sockRead();
            if (PEAR::isError($in)) {
                return $in;
            }
        }
        $this->recordio("readInfo collected: ");
        $this->recordio(print_r($infoArray, 1));
        $this->recordio("<<--  Finish readInfo  -->>");
        return $infoArray;
    }
    /**
     * dotQmailSplit 
     * 
     * @param mixed $fileContents 
     * @access public
     * @return array
     */
    public function dotQmailSplit($fileContents)
    {
        $result = array('Comment' => array(), 'Program' => array(), 'Delivery' => array(), 'Forward' => array(),);
        if (!is_array($fileContents)) {
            return $result;
        }
        reset($fileContents);
        while (list(, $line) = each($fileContents)) {
            switch ($line{0}) {
                case '#':
                    $result['Comment'][] = $line;
                break;
                case '|':
                    $result['Program'][] = $line;
                break;
                case '/':
                    $result['Delivery'][] = $line;
                break;
                case '&':
                default:
                    $result['Forward'][] = $line;
                break;
            }
        }
        return $result;
    }


    /**
     * robotDel 
     * 
     * @param mixed $domain 
     * @param mixed $user 
     * @access public
     * @return mixed PEAR_Error on failure, true on success
     */
    public function robotDel($domain, $user)
    {
        $result = $this->robotGet($domain, $user);
        if (PEAR::isError($result)) {
            return $result;
        }
        $robotDir = strtoupper($user);
        $dotQmailName = ".qmail-$user";

        // Get domain directory for robotPath
        $domainArray = $this->domainInfo($domain);
        if (PEAR::isError($domainArray)) {
            return $domainArray;
        }
        $robotPath = $domainArray['path']."/$robotDir";
        $result = $this->rmDir($robotPath);
        if (PEAR::isError($result)) {
            return $result;
        }
        $result = $this->RmFile($domain, '', $dotQmailName);
        if (PEAR::isError($result)) {
            return $result;
        }
        return true;
    }

    /**
     * robotSet 
     * 
     * @param mixed $domain 
     * @param mixed $user 
     * @param mixed $subject 
     * @param mixed $message 
     * @param mixed $forward 
     * @param mixed $time 
     * @param mixed $number 
     * @access public
     * @return mixed true on success, PEAR_Error on failure
     */
    public function robotSet($domain, $user, $subject, $message, $forward, $time = '', $number = '')
    {
        if ($time == '') {
            $time = $this->vpopmail_robot_time;
        }
        if ($number == '') {
            $number = $this->vpopmail_robot_number;
        }
        $robotDir = strtoupper($user);
        $dotQmailName = ".qmail-$user";
        if (!is_array($message)) {
            $message = explode("\n", $message);
        }

        // Get domain directory for robotPath
        $domainArray = $this->domainInfo($domain);
        if (PEAR::isError($domainArray)) {
            return $domainArray;
        }
        $robotPath = $domainArray['path']."/$robotDir";

        $messagePath = "$robotPath/message";
        $program = $this->vpopmail_robot_program;
        #  Build the dot qmail file
        $dotQmail = array("|$program $time $number $messagePath $robotPath");
        if (is_array($forward)) {
            array_merge($dotQmail, $forward);
        } elseif (is_string($forward)) {
            $dotQmail[] = $forward;
        }
        $result = $this->writeFile($dotQmail, $domain, '', $dotQmailName);
        if (PEAR::isError($result)) {
            return $result;
        }
        $result = $this->mkDir($domain, '', $robotDir);
        if (PEAR::isError($result)) {
            return $result;
        }
        #  NOTE:  You have to add them backwards!
        array_unshift($message, "");
        array_unshift($message, "Subject: $subject");
        array_unshift($message, "From: $user@$domain");
        $result = $this->writeFile($message, $messagePath);
        if (PEAR::isError($result)) {
            return $result;
        }
        return true;
    }

    /**
     * robotGet 
     * 
     * @param mixed $domain 
     * @param mixed $user 
     * @access public
     * @return mixed PEAR_Error on failure, robot array on success
     */
    public function robotGet($domain, $user)
    {
        $dotQmailName = ".qmail-$user";
        $dotQmail = $this->readFile($domain, '', $dotQmailName);
        if (PEAR::isError($dotQmail)) {
            return PEAR::raiseError("ERR - Unable to find dotqmail file - " . $dotQmail->getMessage());
        }
        $this->recordio("dotQmail: " . print_r($dotQmail, 1));
        $dotQmail = $this->dotQmailSplit($dotQmail);
        $this->recordio("dotQmaili split: " . print_r($dotQmail, 1));
        if (count($dotQmail['Program']) > 1)  { #  Too many programs
            return PEAR::raiseError('ERR - too many programs in robot dotqmail file');
        }
        if (!ereg($this->vpopmail_robot_program, $dotQmail['Program'][0])) {
            return PEAR::raiseError('ERR - Mail Robot program not found');
        }
        list($Program, $Time, $Number, $MessageFile, $RobotPath) = explode(' ', $dotQmail['Program'][0]);
        $message = $this->readFile($MessageFile);
        if (PEAR::isError($message)) {
            return PEAR::raiseError("ERR - Unable to find message file - " . $dotQmail->getMessage());
        }
        $result = array();
        $result['Time'] = $Time;
        $result['Number'] = $Number;
        array_shift($message); #   Eat From: address
        $result['Subject'] = substr(array_shift($message), 9);
        array_shift($message); #  eat blank line
        if (0 == count($dotQmail['Forward'])) { #  Empty
            $result['Forward'] = '';
        } elseif (count($dotQmail['Forward']) > 1) { #  array
            $result['Forward'] = $dotQmail['Forward'];
        } else { #  Single entry
            $result['Forward'] = $dotQmail['Forward'][0];
        }
        $result['Message'] = $message;
        return $result;
    }


    /**
     * listLists 
     * 
     * @param mixed $domain 
     * @param string $user 
     * @access public
     * @return mixed lists array on success or PEAR_Error on failure
     */
    public function listLists($domain, $user = '')
    {
        $basePath = $this->formatBasePath($domain, $user);
        $status = $this->sockWrite("list_lists $basePath");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        $lists = array();
        $in = $this->sockRead();
        while (!$this->dotOnly($in) && !$this->statusOk($in) && !$this->statusErr($in)) {
            $lists[] = $in;
            $in = $this->sockRead();
            if (PEAR::isError($in)) {
                return $in;
            }
        }
        return $lists;
    }

    /**
     * listAlias 
     * 
     * @param mixed $domain 
     * @param string $user 
     * @access public
     * @return mixed alias array on success, PEAR_Error on failure
     */
    public function listAlias($domain, $user = '')
    {
        $basePath = $this->formatBasePath($domain, $user);
        $status = $this->sockWrite("list_alias $basePath");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        $alii = array();
        $in = $this->sockRead();
        if (PEAR::isError($in)) {
            return $in;
        }
        while (!$this->dotOnly($in) && !$this->statusOk($in) && !$this->statusErr($in)) {
            $alii[] = $in;
            $in = $this->sockRead();
            if (PEAR::isError($in)) {
                return $in;
            }
        }
        return $alii;
    }

    /**
     * rmFile 
     * 
     * @param mixed $domain 
     * @param string $user 
     * @param string $path 
     * @access public
     * @return mixed true on success, PEAR_Error on failure
     */
    public function rmFile($domain, $user = '', $path = '')
    {
        $basePath = $this->formatBasePath($domain, $user, $path);
        $status = $this->sockWrite("rm_file $basePath");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
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
     * @return mixed true on success, PEAR_Error on failure
     */
    public function writeFile($contents, $domain, $user = '', $path = '')
    {
        $basePath = $this->formatBasePath($domain, $user, $path);
        $status = $this->sockWrite("write_file $basePath");
        if (PEAR::isError($status)) {
            return $status;
        }
        reset($contents);
        while (list(, $line) = each($contents)) {
            $status = $this->sockWrite($line);
            if (PEAR::isError($status)) {
                return $status;
            }
        }
        $status = $this->sockWrite(".");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
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
     * @return mixed file contents as array on success, PEAR_Error on failure
     */
    public function readFile($domain, $user = '', $path = '')
    {
        $basePath = $domain;
        if (!empty($user)) {
            $basePath  = "$user@$basePath";
        }
        if (!empty($path)) {
            $basePath .= "/".$path;
        }
        $status = $this->sockWrite("read_file $basePath");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        $fileContents = array();
        $in = $this->sockRead();
        while (!$this->dotOnly($in) && !$this->statusOk($in) && !$this->statusErr($in)) {
            $fileContents[] = $in;
            $in = $this->sockRead();
            if (PEAR::isError($status)) {
                return $status;
            }
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
     * @return array of directory contents, or PEAR_Error on failure
     */
    public function listDir($domain, $user = '', $path = '')
    {
        $basePath = $this->formatBasePath($domain, $user, $path, 'dir');
        $status = $this->sockWrite("list_dir $basePath");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        $directoryContents = array();
        $in = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        while (!$this->dotOnly($in) && !$this->statusOk($in) && !$this->statusErr($in)) {
            list($dirName, $type) = explode(' ', $in);
            $directoryContents[$dirName] = $type;
            $in = $this->sockRead();
            if (PEAR::isError($status)) {
                return $status;
            }
        }
        return ksort($directoryContents);
    }
    /**
     * rmDir 
     * 
     * @param mixed $domain 
     * @param string $user 
     * @param string $path 
     * @access public
     * @return mixed PEAR_Error on failure, true on success
     */
    public function rmDir($domain, $user = '', $path = '')
    {
        $basePath = $this->formatBasePath($domain, $user, $path);
        $status = $this->sockWrite("rm_dir $basePath");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (!$this->statusOk($status)) {
            return PEAR::raiseError("command rmdir failed - $status");
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
     * @return mixed PEAR_Error on failure, true on success
     */
    public function mkDir($domain, $user = '', $path = '')
    {
        $basePath = $this->formatBasePath($domain, $user, $path);
        $status = $this->sockWrite("mk_dir $basePath");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        return true;
    }

    /**
     * getLimits 
     * 
     * @param mixed $domain 
     * @access public
     * @return mixed array limits on success, PEAR_Error on failure
     */
    public function getLimits($domain)
    {
        $status = $this->sockWrite("get_limits $domain");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        $limits = $this->readInfo();
        return $limits;
    }

    /**
     * setLimits 
     * 
     * @param mixed $domain 
     * @param mixed $limits 
     * @access public
     * @return mixed true on success, PEAR_Error on failure
     */
    public function setLimits($domain, $limits)
    {
        static $stringParms = array(
                                'max_popaccounts',
                                'max_aliases',
                                'max_forwards',
                                'max_autoresponders',
                                'max_mailinglists', 
                                'disk_quota',
                                'max_msgcount',
                                'default_quota',
                                'default_maxmsgcount');

        static $flagParms = array(
                                'disable_pop',
                                'disable_imap',
                                'disable_dialup',
                                'disable_password_changing',
                                'disable_webmail',
                                'disable_external_relay',
                                'disable_smtp',
                                'disable_spamassassin',
                                'delete_spam',
                                'perm_account',
                                'perm_alias',
                                'perm_forward',
                                'perm_autoresponder',
                                'perm_maillist',
                                'perm_maillist_users',
                                'perm_maillist_moderators',
                                'perm_quota',
                                'perm_defaultquota');

        $status = $this->sockWrite("set_limits $domain");
        if (PEAR::isError($status)) {
            return $status;
        }
        // string parms
        while (list(, $name) = each($stringParms)) {
            if (!empty($limits[$name])) {
                $value = $limits[$name];
                $status = $this->sockWrite("$name $value");
                if (PEAR::isError($status)) {
                    return $status;
                }
            }
        }
        // flag parms
        while (list(, $name) = each($flagParms)) {
            if (!empty($limits[$name])) {
                $value = $limits[$name];
                $status = $this->sockWrite("$name $value");
                if (PEAR::isError($status)) {
                    return $status;
                }
            }
        }
        $status = $this->sockWrite(".");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        return true;
    }

    /**
     * delLimits 
     * 
     * @param mixed $domain 
     * @access public
     * @return mixed true on success, PEAR_Error on failure
     */
    public function delLimits($domain)
    {
        $status = $this->sockWrite("del_limits $domain");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        return true;
    }

    /**
     * domainInfo 
     * 
     * @param mixed $domain 
     * @access public
     * @return mixed dom_info array on success, PEAR_Error on failure
     */
    public function domainInfo($domain)
    {
        $out = $this->sockWrite("dom_info $domain");
        if (PEAR::isError($out))  {
            return $out;
        }
        $in = $this->sockRead();
        if (PEAR::isError($in)) {
            return $in;
        }
        if (!$this->statusOk($in)) {
            return PEAR::raiseError("dom_info failed - " . $in);
        }
        return $this->readInfo();
    }
    /**
     * listDomains 
     * 
     * @param int $page 
     * @param int $perPage 
     * @access public
     * @return mixed
     */
    public function listDomains($page = 0, $perPage = 0)
    {
        $return = $this->sockWrite("list_domains $page $perPage");
        if (PEAR::isError($return)) {
            return $return;
        }

        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError("command failed - " . $status);
        }
        $domains = array();
        $list = array();
        $in = $this->sockRead();
        if (PEAR::isError($in)) {
            return $in;
        }
        while (!$this->dotOnly($in) && !$this->statusOk($in) && !$this->statusErr($in)) {
            list($parent, $domain) = explode(' ', $in, 2);
            $domains[$domain] = $parent;
            $in = $this->sockRead();
            if (PEAR::isError($in)) {
                return $in;
            }
        }
        return $domains;
    }
    public function domainCount()
    {
        $status = $this->sockWrite("domain_count");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        $in = $this->sockRead();
        while (!$this->dotOnly($in) && !$this->statusOk($in) && !$this->statusErr($in)) {
            list(, $count) = explode(' ', $in, 2);
            $in = $this->sockRead();
        }
        return $count;
    }
    /**
     * addDomain 
     * 
     * @param mixed $domain 
     * @param mixed $password 
     * @access public
     * @return mixed true on success, PEAR_Error on failure
     */
    public function addDomain($domain, $password)
    {
        $status = $this->sockWrite("add_domain $domain $password");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        return true;
    }

    /**
     * addAliasDomain 
     * 
     * @param mixed $domain 
     * @param mixed $alias 
     * @access public
     * @return mixed true on success, PEAR_Error on failure
     */
    public function addAliasDomain($domain, $alias)
    {
        $this->Error = '';
        $status = $this->sockWrite("add_alias_domain $domain $alias");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        return true;
    }
    /**
     * delDomain 
     * 
     * @param mixed $domain 
     * @access public
     * @return mixed PEAR_Error on failure, true on success
     */
    public function delDomain($domain)
    {
        $status = $this->sockWrite("del_domain $domain");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        return true;
    }

    /**
     * findDomain 
     * 
     * return page number that the domain occurs on
     * 
     * @param mixed $domain 
     * @param mixed $perPage 
     * @access public
     * @return mixed int page on success, PEAR_Error on failure
     */
    public function findDomain($domain, $perPage)
    {
        $status = $this->sockWrite("find_domain $domain $perPage");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        while (!$this->dotOnly($in) && !$this->statusOk($in) && !$this->statusErr($in)) {
            list(, $page) = explode(' ', $in, 2);
            $in = $this->sockRead();
            if (PEAR::isError($in)) {
                return $in;
            }
        }
        return $page;
    }

    /**
     * addUser 
     * 
     * @param mixed $domain 
     * @param mixed $user 
     * @param mixed $password 
     * @access public
     * @return mixed true on success, PEAR_Error on failure
     */
    public function addUser($domain, $user, $password)
    {
        $status = $this->sockWrite("add_user $user@$domain $password");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        return true;
    }

    /**
     * delUser 
     * 
     * @param mixed $domain 
     * @param mixed $user 
     * @access public
     * @return true on success, PEAR_Error on failure
     */
    public function delUser($domain, $user)
    {
        $status = $this->sockWrite("del_user $user@$domain");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        return true;
    }


    /**
     * modUser 
     * 
     * @param mixed $domain 
     * @param mixed $user 
     * @param mixed $userInfo 
     * @access public
     * @return mixed true on success, PEAR_Error on failure
     */
    public function modUser($domain, $user, $userInfo)
    {
        #  NOTE:  If you want your users to be able to change passwords
        #         from ModUser, you must un-comment the name below.
        $stringParms = array(   'quota',
                                'comment',
                                'clear_text_password');

        $flagParms = array(     'no_password_change',
                                'no_pop',
                                'no_webmail',
                                'no_imap',
                                'no_smtp',
                                'bounce_mail',
                                'no_relay',
                                'no_dialup',
                                'user_flag_0',
                                'user_flag_1',
                                'user_flag_2',
                                'user_flag_3',
                                'system_admin_privileges',
                                'system_expert_privileges',
                                'domain_admin_privileges',
                                'override_domain_limits',
                                'no_spamassassin',
                                'no_maildrop',
                                'delete_spam',);

        $status = $this->sockWrite("mod_user $user@$domain");
        if (PEAR::isError($status)) {
            return $status;
        }
        while (list(, $name) = each($stringParms)) {
            if (!empty($userInfo[$name])) {
                $value = $userInfo[$name];
                $status = $this->sockWrite("$name $value");
                if (PEAR::isError($status)) {
                    return $status;
                }
            }
        }
        while (list(, $name) = each($flagParms)) {
            $flip = false;
            $value = $this->getGidBit($userInfo['gidflags'], $name, $flip);
            $value = ($value) ? '1' : '0';
            $status = $this->sockWrite("$name $value");
            if (PEAR::isError($status)) {
                return $status;
            }
        }
        $status = $this->sockWrite(".");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        // Not sure what this is for - bshupp
        // if (!empty($Warnings)) {
        //     return "Warning:\n   ".implode("\n   ", $Warnings) ."\n\n";
        // }
        return true;
    }
    /**
     * userInfo 
     * 
     * @param mixed $domain 
     * @param mixed $user 
     * @access public
     * @return mixed user info array on success, PEAR_Error on failure
     */
    public function userInfo($domain, $user)
    {
        $status = $this->sockWrite("user_info $user@$domain");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        return $this->readInfo();
    }

    public function listUsers($domain, $page = 0, $perPage = 0)
    {
        $status = $this->sockWrite("list_users $domain $page $perPage");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        $i = 0;
        $currentName = '';
        $list = array();
        $this->recordio("<<--  Start collecting user data  -->>");
        $in = $this->sockRead();
        if (PEAR::isError($in)) {
            return $in;
        }
        while (!$this->dotOnly($in) && !$this->statusOk($in) && !$this->statusErr($in) && $i < 10) {
            list($name, $value) = explode(' ', $in, 2);
            if ('name' == $name) {
                if (!empty($currentName)) {
                    $list[$currentName] = $user;
                }
                $currentName = $value;
                $user = array();
            } else {
                $user[$name] = trim($value);
            }
            $in = $this->sockRead();
            if (PEAR::isError($in)) {
                return $in;
            }
        }
        if (!empty($currentName)) {
            $list[$currentName] = $user;
        }
        $this->recordio("<<--  Stop collecting user data  -->>");
        return $list;
    }
    /**
     * userCount 
     * 
     * @param mixed $domain 
     * @access public
     * @return int count on success, PEAR_Error on failure
     */
    public function userCount($domain)
    {
        $status = $this->sockWrite("user_count $domain");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (!$this->statusOk($status)) {
            return PEAR::raiseError("command failed - $in");
        }
        $in = $this->sockRead();
        while (!$this->dotOnly($in) && !$this->statusOk($in) && !$this->statusErr($in)) {
            list(, $count) = explode(' ', $in, 2);
            $in = $this->sockRead();
        }
        return $count;
    }

    /**
     * getLastAuthIP 
     * 
     * @param mixed $domain 
     * @param mixed $user 
     * @access public
     * @return return string IP on success, PEAR_Error on failure
     */
    public function getLastAuthIP($domain, $user)
    {
        $status = $this->sockWrite("get_lastauthip $user@$domain");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        $in = $this->sockRead();
        return $in;
    }

    /**
     * getLastAuth 
     * 
     * @param mixed $domain 
     * @param mixed $user 
     * @access public
     * @return string time on success, PEAR_Error on failure
     */
    public function getLastAuth($domain, $user)
    {
        $status = $this->sockWrite("get_lastauth $user@$domain");
        if (PEAR::isError($status)) {
            return $status;
        }
        $status = $this->sockRead();
        if (PEAR::isError($status)) {
            return $status;
        }
        if (!$this->statusOk($status)) {
            return PEAR::raiseError($status);
        }
        $in = $this->sockRead();
        return $in;
    }

    /**
     * authenticate 
     * 
     * Authenticate user based on email and password
     * 
     * @param mixed $email 
     * @param mixed $password 
     * @access public
     * @return mixed true on success, PEAR_Error on failure
     */
    public function authenticate($email, $password)
    {
        $result = $this->clogin($email, $password);
        if (PEAR::isError($result)) {
            return $result;
        }
        // Easy way to access domain
        $email_array = explode('@', $email);
        $this->loginUser['domain'] = $email_array[1];
        return true;
    }

    /**
     * isSysAdmin 
     * 
     * @param string $acct_info 
     * @access public
     * @return void
     */
    function isSysAdmin($acct_info = '') {
        if ($acct_info == '') {
            $acct_info = $this->loginUser;
        }
        return $this->getGidBit($acct_info['gidflags'], 'system_admin_privileges');
    }
    /**
     * Is Domain Admin
     *
     * Determin if this is a domain administrator for this domain
     *
     * @author Bill Shupp <hostmaster@shupp.org>
     *
     */

    function isDomainAdmin($domain, $acct_info = '') {
        if ($acct_info == '') {
            $acct_info = $this->loginUser;
        }
        if ($this->isSysAdmin()) {
            return true;
        }
        if ($this->getGidBit($acct_info['gidflags'], 'domain_admin_privileges')) {
            return true;
        }
        if (($acct_info['user'] == 'postmaster') && $domain == $acct_info['domain']) {
            return true;
        }
        return false;
    }

    /**
     * Is User Admin
     *
     * Determin if this user have privileges on this account
     *
     * @author Bill Shupp <hostmaster@shupp.org>
     *
     */
    function isUserAdmin($account, $domain) {
        if ($this->isDomainAdmin($domain)) {
            return true;
        }
        if (($this->loginUser['name'] == $account) && ($this->loginUser['domain'] == $domain)) {
            return true;
        }
        return false;
    }

    /**
     * getQuota 
     * 
     * @param mixed $quota 
     * @access public
     * @return string
     */
    function getQuota($quota) {
        if (ereg('S$', $quota)) {
            $quota = ereg_replace('S$', '', $quota);
            $quota = $quota/1024;
            $quota = $quota/1024;
            $quota = $quota.'MB';
        }
        return $quota;
    }
    /**
     * Parse Home dot-qmail
     *
     * Evaluate contents of a .qmail file in a user's home directory.
     * Looking for routing types standard, delete, or forward, with optional
     * saving of messages, as well as vacation messages.
     *
     * @author Bill Shupp <hostmaster@shupp.org>
     * @param mixed $contents 
     * @param mixed $account_info 
     * @access public
     * @return array $defaults
     */
    function parseHomeDotqmail($contents, $account_info) {
        $is_standard = false;
        $is_deleted = false;
        $is_forwarded = false;
        // Set default template settings
        $defaults['comment'] = $account_info['comment'];
        $defaults['forward'] = '';
        $defaults['save_a_copy_checked'] = '';
        $defaults['vacation'] = '';
        $defaults['vacation_subject'] = '';
        $defaults['vacation_body'] = '';
        if (empty($contents)) {
            $is_standard = true;
        }
        if ((is_array($contents) && count($contents) == 1 && $contents[0] == '# delete')) {
            $is_deleted = true;
        }
        if ($is_standard) {
            $defaults['routing'] = 'routing_standard';
        } else if ($is_deleted) {
            $defaults['routing'] = 'routing_deleted';
        } else {
            // now let's parse it
            while (list($key, $val) = each($contents)) {
                if ($val == $account_info['user_dir'].'/Maildir/' || $val == './Maildir/') {
                    $defaults['save_a_copy_checked'] = ' checked';
                    continue;
                }
                if (ereg($this->vpopmail_robot_program, $val)) {
                    $vacation_array = $this->getVacation($val, $account_info);
                    while (list($vacKey, $vacVal) = each($vacation_array)) {
                        $defaults[$vacKey] = $vacVal;
                    }
                    continue;
                } else {
                    if (Validate::email(ereg_replace('^&', '', $val), array('use_rfc822' => 1))) {
                        $is_forwarded = true;
                        $defaults['routing'] = 'routing_forwarded';
                        $defaults['forward'] = ereg_replace('^&', '', $val);
                    }
                }
            }
            // See if default routing select applies
            if (!$is_standard && !$is_deleted && !$is_forwarded) {
                $defaults['routing'] = 'routing_standard';
            }
        }
        return $defaults;
    }
    /**
     * Get Vacaation Message Contents
     *
     * Parse use .qmail line contents to get message subject and meessage body
     *
     * @author Bill Shupp <hostmaster@shupp.org>
     * @param string $line 
     * @param mixed $user_info 
     * @access public
     * @return void
     */
    function getVacation($line = '', $user_info) {
        if ($line == '') {
            $path = $user_info['user_dir'].'/vacation/message';
        } else {
            $line = ereg_replace('^[|][ ]*', '', $line);
            $array = explode(' ', $line);
            $path = $array[3];
        }
        $contents = $this->readFile($path);
        if (PEAR::isError($contents)) {
            return $contents;
        }
        array_shift($contents); #   Eat From: address
        $subject = substr(array_shift($contents), 9);
        array_shift($contents); #  eat blank line
        return array(   'vacation_subject' => $subject,
                        'vacation_body' => implode("\n", $contents),
                        'vacation' => ' checked');
    }
    /**
     * setupVacation
     *
     * @param mixed $user
     * @param mixed $domain
     * @param mixed $subject
     * @param mixed $message
     * @param string $acct_info
     * @access public
     * @return void
     */
    function setupVacation($user, $domain, $subject, $message, $acct_info = '')
    {
        if ($acct_info == '') {
            global $user_info;
            $acct_info = $user_info;
        }
        $vacation_dir = $user_info['user_dir'].'/vacation';
        $message_file = $dir.'/message';
        $contents = "From: $user@$domain\n";
        $contents.= "Subject: $subject\n\n";
        $contents.= "$message\n";
        $this->rmDir($domain, $user, $vacation_dir);
        $this->mkDir($vacation_dir);
        $this->writeFile($contents, $domain, $user, $message_file);
    }

    /**
     * getAliasContents 
     * 
     * @param mixed $contentsArray 
     * @access public
     * @return string
     */
    function getAliasContents($contentsArray) {
        $count = 0;
        $string = '';
        while (list($key, $val) = each($contentsArray)) {
            if ($count > 0) {
                $string.= ', ';
            }
            $string.= ereg_replace('^&', '', $val);
            $count++;
        }
        return $string;
    }

    /**
     * aliasesToArray 
     * 
     * take raw ListAlias output, and format into 
     * associative arrays
     * 
     * @param mixed $aliasArray 
     * @access public
     * @return void
     */
    function aliasesToArray($aliasArray) {
        // generate unique list of aliases
        $aliasList = array();
        while (list($key, $val) = each($aliasArray)) {
            $alias = ereg_replace('(^[^ ]+) .*$', '\1', $val);
            if (!in_array($alias, $aliasList)) {
                array_push($aliasList, $alias);
            }
        }
        // Now create content arrays
        $contentArray = array();
        reset($aliasList);
        while (list($key, $val) = each($aliasList)) {
            reset($aliasArray);
            $count = 0;
            while (list($lkey, $lval) = each($aliasArray)) {
                if (ereg("^$val ", $lval)) {
                    $aliasLine = ereg_replace('^[^ ]+ (.*$)', '\1', $lval);
                    $contentArray[$val][$count] = $aliasLine;
                    $count++;
                }
            }
        }
        return $contentArray;
    }

    /**
     * displayForwardLine
     *
     * @param mixed $line
     * @access public
     * @return mixed null on failure, string on success
     */
    function displayForwardLine($line) {
        if (ereg('^&', $line)) {
            return ereg_replace('^&', '', $line);
        }
    }


    /**
     * parseAliases 
     * 
     * Return correct type of aliases - forwards, responders, or lists (ezmlm)
     * 
     * @param mixed $in_array 
     * @param mixed $type 
     * @access public
     * @return array of parsed aliases
     */
    function parseAliases($in_array, $type) {
        $out_array = array();
        $raw_array = $this->aliasesToArray($in_array);
        foreach ($raw_array as $parentkey => $parentval) {
            $is_type = 'forwards';
            foreach ($parentval as $key => $val) {
                if (ereg('[|].*' . $this->vpopmail_robot_program, $val)) {
                    $is_type = 'responders';
                    break;
                }
                if (ereg('[|].*ezmlm-', $val)) {
                    $is_type = 'lists';
                    break;
                }
            }
            if ($type == $is_type) {
                $out_array[$parentkey] = $parentval;
            }
        }
        return $out_array;
    }

    /**
     * paginateArray 
     * 
     * A simple function to paginate an array.  Could probably be better.
     * 
     * @param mixed $array 
     * @param mixed $page 
     * @param mixed $limit 
     * @access public
     * @return array
     */
    function paginateArray($array, $page, $limit) {
        $page_count = 1;
        $limit_count = 1;
        $out_array = array();
        while ((list($key, $val) = each($array)) && $page_count <= $page) {
            if ($page_count == $page) {
                $out_array[$key] = $val;
            }
            $limit_count++;
            if ($limit_count > $limit) {
                $limit_count = 1;
                $page_count++;
            }
        }
        return $out_array;
    }

}
?>
