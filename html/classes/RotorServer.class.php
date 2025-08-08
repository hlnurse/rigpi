<?php
require_once "/var/www/html/programs/GetSettingsFunc.php";
require_once "/var/www/html/programs/SetSettingsFunc.php";
require_once "/var/www/html/programs/GetInterfaceOut.php";
require_once "/var/www/html/classes/PhpSerialClass.php";

class RotorServer
{
  public $cDB; //
//  private static $user;
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
    set_time_limit(0);
    if (!($port > 4532 && $port < 5000)) {
      //ve9gj
      $port = $rotor * 2 + 4531;
    }
    $this->config["ip"] = $bind_ip;
    $this->config["port"] = $port;
    $this->config["rotor"] = $rotor;
    $this->config["test"] = $test;
 //   $this->doRotorLog(
      echo(
//      $test,
      "Rotor class construct: IP: " .
        $this->config["ip"] .
        " Port: " .
        $this->config["port"] .
        " Rotor: " .
        $rotor
    );
    if ($this->config["port"] !== 0) {
      $this->rotor_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
            if (!$this->rotor_socket){
              echo "ERROR: " . socket_last_error();
            }else{
              echo "SOCKET is OK!";
            };
            echo $this->config['ip'] . PHP_EOL;
            echo $this->config['port'] . PHP_EOL;
      //      $this->config['ip']='localhost';
      //      $this->config['port']=4532;
            $result=socket_connect($this->rotor_socket, $this->config['ip'],$this->config["port"]);
/*
      $address = $this->config["ip"];
      $port= $this->config["port"];
      ($this->rotor_socket = fsockopen(
         $address,
         $port,
         $errnum,
         $errstr,
         0
      )) or die("Failed to Open");
 */   }
//     $this->config["minAz"] = $this->getAzEl("Min Azimuth");
//     $this->config["maxAz"] = $this->getAzEl("Max Azimuth");
//     echo $this->config["minAz"]. "\n";

  }

  public function doRotorLog($utest, $what)
  {
    if ($this->config["test"] == 1) {
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
    $this->doRotorLog(
      1,
      "Set rotor ifc: " . $r . " " . $f . " " . $d);

    SetInterface($r, $f, $d);
  }

  public function sendRotorMessage($what)
  {
    $what = $what . "\n";
//    echo "sendrotormessage: $what\n";

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
//echo "looping, datain: $dataIn az=$tAz\n";
    if ($tStop == 1) {
      $tosend = "S";
      $this->sendRotorMessage($tosend);
      $trig = $this->getRotorMessage(11);
      $this->setRotorInterface($tRotor, "RotorStop", "0");
      return true;
    }
//    echo "rotorcheck: $tRotorCk\n";
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
      $tMin = $this->getAzEl("Min Azimuth"); // $this->config["minAz"];
      $tMax = $this->getAzEl("Max Azimuth");  // $this->config["maxAz"];
      if ($tAz > $tMax) {
        $tAz = $tAz - 360;
      }
      if ($tAz < $tMin) {
        $tAz = $tAz + 360;
      }
      $tosend = $command . " $tAz $tEl";
      usleep(500000);
//      echo "sending: $tosend\n";
      $this->sendRotorMessage($tosend);
      $trig = $this->getRotorMessage(11);
      return true;
    }
    $tosend = "p\n";
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
//    echo "writing: $string\n";

    return socket_write($sock, $string);
  }

  public static function rotor_socket_read(&$sock, $count)
  {
    $read = socket_read($sock, $count);
//    echo "receiving: $read\n";
    return $read;
  }

  function &__get($name)
  {
    return $this->{$name};
  }

  public function getAzEl($what)
  {
    $rotorID = getField($this->config["rotor"], "RotorID", "MySettings");
    $cmd="rotctl -m " .
    $rotorID .
    " -r " . $this->config["ip"] . ":" .
    $this->config["port"] .
    " -u | grep '" .
    $what .
    "'";
//    echo $cmd . "\n";
    $data = shell_exec(
      $cmd
    );    
/*$data = shell_exec(
      "rotctl -m " .
        $rotorID .
        " -r " . $this->config["ip"] . " " .
        $this->config["port"] .
        " -u | grep '" .
        $what .
        "'"
    );
 */
   return trim(substr($data, strpos($data, ":") + 1));
  }
}

?>
