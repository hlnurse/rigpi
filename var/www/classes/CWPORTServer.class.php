<?php
require_once "/var/www/html/programs/GetSettingsFunc.php";
require_once "/var/www/html/programs/GetInterfaceFunc.php";
require_once "/var/www/html/programs/SetSettingsFunc.php";
require_once "/var/www/html/programs/GetInterfaceOut.php";
require_once "/var/www/html/classes/PhpSerialClass.php";
require_once "/var/www/html/classes/TCPServer.class.php";
require_once "/var/www/html/classes/MysqliDb.php";
//define("DB_SERVER", "localhost");
//define("DB_USER", "ham");
//define("DB_PWD", "7388");
//define("DB_NAME", "station");

class CWPortServer
{
  private $cDB;
  private static $user;
  private static $winkey;
  private static $winkeyOK = true;
  public $cwSpeed = "KS" . 0x20 . ";";
  private static $winkey_WPM_Speed;
  private static $cwBuffer;
  protected $config;
  public $cw_socket;
  public $cw_TCP_Client;
  public $cw_TCP_Socket;
  public $keyerPort;
  public $busy = 0;

  public $max_read = 1024;

  public function __construct(
    $bind_ip,
    $port,
    $kport,
    $ktype,
    $radio,
    $user,
    $trans
  ) {
    /*  		$radio=1;
      $bind_ip='localhost';
      $ktype='rpk';
      $user='admin';
*/
    //    $kport = "/dev/ttyUSB1";
    ($this->cDB = new MysqliDB(DB_SERVER, DB_USER, DB_PWD, DB_NAME)) or
      die("There was a problem connecting to the database.");
    $this->cDB->where("Radio", $radio);
    $val = $this->cDB->getValue("Keyer", "WKPTT");
    SetInterface($radio, "CWOutCk", 0);
    $this->config["pttOn"] = $val;
    set_time_limit(0);
    $this->config["full"] = "0";
    $this->config["ip"] = $bind_ip;
    $this->config["radio"] = $radio;
    $this->config["port"] = $port;
    $this->config["tcpport"] = 30001;
    $this->config["type"] = $ktype;
    $this->config["user"] = $user;
    $this->config["keyerPort"] = $kport;
    $this->config["portTrans"] = $trans;
    if ($this->config["portTrans"] == 0) {
      if ($ktype == "rpk") {
        $kport = "ttyS0";
        system("stty -F /dev/" . $kport . " -echo");
        system("stty -F /dev/" . $kport . " raw");
        self::$winkey = new PhpSerial();
        $this->setWinkeyDevice("/dev/" . $kport);
        $this->setWinkeyBaud(1200);
        $this->setWinkeyParity("none");
        $this->setWinkeyBits(8);
        $this->setWinkeyStop(1);
        $this->setWinkeyFlow("none");
        $this->setWinkeyOpen();
        usleep(100000);
        $this->InitWinKeyer();
      } elseif ($ktype == "cat") {
        //				$this->debug('CAT used');
      } elseif ($ktype == "wkr") {
        //        if (strstr($kport, "tty")) {
        system("stty -F " . $kport . " -echo");
        system("stty -F " . $kport . " raw");
        self::$winkey = new PhpSerial();
        usleep(1000000);
        $this->setWinkeyDevice($kport);
        //        echo "phps set winkey to: " . $kport . "\n";
        $this->setWinkeyBaud(1200);
        $this->setWinkeyParity("none");
        $this->setWinkeyBits(8);
        $this->setWinkeyStop(1);
        $this->setWinkeyFlow("none");
        $this->setWinkeyOpen();
        $this->InitWinKeyer();
        //        }
      }
    }
    $lanIP1 = trim(str_replace("\n", "", shell_exec("hostname -I")));
    $lan1 = "";
    $lan2 = "";
    if (strpos($lanIP1, " ") > 0) {
      $lan1 = substr($lanIP1, 0, strpos($lanIP1, " "));
      $lan2 = substr($lanIP1, strpos($lanIP1, " "));
    } else {
      $lan1 = $lanIP1;
      $lan2 = "not found";
    }
    $lan3 = shell_exec("curl ifconfig.me");
    $this->config["tcpip"] = $lan1;

    //    if ($this->config["port"] != 0 && $this->config["type"] == "cat") {
    ($this->cw_socket = fsockopen(
      $this->config["ip"],
      $this->config["port"],
      $errnum,
      $errstr,
      0
    )) or die("Failed to Open");
    //    }
    //    echo $tCWOutWKCk . "\n";///////////////////////////////////////
    ($this->cw_TCP_Client = fsockopen(
      $this->config["tcpip"],
      $this->config["tcpport"],
      $errnum,
      $errstr,
      0
    )) or die("Failed to Open cw tcp connection");

    //    echo "phps sock " . $this->cw_TCP_Client . "\n";
    //    $this->cw_TCP_Client = new SocketServerClient($this->cw_TCP_Socket, 1);
  }

