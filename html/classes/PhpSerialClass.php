<?php
define("SERIAL_DEVICE_NOTSET", 0);
define("SERIAL_DEVICE_SET", 1);
define("SERIAL_DEVICE_OPENED", 2);

/**
 * Serial port control class
 *
 * THIS PROGRAM COMES WITH ABSOLUTELY NO WARRANTIES !
 * USE IT AT YOUR OWN RISKS !
 *
 * @author RÃƒÂ©my Sanchez <remy.sanchez@hyperthese.net>
 * @author Rizwan Kassim <rizwank@geekymedia.com>
 * @thanks AurÃƒÂ©lien Derouineau for finding how to open serial ports with windows
 * @thanks Alec Avedisyan for help and testing with reading
 * @thanks Jim Wright for OSX cleanup/fixes.
 * @copyright under GPL 2 licence
 */
class PhpSerial
{
  public $_device = null;
  public $_winDevice = null;
  public $_dHandle = null;
  public $_dState = SERIAL_DEVICE_NOTSET;
  public $_buffer = "";
  public $_os = "";

  /**
   * This var says if buffer should be flushed by sendMessage (true) or
   * manually (false)
   *
   * @var bool
   */
  public $autoFlush = true;

  /**
   * Constructor. Perform some checks about the OS and setserial
   *
   * @return PhpSerial
   */

  //   public function __construct()
  //   {
  //To prevent deprication error
  //   }

  public function __construct($radio)
  {
    echo "starting phpserial\n";
    setlocale(LC_ALL, "en_US");

    $sysName = php_uname();

    if (substr($sysName, 0, 5) === "Linux") {
      $this->_os = "linux";

      if ($this->_exec("stty --version") === 0) {
        register_shutdown_function([$this, "deviceClose"]);
      } else {
        trigger_error("No stty available, unable to run.", E_USER_ERROR);
      }
    } else {
      trigger_error(
        "Host OS is neither osx, linux nor windows, unable " . "to run.",
        E_USER_ERROR
      );
      exit();
    }
  }

  //
  // OPEN/CLOSE DEVICE SECTION -- {START}
  //

  /**
   * Device set function : used to set the device name/address.
   * -> linux : use the device address, like /dev/serial0
   * -> osx : use the device address, like /dev/tty.serial
   * -> windows : use the COMxx device name, like COM1 (can also be used
   *     with linux)
   *
   * @param  string $device the name of the device to be used
   * @return bool
   */
  public function deviceSet($device)
  {
    echo "setting device: " . $device . "\n";
    if ($this->_dState !== SERIAL_DEVICE_OPENED) {
      if ($this->_os === "linux") {
//        if (preg_match("@^COM(\\d+):?$@i", $device, $matches)) {
//          $device = "/dev/ttyS0";
//        }
        if ($this->_exec("stty -F " . $device) === 0) {
          echo "device: " . $device . "\n";
          $this->_device = $device;
          $this->_dState = SERIAL_DEVICE_SET;

          return true;
        }
      }

      trigger_error(
        "Specified serial port is not valid" .
          " device: " .
          $device .
          " " .
          $this->_os,
        E_USER_WARNING
      );

      return false;
    } else {
      trigger_error(
        "You must close your device before to set an other " . "one",
        E_USER_WARNING
      );

      return false;
    }
  }

  /**
   * Opens the device for reading and/or writing.
   *
   * @param  string $mode Opening mode : same parameter as fopen()
   * @return bool
   */
  public function deviceOpen($mode = "r+b")
  {
    //    echo "opening device " . $this->_dState . "\n";
    if ($this->_dState === SERIAL_DEVICE_OPENED) {
      trigger_error("The device is already opened", E_USER_NOTICE);

      return true;
    }

    if ($this->_dState === SERIAL_DEVICE_NOTSET) {
      trigger_error("The device must be set before to be open", E_USER_WARNING);

      return false;
    }

    if (!preg_match("@^[raw]\\+?b?$@", $mode)) {
      trigger_error(
        "Invalid opening mode : " . $mode . ". Use fopen() modes.",
        E_USER_WARNING
      );

      return false;
    }

    $this->_dHandle = @fopen($this->_device, $mode);
echo "open: $this->_dHandle $this->_device \n";
    if ($this->_dHandle !== false) {
      stream_set_blocking($this->_dHandle, 0);
      $this->_dState = SERIAL_DEVICE_OPENED;

      return true;
    }

    $this->_dHandle = null;
    trigger_error("Unable to open the device", E_USER_WARNING);

    return false;
  }

