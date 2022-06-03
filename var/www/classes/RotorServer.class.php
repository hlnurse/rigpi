<?php
require_once "/var/www/html/programs/GetSettingsFunc.php";
require_once "/var/www/html/programs/SetSettingsFunc.php";
require_once "/var/www/html/programs/GetInterfaceOut.php";
require_once "/var/www/html/classes/PhpSerialClass.php";

class RotorServer
{
  private $cDB;
  private static $user;
  protected $config;
  public $rotor_socket;

  public $max_read = 1024;

  /*!	@function	__construct
			@abstract	Creates the socket and starts listening to it.
			@param		string	- IP Address to bind to, NULL for default.
			@param		int	- Port to bind to
			@result		void
		 */
  public function __construct($bind_ip, $port, $rotor, $test)
  {
    $this->debug("construct start");
    set_time_limit(0);
    if (!($port > 4532 && $port < 5000)) {
      //ve9gj
      $port = $rotor * 2 + 4531;
    }
    $this->config["ip"] = $bind_ip;
    $this->config["port"] = $port;
    $this->config["rotor"] = $rotor;
    $this->config["minAz"] = $this->getAzEl("Min Azimuth");
    $this->config["maxAz"] = $this->getAzEl("Max Azimuth");
    $this->config["test"] = $test;
    $this->doRotorLog(
      $test,
      "Rotor class construct: IP: " .
        $bind_ip .
        " Port: " .
        $port .
        " Rotor: " .
        $rotor
    );
    if ($this->config["port"] != 0) {
      ($this->rotor_socket = fsockopen(
        $this->config["ip"],
        $this->config["port"],
        $errnum,
        $errstr,
        0
      )) or die("Failed to Open");
    }
  }

  public function doRotorLog($utest, $what)
  {
    if ($utest == 1) {
      error_log(
        date("Y-m-d H:i:s", time()) . " " . $what,
        3,
        "/var/log/rigpi-rotor.log"
      );
    }
  }

  public function setMyCall($which)
  {
    $this::$myCall = $which;
  }

  public function getMyCall()
  {
    return $this::$myCall;
  }

  public function setRotorInterface($r, $f, $d)
  {
    SetInterface($r, $f, $d);
  }

  public function sendRotorMessage($what)
  {
    $what = $what . "\n";
    $this->rotor_socket_write_smart($this->rotor_socket, $what, "");
    $this->doRotorLog($this->config["test"], "Socket write: " . $what);
  }

  public function getRotorMessage($count)
  {
    $ret = $this->rotor_socket_read($this->rotor_socket, $count);
    $this->doRotorLog($this->config["test"], "Socket read: " . $ret);
    return $ret;
  }

  public function loop_once()
  {
    $tRotor = $this->config["rotor"];
    $dataIn = GetRotorOut($tRotor);
    $data = explode(chr(96), $dataIn);
    $tAz = $data[0];
    $tRotorCk = $data[1];
    $tEl = $data[2];
    $tStop = $data[3];
    if ($tStop == 1) {
      $tosend = "S";
      $this->sendRotorMessage($tosend);
      $trig = $this->getRotorMessage(11);
      $this->setRotorInterface($tRotor, "RotorStop", "0");
      return true;
    }
    if ($tRotorCk == 1) {
      $this->setRotorInterface($tRotor, "RotorCk", "0");
      $tField = "RotorAzIn";
      $tData = $tAz;
      $this->setRotorInterface($tRotor, $tField, $tData);
      $tField = "RotorElIn";
      $tEl = "0";
      $tData = $tEl;
      $this->setRotorInterface($tRotor, $tField, $tData);
      $command = "P";
      $tMin = $this->config["minAz"];
      $tMax = $this->config["maxAz"];
      if ($tAz > $tMax) {
        $tAz = $tAz - 360;
      }
      if ($tAz < $tMin) {
        $tAz = $tAz + 360;
      }
      $tosend = $command . " $tAz $tEl";
      usleep(500000);
      $this->sendRotorMessage($tosend);
      $trig = $this->getRotorMessage(11);
      return true;
    }
    $tosend = "p";
    $this->sendRotorMessage($tosend);
    $tRotor1 = $this->getRotorMessage(40);
    if (strlen($tRotor1) > 0) {
      $aRot = explode("\n", $tRotor1);
      $tField = "RotorAzIn";
      $sAz = $aRot[0];
      if ($sAz < 0) {
        $sAz = 360 + $sAz;
      }
      $this->setRotorInterface($tRotor, $tField, $sAz);
    }

    return true;
  }

  /*!	@function	infinite_loop
			@abstract	Runs the server code until the server is shut down.
			@see		loop_once
			@param		void
			@result		void
		*/
  public function infinite_loop()
  {
    $test = true;
    do {
      $test = $this->loop_once();
      usleep(1000000);
    } while ($test);
    echo "ALL DONE";
  }

  /*!	@function	debug
			@static
			@abstract	Outputs Text directly.
			@discussion	Yeah, should probably make a way to turn this off.
			@param		string	- Text to Output
			@result		void
		*/
  public static function debug($text)
  {
    echo "{$text}\r\n";
  }

  public static function rotor_socket_write_smart(&$sock, $string, $crlf = "")
  {
    if ($crlf) {
      $string = "{$string}{$crlf}";
    }
    return fwrite($sock, $string);
  }

  public static function rotor_socket_read(&$sock, $count)
  {
    $read = fread($sock, $count);
    return $read;
  }

  function &__get($name)
  {
    return $this->{$name};
  }

  public function getAzEl($what)
  {
    $rotorID = getField($this->config["rotor"], "RotorID", "MySettings");
    $data = shell_exec(
      "rotctl -m " .
        $rotorID .
        " -r localhost:" .
        $this->config["port"] .
        " -u | grep '" .
        $what .
        "'"
    );
    return trim(substr($data, strpos($data, ":") + 1));
  }
}

?>