  public function microtime_float()
  {
    list($usec, $sec) = explode(" ", microtime());
    return (float) $usec + (float) $sec;
  }

  public function loop_once()
  {
    //    echo "CW loop\n";
    $tRadio = $this->config["radio"];
    $dataIn = GetCWOut($tRadio);
    $data = explode(chr(96), $dataIn);
    //    if (strlen($data[5]) > 0) {
    //      echo "datain: " . $dataIn . "\n";
    //    }
    $tCW = $data[0]; //cwout, FROM keyer
    $tCWCk = $data[1]; //cwout from keyer ready
    $tCWIn = strtoupper($data[2]); //cwin, TO keyer
    $tCWOutWK = strtoupper($data[5]); //cwin, TO keyer
    $tCWOutWKCk = $data[6]; //cwin, TO keyer

    if ($tCWOutWKCk == 1) {
      SetInterface($this->config["radio"], "CWOutWKCk", 0);
      if (strlen($tCWOutWK) == 0) {
        //        SetInterface($this->config["radio"], "CWOutWK", "");
        return true;
      }
      echo "phps CWOutWKCk: " .
        $tCWOutWKCk .
        " " .
        $tCWOutWK .
        " " .
        bin2hex($tCWOutWK) .
        "\n";
      //      $tCWOutWKCk = 0;
      if (strlen($tCWOutWK) > 0) {
        //        echo "phps sending to keyer: KR" . $tCWOutWK . ";\n;";
        $this->sendWinkeyMessage($tCWOutWK);
        /*        $this->cw_socket_write_smart(
          $this->cw_TCP_Client,
          "KR" . $tCWOutWK . ";",
          ""
        );
*/
        //        $readT = $this->TCP_socket_read($this->cw_TCP_Client, 10);
        //        echo "TCP read: " . $readT . "\n";
        //        SetInterface($this->config["radio"], "CWOutWK", "");
        return true;
      }
    }

    if (strlen($tCWIn) > 0) {
      //      echo "phps CW Port Server got data to send: " . $tCWIn . "\n";
      $tH = $this->getCWBuffer() . $tCWIn;
      //check for < and > to catch hex control characters.  See Winkeyer3 API.
      $cancel = false;
      while ($cancel == false) {
        if (strpos($tH, "<") > -1 && strpos($tH, ">") > 0) {
          $tHStart = strpos($tH, "<");
          $tHVal = substr($tH, $tHStart + 1, 2);
          $tIns = chr(hexdec($tHVal));
          $tHOut =
            substr($tH, 0, $tHStart) .
            $tIns .
            substr($tH, strpos($tH, ">") + 1);
          $tH = $tHOut;
        } else {
          $cancel = true;
        }
      }
      $cancel = false;
      while ($cancel == false) {
        if (strpos($tH, "!") > 0) {
          $tH =
            substr($tH, 0, strpos($tH, "!") - 1) .
            substr($tH, strpos($tH, "!") + 1);
        } else {
          $cancel = true;
        }
      }
      $this->setCWBuffer($tH);
    }
    $tCWChangeCk = $data[3];
    $tCWInitCk = $data[4];

    //cw
    $tSend = $this->getCWBuffer();
    if (strlen($tSend) > 0 && $tCWCk == "0") {
      //TO keyer plus $tCWCk is used as Hold
      if ($this->config["type"] == "cat") {
        if (strlen($tSend) < 40) {
          $this->setCWBuffer("");
        } else {
          $this->setCWBuffer(substr($tSend, 9));
          $tSend = substr($tSend, 0, 10);
        }
        $this->sendCATMessage($this->config["port"], "b", $tSend);
      } else {
        if ($this->config["full"] == "1") {
          //          echo "winkere full\n";
          //do nothing
        } else {
          //         echo "phps setting up to winkeye: " . $tSend . "\n";
          if (strlen($tSend) < 40) {
            $this->setCWBuffer("");
          } else {
            $this->setCWBuffer(substr($tSend, 9));
            $tSend = substr($tSend, 0, 9);
          }
          if (
            $this->config["type"] == "rpk" ||
            $this->config["type"] == "wkr"
          ) {
            $this->sendWinkeyMessage($tSend);
          }
        }
      }
    }
    if ($tCWInitCk == 1 && $this->config["portTrans"] == 0) {
      SetInterface($tRadio, "CWInitCk", "0");
      if ($this->config["type"] == "rpk" || $this->config["type"] == "wkr") {
        $this->InitWinKeyer();
      } elseif ($this->config["type"] == "cat") {
        //init goes here
      }
    }
    if ($tCWChangeCk == 1) {
      SetInterface($tRadio, "CWChangeCk", "0");
      $cwS = GetField($tRadio, "WKSpeed", "Keyer");
      //     echo "after changeck: " . bin2hex($cwS) . "\n";
      if ($this->config["type"] == "rpk" || $this->config["type"] == "wkr") {
        $this->setWinkeySpeed(chr($cwS));
      } elseif ($this->config["type"] == "cat") {
        $this->sendCATMessage($this->config["port"], "L", "KEYSPD $cwS\n");
      }
    }

    if (
      ($this->config["type"] == "rpk" || $this->config["type"] == "wkr") &&
      $this->config["portTrans"] == 0
    ) {
      //      $this->sendWinkeyMessage(chr(0x07));
      //      $this->sendWinkeyMessage(chr(0x15));
      $wk_s = $this->getWinkeyMessage($this->cw_socket, 1);
      if (strlen($wk_s) > 0) {
        //        echo "phps  get winkey: " . $wk_s . " " . bin2hex($wk_s) . "\n";
        for ($i = 0; $i < strlen($wk_s); $i++) {
          $wk1 = substr($wk_s, $i, 1);
          //         echo "phps  next: " .
          //            $i .
          //            " " .
          //           $wk1 .
          //            " wk1: " .
          //           bin2hex($wk1) .
          //            "\n";
          if (ord($wk1) < 0x80) {
            //           echo "phps  < 128: " . $wk1 . " " . bin2hex($wk1) . "\n";
            if (strlen($wk1) > 0) {
              SetCWOutAdditive($tRadio, $wk1);
              //              echo "phps sending to ip: " . bin2hex($wk1) . "\n";
              $this->cw_socket_write_smart(
                $this->cw_TCP_Client,
                "KR" . $wk1 . ";",
                ""
              );
              //              echo "cwp read start\n";
              /*              $readT = $this->TCP_socket_read($this->cw_TCP_Client, 0);
              echo "cwp read end\n";
              if (strstr($readT, "KR")) {
                $this->sendWinkeyMessage($readT);
*/

              //              }

              //              echo "phps TCP read: " . bin2hex($readT) . "\n";
            }
          } elseif ((intval($wk1) & 0xc0) == 0x80) {
            //            echo "phps  80 speed: " . bin2hex($wk1);
            //            $wk = ord($wk1) & 0x3f;
            //            echo "speed: " . bin2hex(hexdec($wk)) . "\n";
            //            SetKeyerSpeed($tRadio, "WKPot", $wk);
            //            $newS = (0x80 or chr($wk));
            //           echo "wk1: " . $wk1 . " " . bin2hex($wk1) . "\n";
            /*            $this->cw_socket_write_smart(
              $this->cw_TCP_Client,
              "KR" . $wk1 . ";",
              ""
            );
*/
          } elseif ((ord($wk1) & 0xc0) == 0xc0) {
            //            echo "phps  c0: " . bin2hex($wk1);
            //buffer 3/4 full
            if ((ord($wk1) & 0x01) == 0x01) {
              $this->config["full"] = "1";
            } else {
              $this->config["full"] = "0";
            }
            //            if ((ord($wk1) & 0x04) == 0x04) {
            echo "phps  4: " . bin2hex($wk1) . "\n";
            $this->cw_socket_write_smart(
              $this->cw_TCP_Client,
              "KR" . $wk1 . ";",
              ""
            );
            //busy (PTT)
            //              SetInterface($tRadio, "CWBusy", "1");
          } else {
            ///              echo "phps  only c0: " . bin2hex($wk1);
            /*              $this->cw_socket_write_smart(
                $this->cw_TCP_Client,
                "KR" . $wk1 . ";",
                ""
              );
*/
            //busy (PTT)
            //              SetInterface($tRadio, "CWBusy", "0");
          }
        }
      }
    }
    //    }

    return true;
  }