  /**
   * Closes the device
   *
   * @return bool
   */
  public function deviceClose()
  {
    if ($this->_dState !== SERIAL_DEVICE_OPENED) {
      return true;
    }

    if (fclose($this->_dHandle)) {
      $this->_dHandle = null;
      $this->_dState = SERIAL_DEVICE_SET;

      return true;
    }

    trigger_error("Unable to close the device", E_USER_ERROR);

    return false;
  }

  //
  // OPEN/CLOSE DEVICE SECTION -- {STOP}
  //

  //
  // CONFIGURE SECTION -- {START}
  //

  /**
   * Configure the Baud Rate
   * Possible rates : 110, 150, 300, 600, 1200, 2400, 4800, 9600, 38400,
   * 57600 and 115200.
   *
   * @param  int  $rate the rate to set the port in
   * @return bool
   */
  public function confBaudRate($rate)
  {
    //    echo "rate : " . $rate . "\n";
    if ($this->_dState !== SERIAL_DEVICE_SET) {
      trigger_error(
        "Unable to set the baud rate : the device is " .
          "either not set or opened",
        E_USER_WARNING
      );

      return false;
    }

    $validBauds = [
      110 => 11,
      150 => 15,
      300 => 30,
      600 => 60,
      1200 => 12,
      2400 => 24,
      4800 => 48,
      9600 => 96,
      19200 => 19,
      38400 => 38400,
      57600 => 57600,
      115200 => 115200,
    ];

    if (isset($validBauds[$rate])) {
      $ret = $this->_exec(
        "stty -F " . $this->_device . " " . (int) $rate,
        $out
      );

      if ($ret !== 0) {
        trigger_error("Unable to set baud rate: " . $out[1], E_USER_WARNING);

        return false;
      }

      return true;
    } else {
      return false;
    }
  }

  /**
   * Configure parity.
   * Modes : odd, even, none
   *
   * @param  string $parity one of the modes
   * @return bool
   */
  public function confParity($parity)
  {
    if ($this->_dState !== SERIAL_DEVICE_SET) {
      trigger_error(
        "Unable to set parity : the device is either not set or opened",
        E_USER_WARNING
      );

      return false;
    }

    $args = [
      "none" => "-parenb",
      "odd" => "parenb parodd",
      "even" => "parenb -parodd",
    ];

    if (!isset($args[$parity])) {
      trigger_error("Parity mode not supported", E_USER_WARNING);

      return false;
    }

    $ret = $this->_exec(
      "stty -F " . $this->_device . " " . $args[$parity],
      $out
    );

    if ($ret === 0) {
      return true;
    }

    trigger_error("Unable to set parity : " . $out[1], E_USER_WARNING);

    return false;
  }

  /**
   * Sets the length of a character.
   *
   * @param  int  $int length of a character (5 <= length <= 8)
   * @return bool
   */
  public function confCharacterLength($int)
  {
    if ($this->_dState !== SERIAL_DEVICE_SET) {
      trigger_error(
        "Unable to set length of a character : the device " .
          "is either not set or opened",
        E_USER_WARNING
      );

      return false;
    }

    $int = (int) $int;
    if ($int < 5) {
      $int = 5;
    } elseif ($int > 8) {
      $int = 8;
    }

    if ($this->_os === "linux") {
      $ret = $this->_exec("stty -F " . $this->_device . " cs" . $int, $out);
    }

    if ($ret === 0) {
      return true;
    }

    trigger_error(
      "Unable to set character length : " . $out[1],
      E_USER_WARNING
    );

    return false;
  }