  public function infinite_loop()
  {
    $test = true;
    do {
      usleep(100000);
      $test = $this->loop_once();
    } while ($test);
  }

  public static function debug($text)
  {
    echo "{$text}\r\n";
  }

  public function &__get($name)
  {
    return $this->{$name};
  }

  public function sendRigMessage($what)
  {
    $this->cw_socket_write_smart($this->cw_socket, $what, "");
  }

  public function getRigMessage($count)
  {
    stream_set_blocking($sock, false);
    stream_set_timeout($sock, 1);
    $info = stream_get_meta_data($sock);

    while (!feof($sock) && !$info["timed_out"]) {
      $data .= fgets($sock, 4096);
      $info = stream_get_meta_data($sock);
      ob_flush;
      flush();
    }
    return $data;
    //   return $this->cw_socket_read($this->cw_socket, $count);
  }

  public static function cw_socket_write_smart(&$sock, $string, $crlf = "")
  {
    if ($crlf) {
      $string = "{$string}{$crlf}";
    }
    return fwrite($sock, $string);
  }

  public static function cw_socket_read(&$sock, $count)
  {
    stream_set_blocking($sock, false);
    stream_set_timeout($sock, 1);
    $info = stream_get_meta_data($sock);

    while (!feof($sock) && !$info["timed_out"]) {
      $data .= fgets($sock, 4096);
      $info = stream_get_meta_data($sock);
      ob_flush;
      flush();
    }
    return $data;
    //    stream_set_timeout($sock, 2);
    //    $read = fread($sock, $count);
    //    return $read;
  }

  public static function TCP_socket_write(&$sock, $string, $crlf = "")
  {
    //    echo "phps cw writing: " . $string . "\n";
    if ($crlf) {
      $string = "{$string}{$crlf}";
    }
    return fwrite($sock, $string);
    //    return socket_write_smart($sock, $string, $crlf);
  }

  public static function TCP_socket_read(&$sock, $count)
  {
    $rT = "";
    $i = 0;
    if ($count > 0) {
      $rT = fread($sock, $count);
    } else {
      do {
        //       echo "i: " . $i . "\n";
        $rT .= fread($sock, 128);
      } while (($i += 128) === strlen($rT));
    }
    //    echo "phps cw reading..." . $rT . "\n";
    return $rT;
    //    return fgets($sock);
    // return socket_write_smart($sock, $string, $crlf);
  }

  //////////// Keyer
  public function setCWSpeed($which)
  {
    $this->cwSpeed = $which;
  }

  public function setKeyerPort($which)
  {
    $this->keyerPort = $which;
  }

  public function getKeyerPort()
  {
    return $this->keyerPort;
  }

  public function setCWBuffer($which)
  {
    $this::$cwBuffer = $which;
  }

  public function getCWBuffer()
  {
    return $this::$cwBuffer;
  }

  public function setWinkeyDevice($which)
  {
    //   echo "set winkey dev: " . $which . "\n";
    //    if ($this::$winkeyOK) {
    if (self::$winkey->deviceSet($which) == false) {
      $this::$winkeyOK = false;
    }
    //    }
  }