  /**
   * Sets the length of stop bits.
   *
   * @param  float $length the length of a stop bit. It must be either 1,
   *                       1.5 or 2. 1.5 is not supported under linux and on
   *                       some computers.
   * @return bool
   */
  public function confStopBits($length)
  {
    if ($this->_dState !== SERIAL_DEVICE_SET) {
      trigger_error(
        "Unable to set the length of a stop bit : the " .
          "device is either not set or opened",
        E_USER_WARNING
      );

      return false;
    }

    if (
      $length != 1 and
      $length != 2 and
      $length != 1.5 and
      !($length == 1.5 and $this->_os === "linux")
    ) {
      trigger_error("Specified stop bit length is invalid", E_USER_WARNING);

      return false;
    }

    $ret = $this->_exec(
      "stty -F " . $this->_device . " " . ($length == 1 ? "-" : "") . "cstopb",
      $out
    );

    if ($ret === 0) {
      return true;
    }

    trigger_error("Unable to set stop bit length : " . $out[1], E_USER_WARNING);

    return false;
  }

  /**
   * Configures the flow control
   *
   * @param  string $mode Set the flow control mode. Availible modes :
   *                      -> "none" : no flow control
   *                      -> "rts/cts" : use RTS/CTS handshaking
   *                      -> "xon/xoff" : use XON/XOFF protocol
   * @return bool
   */
  public function confFlowControl($mode)
  {
    if ($this->_dState !== SERIAL_DEVICE_SET) {
      trigger_error(
        "Unable to set flow control mode : the device is " .
          "either not set or opened",
        E_USER_WARNING
      );

      return false;
    }

    $linuxModes = [
      "none" => "clocal -crtscts -ixon -ixoff",
      "rts/cts" => "-clocal crtscts -ixon -ixoff",
      "xon/xoff" => "-clocal -crtscts ixon ixoff",
    ];
    $windowsModes = [
      "none" => "xon=off octs=off rts=on",
      "rts/cts" => "xon=off octs=on rts=hs",
      "xon/xoff" => "xon=on octs=off rts=on",
    ];

    if ($mode !== "none" and $mode !== "rts/cts" and $mode !== "xon/xoff") {
      trigger_error("Invalid flow control mode specified", E_USER_ERROR);

      return false;
    }

    $ret = $this->_exec(
      "stty -F " . $this->_device . " " . $linuxModes[$mode],
      $out
    );

    if ($ret === 0) {
      return true;
    } else {
      trigger_error("Unable to set flow control : " . $out[1], E_USER_ERROR);

      return false;
    }
  }

  /**
   * Sets a setserial parameter (cf man setserial)
   * NO MORE USEFUL !
   * 	-> No longer supported
   * 	-> Only use it if you need it
   *
   * @param  string $param parameter name
   * @param  string $arg   parameter value
   * @return bool
   */
  public function setSetserialFlag($param, $arg = "")
  {
    if (!$this->_ckOpened()) {
      return false;
    }

    $return = exec(
      "setserial " . $this->_device . " " . $param . " " . $arg . " 2>&1"
    );

    if ($return[0] === "I") {
      trigger_error("setserial: Invalid flag", E_USER_WARNING);

      return false;
    } elseif ($return[0] === "/") {
      trigger_error("setserial: Error with device file", E_USER_WARNING);

      return false;
    } else {
      return true;
    }
  }

 /**
  * Sets a ptt parameter
  * NO MORE USEFUL !
  * 	-> No longer supported
  * 	-> Only use it if you need it
  *
  * @param  string $param parameter name
  * @param  string $arg   parameter value
  * @return bool
  */
 public function confPTT($param, $arg = "")
 {
   if (!$this->_ckOpened()) {
     return false;
   }
 
   $return = exec(
     "setserial " . $this->_device . " " . $param . " " . $arg . " 2>&1"
   );
 
   if ($return[0] === "I") {
     trigger_error("setPTT: Invalid flag", E_USER_WARNING);
 
     return false;
   } elseif ($return[0] === "/") {
     trigger_error("setserial: Error with device file", E_USER_WARNING);
 
     return false;
   } else {
     return true;
   }
 }
 
 //
  // CONFIGURE SECTION -- {STOP}
  //

  //
  // I/O SECTION -- {START}
  //