  public function setWinkeyBaud($which)
  {
    if ($this::$winkeyOK) {
      self::$winkey->confBaudRate($which);
    }
  }

  public function setWinkeyPTTOn($which)
  {
    if ($this::$winkeyOK) {
      self::$winkey->confBaudRate($which);
    }
  }

  public function setWinkeyParity($which)
  {
    if ($this::$winkeyOK) {
      self::$winkey->confParity($which);
    }
  }

  public function setWinkeyBits($which)
  {
    if ($this::$winkeyOK) {
      self::$winkey->confCharacterLength($which);
    }
  }

  public function setWinkeyStop($which)
  {
    if ($this::$winkeyOK) {
      self::$winkey->confStopBits($which);
    }
  }

  public function setWinkeyFlow($which)
  {
    if ($this::$winkeyOK) {
      self::$winkey->confFlowControl($which);
    }
  }

  public function setWinkeyOpen()
  {
    if ($this::$winkeyOK) {
      self::$winkey->deviceOpen();
    }
  }

  public function sendCATMessage($port, $com, $what)
  {
    exec("php /var/www/html/cwCAT.php $port $com '$what' > /dev/null 2>&1 &");
  }

  public function sendWinkeyMessage($what)
  {
    //    echo "send: " . $what . "\n";
    if ($this::$winkeyOK) {
      //      echo "sending to winkeyer: " . $what . " " . bin2hex($what) . "\n";
      self::$winkey->sendMessage($what);
    }
  }
  /* public static function getWinkeyMessage(&$sock, $count)
  {
    stream_set_timeout($sock, 5);
    $read = fread($sock, $count);
    //    ob_clean();
    return $read;
  }
*/

  public function getWinkeyMessage(&$sock, $count)
  {
    //    $count = 8;
    //    echo "count: " . strlen($count) . " " . bin2hex($count) . "\n";
    if ($this::$winkeyOK) {
      $ts = self::$winkey->readPort($count);
      return $ts;
    }
  }

  public function setWinkeySpeed($what)
  {
    if ($this::$winkeyOK) {
      $this::$winkey_WPM_Speed = $what;
      self::$winkey->sendMessage(chr(2) . $what);
    }
  }

  public function getWinkeySpeed()
  {
    if ($this::$winkeyOK) {
      return $this::$winkey_WPM_Speed;
    }
  }

  public function InitWinKeyer()
  {
    //Winkeyer ADMIN constants
    $winkey_Admin = chr(0x00);
    $winkey_WK3_Mode = chr(20); //sets WK3 mode
    $winkey_Reset = chr(0x01);
    $winkey_Host_Open = chr(0x02);
    $winkey_Admin_Host_Close = chr(0x03);
    $winkey_Echo_Test = chr(0x04);
    $winkey_Readback_VCC = chr(0x21);

    //Winkeyer commands
    $winkey_Sidetone_Control = chr(0x01); //+<nn>
    $winkey_Get_Speed_Pot = chr(0x07);
    $winkey_Request_Status = chr(0x15);
    //Winkeyer load defaults
    $tRadio = $this->config["radio"];
    $winkey_Mode_Register = chr(14) . chr(GetField($tRadio, "WKMode", "Keyer"));
    $winkey_Speed = chr(2) . chr(GetField($tRadio, "WKSpeed", "Keyer"));
    $winkey_Sidetone_Frequency =
      chr(1) . chr(GetField($tRadio, "WKSidetone", "Keyer")); //800 Hz
    $winkey_Weight = chr(3) . chr(GetField($tRadio, "WKWeight", "Keyer")); //50
    $winkey_Leadin_Time =
      chr(4) .
      chr(GetField($tRadio, "WKLeadin", "Keyer")) .
      chr(GetField($tRadio, "WKTail", "Keyer"));
    $winkey_Minimum_WPM =
      chr(5) .
      chr(GetField($tRadio, "WKMinWPM", "Keyer")) .
      chr(GetField($tRadio, "WKWPMRange", "Keyer")) .
      chr(0); //5 WPM
    $winkey_X2_Mode =
      chr(0) . chr(22) . chr(GetField($tRadio, "WKX2Mode", "Keyer"));
    $winkey_Key_Compensation =
      chr(0x11) . chr(GetField($tRadio, "WKKeyComp", "Keyer"));
    $winkey_Farnsworth_WPM =
      chr(0x0d) . chr(GetField($tRadio, "WKFarnsworth", "Keyer"));
    $winkey_Paddle_Setpoint =
      chr(0x12) . chr(GetField($tRadio, "WKPaddleSet", "Keyer"));
    $winkey_DitDah_Ratio =
      chr(0x17) . chr(GetField($tRadio, "WKDitDahRatio", "Keyer")); //50
    $winkey_Pin = GetField($tRadio, "WKPinConf", "Keyer");
    if (hexdec($this->config["pttOn"] & $winkey_Pin) == 1) {
      $winkey_Pin = $winkey_Pin & 0xfe;
    }
    $winkey_Pin_Configuration = chr(9) . chr($winkey_Pin);
    $winkey_X1_Mode =
      chr(0) . chr(15) . chr(GetField($tRadio, "WKX1Mode", "Keyer"));
    $this->sendWinkeyMessage($winkey_Admin . $winkey_Host_Open);
    usleep(100000);
    $this->sendWinkeyMessage($winkey_Admin . $winkey_WK3_Mode);
    usleep(100000);
    $this->sendWinkeyMessage($winkey_Mode_Register);
    //    echo "sent: " . bin2hex($winkey_Mode_Register) . "\n";
    $this->sendWinkeyMessage($winkey_Speed);
    $this->sendWinkeyMessage($winkey_Sidetone_Frequency);
    $this->sendWinkeyMessage($winkey_Weight);
    $this->sendWinkeyMessage($winkey_Leadin_Time);
    $this->sendWinkeyMessage($winkey_Minimum_WPM);
    $this->sendWinkeyMessage($winkey_X2_Mode);
    $this->sendWinkeyMessage($winkey_Key_Compensation);
    $this->sendWinkeyMessage($winkey_Farnsworth_WPM);
    $this->sendWinkeyMessage($winkey_Paddle_Setpoint);
    $this->sendWinkeyMessage($winkey_DitDah_Ratio);
    $this->sendWinkeyMessage($winkey_Pin_Configuration);
    $this->sendWinkeyMessage($winkey_X1_Mode);
    usleep(100000);

    $tInit = $winkey_Get_Speed_Pot . $winkey_Request_Status;
    $this->sendWinkeyMessage($tInit);
    usleep(100000);
    $this->sendWinkeyMessage(chr(0x18) . chr($this->config["pttOn"]));
    //    echo "init done\n";
    $this->sendWinkeyMessage("E");
  }
}

?>