  /**
   * Sends a string to the device
   *
   * @param string $str          string to be sent to the device
   * @param float  $waitForReply time to wait for the reply (in seconds)
   */
  public function sendMessage($str, $waitForReply = 0.1)
  {
      echo "phps send: $str" . "\n";
    /////////////////////////////// next test ???
    //    if (strlen($str) > 0 && $str != 0) {
    $this->_buffer .= $str;
    //    echo "\n" . $this->autoFlush . "sm: " . $str . "\n";
    if ($this->autoFlush === true) {
      $this->serialflush();
      usleep((int) (.1 * 1000000));
      echo "ser serial class wrote " . $str . " " . bin2hex($str) . "\n";
      return true;
    } else {
      usleep((int) ($waitForReply * 1000000));
      return false;
    }
    //    }
  }

  /**
   * Reads the port until no new datas are available, then return the content.
   *
   * @param int $count Number of characters to be read (will stop before
   *                   if less characters are in the buffer)
   * @return string
   */
  public function readPort($count)
  {
    //    $count = 8;
        echo "phps readport " . bin2hex($count) . "\n";
    if ($this->_dState !== SERIAL_DEVICE_OPENED) {
      trigger_error("Device must be opened to read it", E_USER_WARNING);
      //      echo "oops no serial\n";
      return false;
    }
    $content = "";
    $i = 0;

    if ($count !== 0) {
      do {
        if ($i > $count) {
          $ts = fread($this->_dHandle, $count - $i);
          //         echo $ts;
          $content .= $ts;
        } else {
          $ts = fread($this->_dHandle, 128);
                    echo "TS: " . $ts;
          $content .= $ts;
        }
      } while (($i += 128) === strlen($content));
    } else {
      do {
        $content1 = fread($this->_dHandle, 128);
        for ($j = 0; $j < strlen($content1); $i++) {
          $content2 = substr($content1, $j, 1);
          if (intval($content2) < 96) {
            $content .= $content2; //fread($this->_dHandle, 128);
          }
        }
      } while (($i += 128) === strlen($content1));
    }
    //    echo "content: " . bin2hex($content) . "\n";
    if (strlen($content) > 0) {
      /*      echo "ser serial class reading port: " .
        $content .
        " " .
        bin2hex($content) .
        " \n";
        
*/
      return $content;
    } else {
      return "";
    }
  }

  //        return false;
  //    }

  /**
   * Flushes the output buffer
   * Renamed from flush for osx compat. issues
   *
   * @return bool
   */
  public function serialflush()
  {
    if (!$this->_ckOpened()) {
      return false;
    }
    /*    trigger_error(
//      "sending _buffer " .
/        bin2hex($this->_buffer) .
        " len: " .
        strlen($this->_buffer),
      E_USER_NOTICE
    );
    */
    echo "about to write\n";
    if (fwrite($this->_dHandle, $this->_buffer) !== false) {
       echo "wrote: " . $this->_buffer . " $this->_dHandle \n";
      $this->_buffer = "";
      return true;
    } else {
      $this->_buffer = "";
      trigger_error("Error while sending message", E_USER_WARNING);
      //     echo "oops\n";

      return false;
    }
  }

  //
  // I/O SECTION -- {STOP}
  //

  //
  // INTERNAL TOOLKIT -- {START}
  //

  public function _ckOpened()
  {
    if ($this->_dState !== SERIAL_DEVICE_OPENED) {
      trigger_error("Device must be opened", E_USER_WARNING);

      return false;
    }

    return true;
  }

  public function _ckClosed()
  {
    if ($this->_dState === SERIAL_DEVICE_OPENED) {
      trigger_error("Device must be closed", E_USER_WARNING);

      return false;
    }

    return true;
  }

  public function _exec($cmd, &$out = null)
  {
    $desc = [
      1 => ["pipe", "w"],
      2 => ["pipe", "w"],
    ];

    $proc = proc_open($cmd, $desc, $pipes);

    $ret = stream_get_contents($pipes[1]);
    $err = stream_get_contents($pipes[2]);

    fclose($pipes[1]);
    fclose($pipes[2]);

    $retVal = proc_close($proc);

    if (func_num_args() == 2) {
      $out = [$ret, $err];
    }
    return $retVal;
  }

  //
  // INTERNAL TOOLKIT -- {STOP}
  //
}
