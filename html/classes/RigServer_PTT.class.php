<?php
define("DB_SERVER", "localhost");
require_once "/var/www/html/programs/sqldata.php";
define("DB_USER", $sql_radio_username);
define("DB_PWD", $sql_radio_password);
define("DB_NAME", $sql_radio_database);
require_once "/var/www/html/programs/SetSettingsFunc.php";
require_once "/var/www/html/programs/GetInterfaceOut.php";
require_once "/var/www/html/classes/PhpSerialClass.php";
require_once "/var/www/html/programs/doGPIOPTTFunc.php";
require_once "/var/www/html/programs/disconnectRadioFunc.php";
require_once "/var/www/html/programs/GetBand.php";

class RigServer
{
  public $cDB;
  protected $config;
  protected $rigID;
  public $rig_socket;
  private static $slave;

  /*!	@function	__construct
      @abstract	Creates the socket and starts listening to it.
      @param		string	- IP Address to bind to, NULL for default.
      @param		int	- Port to bind to
      @result		void
     */
  public function __construct($bind_ip, $port, $radio, $test, $vfo, $vfoMode)
  {
    set_time_limit(0);
    $this->config["ip"] = $bind_ip;
    $this->config["port"] = $port;
    $this->config["radio"] = $radio;
    $this->config["test"] = $test;
    $this->config["levelCount"] = 11;
    if ($vfo == "VFOA") {
      $this->config["VFOA1"] = "VFOA";
      $this->config["VFOB1"] = "VFOB";
      $this->config["VFOA"] = " VFOA";
      $this->config["VFOB"] = " VFOB";
    } else {
      $this->config["VFOA1"] = "Main";
      $this->config["VFOB1"] = "Sub";
      $this->config["VFOA"] = " Main";
      $this->config["VFOB"] = " Sub";
    }
    if ($vfoMode == 0) {
      $this->config["VFOA"] = "";
      $this->config["VFOB"] = "";
    }
    $this->setRigInterface($radio, "CommandOut", "");
    $this->setRigInterface($radio, "CommandOutCk", 0);
    $this->config["disconnect"] = false;
    $this->config["inTransmit"] = "off";
    $this->config["splitF"] = "0";
    $this->config["UnkVFO"] = "0";
    $this->config["execTime"] = 0;
    $this->config["disableSplit"] = 0;
    $this->config["pttWait"] = 0;
    $this->config["radioResponse"] = 0;
    $this->config["currentBand"] = 1;
    ($this->cDB = new MysqliDB(DB_SERVER, DB_USER, DB_PWD, DB_NAME)) or
      die("There was a problem connecting to the database.");
    $this->cDB->where("Radio", $radio);
    $val = $this->cDB->getValue("MySettings", "DisableSplitPolling");
    $this->config["disableSplit"] = $val;
    $this->cDB->where("Radio", $radio);
    $val = $this->cDB->getValue("MySettings", "TransmitLevel");
    $this->config["transmitLevel"] = $val;
    $this->cDB->where("Radio", $radio);

    $val = $this->cDB->getValue("MySettings", "PTTCmd");
    $this->config["PTTCmd"] = $val;

    $this->cDB->where("Radio", $radio);
    $val = $this->cDB->getValue("MySettings", "ID");
    $this->rigID = $val;
    $this->cDB->where("Radio", $radio);
    $val = $this->cDB->getValue("MySettings", "PTTCmd");
    $this->config["PTTCmd"] = $val;
    if (strtolower($val) == "default") {
      $this->config["extendedPTT"] = 0;
    } else {
      $this->config["extendedPTT"] = 1;
    }
    $this->cDB->where("Radio", $radio);
    $val = $this->cDB->getValue("MySettings", "PTTCAT");
    $this->config["PTTCAT"] = $val;
    $this->cDB->where("Radio", $radio);
    $val = $this->cDB->getValue("MySettings", "PTTMode");
    $this->config["PTTMode"] = $val;
    $val = $this->cDB->getValue("MySettings", "PTTDelay");
    $this->config["PTTDelay"] = 1000 * $val;
    $this->cDB->where("Radio", $radio);
    $val = $this->cDB->getValue("MySettings", "Keyer");
    $this->config["Keyer"] = $val;

    $val = $this->cDB->getValue("MySettings", "SlavePort");
    $this->config["slavePort"] = $val;
    $val = $this->cDB->getValue("MySettings", "SlaveBaud");
    $this->config["slaveBaud"] = $val;
    $val = $this->cDB->getValue("MySettings", "SlaveCommand");
    $this->config["slaveCommand"] = $val;
    $this->config["slaveDec"] = 0;

    $val = $this->cDB->getValue("Keyer", "WKMode");
    if (($val & 128) == 128) {
      $this->config["CWDeadman"] = 1;
    } else {
      $this->config["CWDeadman"] = 0;
    }
    $this->config["sliderOK"] = 11;
    $this->cDB->where("uID", $radio); //rather than id
    $val = $this->cDB->getValue("Users", "DeadMan");
    $this->config["DeadMan"] = $val * 60; //this is transmit deadman, not ce deadman
    $this->config["CWDeadmanTimer"] = 0;
    $this->config["xStart"] = time();
    $this->config["xLockOut"] = 0;
    $this->config["freq"] = 0;
    $this->config["xmitMtrCount"] = 0;

    if ($this->config["port"] !== 0) {
      ($this->rig_socket = fsockopen(
        "127.0.0.1",
        $this->config["port"],
        $errnum,
        $errstr,
        0
      )) or die("Failed to Open");
    }
    if (
      $this->config["slaveCommand"] == "Kenwood FA" ||
      $this->config["slaveCommand"] == "Kenwood IF"
    ) {
      self::$slave = new PhpSerial();
      $sport = $this->config["slavePort"];
      $what =
        "Slave: " .
        $sport .
        " Baud: " .
        $this->config["slaveBaud"] .
        " Command: " .
        $this->config["slaveCommand"] .
        PHP_EOL;
      error_log(
        date("Y-m-d H:i:s", time()) . " " . $what,
        3,
        "/var/log/rigpi-radio.log"
      );
      system("stty -F " . $sport . " -echo");
      system("stty -F " . $sport . " raw");
      $tSet = self::$slave->deviceSet($sport);
      $tBaud = self::$slave->confBaudRate($this->config["slaveBaud"]);
      $tParity = self::$slave->confParity("none");
      $tLen = self::$slave->confCharacterLength(7);
      $tStop = self::$slave->confStopBits(2);
      $tFlow = self::$slave->confFlowControl("none");
      $tOpen = self::$slave->deviceOpen();
      $what =
        print_r(error_get_last()) .
        "Slave set: " .
        $tSet .
        " open: " .
        $tOpen .
        " baud: " .
        $tBaud .
        " parity: " .
        $tParity .
        " len: " .
        $tLen .
        " stop: " .
        $tStop .
        " flow: " .
        $tFlow .
        PHP_EOL;
      error_log(
        date("Y-m-d H:i:s", time()) . " " . $what,
        3,
        "/var/log/rigpi-radio.log"
      );
    }
  }

  public function doLog($what)
  {
    if ($this->config["test"] == 1) {
      error_log(
        date("Y-m-d H:i:s", time()) . " " . $what,
        3,
        "/var/log/rigpi-radio.log"
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

  public function setRigInterface($r, $f, $d)
  {
    SetInterface($r, $f, $d);
  }

  public function sendRigMessage($what)
  {
    if ($this->config["disconnect"] == false) {
      $this->rig_socket_write_smart($this->rig_socket, $what, "");
      $this->doLog("Socket write: " . $what);
    }
  }

  public function sendSlaveMessage($what)
  {
    if (
      $this->config["slaveCommand"] == "Kenwood FA" ||
      $this->config["slaveCommand"] == "Kenwood IF"
    ) {
      if (self::$slave->sendMessage($what)) {
        $this->doLog("Slave write: " . $what . PHP_EOL);
      } else {
        $this->doLog("Slave write failed" . PHP_EOL);
      }
    }
  }

  public function getRigMessage($count)
  {
    $tR = $this->rig_socket_read($this->rig_socket, $count);
    $this->doLog("Socket read: " . $tR);
    return $tR;
  }

  public function loop_once()
  {
    $tRadio = $this->config["radio"];
    $dataIn = GetOut($tRadio);
    $data = explode("`", $dataIn);
    $tPTT = $data[12];
    $tPTTCk = $data[13];
    $tClose = $data[27];
    if ($tClose > 3) {
      //      disRadio($tRadio, "admin", "");
      return false;
    }
    $tField = "IsAlive"; //careful, messes up multiple connections to same radio
    $tData = "1";
    $this->setRigInterface($tRadio, $tField, $tData);
    $toSend = "t" . $this->config["VFOB"] . "\n"; //.$this->config["VFOB"]."\n";
    if ($tPTTCk == 0) {
      $this->config["pttWait"] = 0;
      $trig = 0;
      if ($this->config["pttWait"] == 0) {
        if ($this->config["PTTMode"] != 2) {
          $this->sendRigMessage($toSend);
          $trig = $this->getRigMessage(11);
        }
        if ($trig == 1) {
          if ($this->config["slaveCommand"] == "Band BCD") {
            exec("sudo /sbin/modprobe -r ftdi_sio");
            exec(
              "sudo /usr/share/rigpi/bitmode " .
                ($this->config["currentBand"] + 16)
            );
            exec("sudo /sbin/modprobe ftdi_sio");
          }
          $this->setRigInterface($tRadio, "Transmit", "1");
          if ($this->config["inTransmit"] == "off") {
            $this->config["inTransmit"] = "on";
            $this->config["pttWait"] = 4;
          }
        } else {
          if ($this->config["slaveCommand"] == "Band BCD") {
            exec("sudo /sbin/modprobe -r ftdi_sio");
            exec(
              "sudo /usr/share/rigpi/bitmode " . $this->config["currentBand"]
            );
            exec("sudo /sbin/modprobe ftdi_sio");
          }
          if ($this->config["PTTMode"] != 2) {
            $this->setRigInterface($tRadio, "Transmit", "0");
            if ($this->config["inTransmit"] == "on") {
              $this->config["inTransmit"] = "off";
            }
          }
        }
      } else {
        $this->config["pttWait"] = $this->config["pttWait"] - 1;
      }
    }
    if ($tPTTCk == 1) {
      //      echo "pttck: " . $tPTTCk . "\n";
      $this->setRigInterface($tRadio, "PTTOutCk", "0");
      //      echo "send: " .
      //        $this->config["PTTCAT"] .
      //        "mode" .
      //      $this->config["PTTMode"];
      if ($this->config["PTTCAT"] == 1 || $this->config["PTTMode"] == 2) {
        if ($tPTT == 1) {
          $this->config["inTransmit"] = "on";
          if ($this->config["PTTDelay"] > 0) {
            //try all trhe reather than figure out which is default
            exec("amixer -q set Master mute");
            exec("amixer -q set PCM mute");
            exec("amixer -q set Speaker mute");
            $this->SetPTT("on");
            $this->setRigInterface($tRadio, "Transmit", "1");
            if ($this->config["extendedPTT"] == 1) {
              $toSend = $this->config["PTTCmd"] . "\n";
              $this->sendRigMessage($toSend);
            } else {
              if ($this->config["PTTMode"] != 2) {
                $toSend = "T" . $this->config["VFOA"] . " 1\n";
                $this->sendRigMessage($toSend);
                $trig = $this->getRigMessage(11);
              }
            }
            //delay for noise burst
            usleep($this->config["PTTDelay"]);
            exec("amixer -q set PCM unmute");
            exec("amixer -q set Master unmute");
            exec("amixer -q set Speaker unmute");
            return true;
          } else {
            $this->setRigInterface($tRadio, "Transmit", "1");
            $this->SetPTT("on");
            if ($this->config["extendedPTT"] == 1) {
              $toSend = $this->config["PTTCmd"] . "\n";
              $this->sendRigMessage($toSend);
            } else {
              if ($this->config["PTTMode"] != 2) {
                $toSend = "T" . $this->config["VFOA"] . " 1\n";
                $this->sendRigMessage($toSend);
                $trig = $this->getRigMessage(11);
              }
            }
          }
          return true;
        } else {
          $this->setRigInterface($tRadio, "Transmit", "0");
          $this->SetPTT("off");
          $this->sendRigMessage("T" . $this->config["VFOA"] . " 0\n");
          $trig = $this->getRigMessage(11);
          $this->config["inTransmit"] = "off";
          $this->config["pttWait"] = 4;
          //          return true;
        }
      }
    }

    /*    if ($this->config["inTransmit"] == "on") {
      $command = $this->config["transmitLevel"];
      if (strlen($command) > 0) {
        if ($this->config["xmitMtrCount"] == 0) {
          $this->config["xmitMtrCount"] = 20;
          $this->sendRigMessage($command . "\n");
          $trig = $this->getRigMessage(11);
          $trig = str_replace("\n", "", $trig);
          $tField = "SMeterIn";
          $tData = $trig;
          $this->setRigInterface($tRadio, $tField, $tData);
        } else {
          $this->config["xmitMtrCount"] = $this->config["xmitMtrCount"] - 1;
        }
        if (
          $this->config["DeadMan"] - (time() - $this->config["xStart"]) < 0 &&
          $this->config["DeadMan"] > 0
        ) {
          $this->config["xLockOut"] = 1;
          $this->setPTT("off");
          $toSend = "T" . $this->config["VFOA"] . " 0\n";
          $this->sendRigMessage($toSend);
          $trig = $this->getRigMessage(11);
          $this->setRigInterface(
            $tRadio,
            "RadioData",
            "Transmit time longer than Deadman timer, restart radio to clear."
          );
        }
      }
      return true;
    } else {
      $this->config["xStart"] = time();
    }
*/
    //"IN" from radio using polling
    $command = "f" . $this->config["VFOA"];
    $this->sendRigMessage($command . "\n");
    $trig = $this->getRigMessage(20);
    $trig = substr($trig, 0, strpos($trig, "\n"));
    $trig = str_replace("\n", "", $trig);
    $tField = "MainIn";
    //			echo $command . " " .$trig . "\n";
    $tData = $trig;
    $tMain = $trig;
    $this->setRigInterface($tRadio, $tField, $tData);
    if ($this->config["freq"] != $tData) {
      $tF1 = str_pad($tData, 11, "0", STR_PAD_LEFT);
    }
    /*      if ($this->config["slaveCommand"] == "Kenwood IF") {
        $tF2 = "IF$tF1     +0000000 002000000 ;";
        //"IF00014023456     +0000000 002000000 ;"
        $this->sendSlaveMessage($tF2);
             } elseif ($this->config["slaveCommand"] == "Band BCD") {
        $band = GetBandFromFrequency($tData);
        switch ($band) {
          case "160":
            $bit = 1;
            break;
          case "80":
            $bit = 2;
            break;
          case "60":
            $bit = 0;
            break;
          case "40":
            $bit = 3;
            break;
          case "30":
            $bit = 4;
            break;
          case "20":
            $bit = 5;
            break;
          case "17":
            $bit = 6;
            break;
          case "15":
            $bit = 7;
            break;
          case "12":
            $bit = 8;
            break;
          case "10":
            $bit = 9;
            break;
          case "6":
            $bit = 10;
            break;
          case "2":
            $bit = 11;
            break;
          case "1.25":
            $bit = 12;
            break;
        }
*/
    if ($this->config["inTransmit"] == "off") {
      $this->config["currentBand"] = $bit;
      /*      if ($this->config["slaveCommand"] == "Band BCD") {
        exec("sudo /sbin/modprobe -r ftdi_sio");
        exec("sudo /usr/share/rigpi/bitmode $bit");
        exec("sudo /sbin/modprobe ftdi_sio");
      }
*/
      //        }
    } else {
      //      $tF2 = "FA" . $tF1 . ";";
      //      $this->sendSlaveMessage($tF2);
    }
    $this->config["freq"] = $tData;
    //    }

    if (ctype_digit($tData)) {
      //Get Sub frequency & split status
      //      if ($this->loop_CheckOnce() == false) {
      //        return true;
      //      }
      //				echo $this->config["disableSplit"]."\n";
      if ($this->config["disableSplit"] == 0) {
        $command = "s" . $this->config["VFOA"]; //get split status and split vfo
        //					$command="s";	//get split status and split vfo
        $this->sendRigMessage($command . "\n");
        $trig = $this->getRigMessage(25);
        $tField = "SplitIn";
        $tData = "0";
        if (strpos($trig, "1") !== false) {
          $tData = "1";
        }
        $this->setRigInterface($tRadio, $tField, $tData);
      } else {
        //unkvfo= 2 after one loop (first loop determines unkvfo)
        $tField = "SplitIn";
        $tData = "0";
        $this->setRigInterface($tRadio, $tField, $tData);
      }
      //now get split frequency but only if split is supported, otherwise substitute

      if ($tData == 1) {
        //split status is ON
        $trig = "";
        if ($this->config["disableSplit"] == "1") {
          //can't poll because of flashing
          $trig = $this->config["splitF"];
          if ($trig == 0) {
            $trig = $tMain;
          }
        } elseif ($this->config["disableSplit"] == 0) {
          $command = "i" . $this->config["VFOB"]; //split supported
          $this->sendRigMessage($command . "\n");
          $trig = $this->getRigMessage(11);
        }
      } else {
        //split status is OFF
        $trig = $this->config["splitF"];
        if ($trig == 0) {
          $trig = $tMain;
        }
      }

      $trig = str_replace("\n", "", $trig);
      //				if ($trig>0){  //getting rprt 10 errors???
      $tData = $trig;
      $tField = "SubIn";
      $this->setRigInterface($tRadio, $tField, $tData); //send freq to RSS
      //				}

      /*      if ($this->loop_CheckOnce() == false) {
        return true;
      }

      //get signal strength
      if ($this->config["xmitMtrCount"] == 0) {
        $this->config["xmitMtrCount"] = 2;
        $command = "l" . $this->config["VFOA"] . " STRENGTH";
        $this->sendRigMessage($command . "\n");
        $trig = $this->getRigMessage(11);
        $trig = str_replace("\n", "", $trig);
        $tField = "SMeterIn";
        $tData = $trig;
        $this->setRigInterface($tRadio, $tField, $tData);
      } else {
        $this->config["xmitMtrCount"] = $this->config["xmitMtrCount"] - 1;
      }
*/ //get mode
      //      if ($this->loop_CheckOnce() == false) {
      //        return true;
      //      }
      $command = "m" . $this->config["VFOA"];
      $this->sendRigMessage($command . "\n");
      $trig = $this->getRigMessage(11);
      $trig = substr($trig, 0, strpos($trig, "\n"));
      $trig = str_replace("\n", "", $trig);
      $tField = "ModeIn";
      $tData = $trig;
      $this->setRigInterface($tRadio, $tField, $tData);
      /*     if ($this->config["levelCount"] > 10) {
        //only check for radio control changes infrequently
        //get AF gain
        $command = "l AF";
        $this->sendRigMessage($command . "\n");
        $trig = $this->getRigMessage(11);
        //        $trig = "0.1\n";echo "trig: $trig\n";
        $trig = substr($trig, 0, strlen($trig) - 1);
        $trig = str_replace("\n", "", $trig);
        //        echo "trig: " . $trig . "\n";
        $tField = "AFGain";
        $tData = ceil($trig * 100);
        $this->config["AFL"] = $tData;
        $this->setRigInterface($tRadio, $tField, $tData);
        //get RF gain
        $command = "l RF";
        $this->sendRigMessage($command . "\n");
        $trig = $this->getRigMessage(11);
        $trig = substr($trig, 0, strpos($trig, "\n"));
        $trig = str_replace("\n", "", $trig);
        $tField = "RFGain";
        $tData = ceil($trig * 100);
        $this->config["RFL"] = $tData;
        $this->setRigInterface($tRadio, $tField, $tData);
        //RF Power max
        $command = "l RFPOWER";
        $this->sendRigMessage($command . "\n");
        $trig = $this->getRigMessage(11);
        $trig = substr($trig, 0, strpos($trig, "\n"));
        $trig = str_replace("\n", "", $trig);
        $tField = "PwrOut";
        $tData = ceil($trig * 100);
        $this->config["PwrL"] = $tData;
        $this->setRigInterface($tRadio, $tField, $tData);
        //Mic gain
        $command = "l MICGAIN";
        $this->sendRigMessage($command . "\n");
        $trig = $this->getRigMessage(11);
        $trig = substr($trig, 0, strpos($trig, "\n"));
        $trig = str_replace("\n", "", $trig);
        //					$this->setRigInterface($tRadio,"RadioData","MicRead: ".$trig." ".$this->config["MicL"]);
        $tField = "MicLvl";
        $tData = ceil($trig * 100);
        $this->config["MicL"] = $tData;
        $this->setRigInterface($tRadio, $tField, $tData);
        $this->config["sliderOK"] = 1;
        $this->config["levelCount"] = 0;
        $this->config["xmitMtrCount"] = 0;
        $this->setRigInterface($tRadio, "CommandOut", "");
        $this->setRigInterface($tRadio, "CommandOutCk", 0);
        usleep(10000);
      } else {
        $this->config["levelCount"] += 1;
      }
    }
*/
      //    return true;
    }
    return true;
  }
  /*
  public function loop_CheckOnce()
  {
    $tRadio = $this->config["radio"];
    $dataIn = GetOut($tRadio);
    $data = explode("`", $dataIn);
    $tF = $data[0];
    $tFCk = $data[1];
    $tS = $data[2];
    $tSCk = $data[3];
    $tM = $data[4];
    $tMCk = $data[5];
    $tSp = $data[6];
    $tSpCk = $data[7];
    $tPTT = $data[12];
    $tPTTCk = $data[13];
    $tComm = $data[14];
    $tCommCk = $data[15];
    $tSlave = $data[16];
    $tSlaveCk = $data[17];
    $tRF = $data[19];
    $tRFCk = $data[20];
    $tAF = $data[21];
    $tAFCk = $data[22];
    $tPwr = $data[23];
    $tPwrCk = $data[24];
    $tMic = $data[25];
    $tMicCk = $data[26];
    if ($this->config["CWDeadman"] == 0) {
      //CWDeadman=0. deadman enabled
      $cwState = exec("gpio read 13"); //cw deadman 15-20 secs w/500 below, use passthru instead of system to mute
      if ($cwState == 0) {
        $this->config["CWDeadmanTimer"] = $this->config["CWDeadmanTimer"] + 1;
        if ($this->config["CWDeadmanTimer"] > 500) {
          system("gpio write 13 1"); //key up
          $this->config["CWDeadmanTimer"] = 0;
        }
      } else {
        $this->config["CWDeadmanTimer"] = 0;
      }
    }

    if ($tCommCk == 1) {
      $this->setRigInterface($tRadio, "CommandOutCk", "0");
      if (strlen($tComm) > 0) {
        $this->setRigInterface($tRadio, "CommandOut", "");
        if ($tComm == "PS0;") {
          //power off kx3
          //         $this->setRigInterface($tRadio, "MainIn", "OFF");
          $tComm = sprintf("\x87") . " 0";
          $tosend = $tComm . "\n";
          $this->sendRigMessage($tosend);
          //          $trig = $this->getRigMessage(11);
          //          $this->config["disconnect"] = true;
          //          return true;
        } elseif ($tComm == "PS1;") {
          //power on kx3
          $tComm = sprintf("\x87") . " 1";
          $tosend = $tComm . "\n";
          $this->sendRigMessage($tosend);
          $trig = $this->getRigMessage(30);
          //         $this->config["disconnect"] = false;
          //          return true;
        } elseif (strtolower($tComm) == "q") {
          $this->setRigInterface($tRadio, "MainIn", "OFF");
          //shutdown rigctl
          $tosend = $tComm . "\n";
          //          echo "disconnect";
          $this->sendRigMessage($tosend);
          $this->config["disconnect"] = true;
          return true;
        } elseif (strpos($tComm, "!SW") !== false) {
          $bunch = explode("!", $tComm);
          $dec2 = 0;
          for ($i = 1; $i < count($bunch); $i++) {
            if ($this->config["slaveCommand"] == "Macro Decimal") {
              $dec = substr($bunch[$i], 2, 1);
              $decval = substr($bunch[$i], 4, 1);
              //								echo $decval;
              $dec1 = 0;
              if ($dec == 0) {
                $dec1 = 0;
              } elseif ($dec == 9) {
                $dec1 = 255;
              } else {
                switch ($dec) {
                  case 0:
                    $dec1 = 0;
                    break;
                  case 1:
                    $dec1 = 1;
                    break;
                  case 2:
                    $dec1 = 2;
                    break;
                  case 3:
                    $dec1 = 4;
                    break;
                  case 4:
                    $dec1 = 8;
                    break;
                  case 5:
                    $dec1 = 16;
                    break;
                  case 6:
                    $dec1 = 32;
                    break;
                  case 7:
                    $dec1 = 64;
                    break;
                  case 8:
                    $dec1 = 128;
                    break;
                }
              }
              if ($dec1 == 0 || $dec1 == 255) {
                //ignore here
              } else {
                if ($decval == 1) {
                  $dec1 = $dec1 | $tSlave;
                  //										echo "1".$dec1;
                } else {
                  if (($dec1 & $tSlave) == 1) {
                    $dec1 = $dec1 ^ $tSlave;
                    //											echo "2".$dec1;
                  } else {
                    //											echo "3".$dec1;
                    $dec1 = $tSlave;
                  }
                }
              }
              $dec2 = $dec1 | $dec2;
            }
          }
          $this->setRigInterface($tRadio, "Slave", $dec2);
        } elseif (strpos($tComm, "*") !== false) {
          //direct (radio dependent) 'w' commands, 0w=skip read, 1w= no skip.level
          $skipRead = false;
          $bunch = explode("*", $tComm);
          for ($i = 1; $i < count($bunch); $i++) {
            if (strpos($bunch[$i], "w ") !== false) {
              if (substr($bunch[$i], 0, 1) == 1) {
                $skipRead = false;
              } else {
                $skipRead = true;
              }
              $tComm = substr($bunch[$i], 1);
              $tosend = $tComm . "\n";
              $this->sendRigMessage($tosend);

              if ($skipRead == false) {
                $trig = $this->getRigMessage(2000);
              } else {
                $trig = ""; //$tComm;
              }
              if (
                strstr($trig, "RPRT -1") ||
                ($skipRead == false && strlen($trig) == 0)
              ) {
                $oops = "<br>There is a problem with the w macro $tComm. 
                    Confirm that it adheres to the Hamlib rigctld api for w and the desired radio command.<br><br>";
                $this->setRigInterface($tRadio, "RadioData", $oops);
                sleep(1); //when rigctld gets a bad command, it blasts several throusand characters of text...
              } else {
                if (strlen($trig) > 0) {
                  $oops = "<br>Radio says: " . $trig . "<br><br>";
                  //										$this->setRigInterface($tRadio,"RadioData",$oops);
                  $trig = $this->getRigMessage(10000);
                }
              }
            } else {
              $tosend = $bunch[$i] . "\n";
              $this->sendRigMessage($tosend);
              $trig = $this->getRigMessage(10000);
              if (strstr($trig, "RPRT -1")) {
                $oops = "<br>There is a problem with the custom macro $tComm. 
                  Confirm that it adheres to the Hamlib rigctld api.<br><br>";
                $this->setRigInterface($tRadio, "RadioData", $oops);
                sleep(1); //when rigctld gets a bad command, it blasts several throusand characters of text...
              } else {
                if (
                  strlen($trig) > 0 &&
                  !$trig == "RPRT 0" &&
                  $this->config["radioResponse"] == 1
                ) {
                  $oops = "<br>Radio says: " . $trig . "<br><br>";
                  $this->setRigInterface($tRadio, "RadioData", $oops);
                }
              }
            }
          }
          return true;
        }
      }   //      }
    //    }
    //mode from RSS
    if ($tMCk == 1 && $tFCk == 1) {
      //mode has been updated by RSS
      $this->setRigInterface($tRadio, "ModeOutCk", "0");
      $command = "M" . $this->config["VFOA"];
      $tosend = $command . " " . $tM;
      $tosend = $tosend . " 0\n";
      $this->sendRigMessage($tosend);
      $response = $this->getRigMessage(24);
      $this->setRigInterface($tRadio, "MainOutCk", "0");
      $tField = "MainIn";
      $tData = $tF; //this is OUT
      $this->setRigInterface($tRadio, $tField, $tData);
      $command = "F" . $this->config["VFOA"];
      //					$command="F";
      $tosend = $command . " " . $tF;
      $tosend = $tosend . "\n";
      $this->sendRigMessage($tosend);
      $response = $this->getRigMessage(11);
    }
    if ($tMCk == 1) {
      //mode has been updated by RSS
      $this->setRigInterface($tRadio, "ModeOutCk", "0");
      $command = "M" . $this->config["VFOA"];
      $tosend = $command . " " . $tM;
      $tosend = $tosend . " 0\n";
      $this->sendRigMessage($tosend);
      usleep(100000); //setting mode with more than one browser causes split issues w/o delay
      $response = $this->getRigMessage(24);
    }
    if ($tFCk == 1) {
      //main frequency has been updated by RSS Tuner not radio, "OUT to radio"
      $this->setRigInterface($tRadio, "MainOutCk", "0");
      $tField = "MainIn";
      $tData = $tF; //this is OUT
      $this->setRigInterface($tRadio, $tField, $tData);
      $command = "F" . $this->config["VFOA"];
      //					$command="F";
      $tosend = $command . " " . $tF;
      $tosend = $tosend . "\n";
      $this->sendRigMessage($tosend);
      $response = $this->getRigMessage(11);
      //					$tF1=str_pad($tData,11,"0",STR_PAD_LEFT);
      //					$this->sendSlaveMessage("FA".$tF1.";");
    }
    if ($tSCk == 1) {
      //split frequency has been updated by RSS
      $this->setRigInterface($tRadio, "SubOutCk", "0");
      //     $tosend = "I" . $this->config["VFOA"] . " " . $tS; //normal set split freq
      /*      $tosend = "V" . " VFOB"; //$this->config["VFOB"];
      $tosend = $tosend . "\n";
      $this->sendRigMessage($tosend);
      $response = $this->getRigMessage(11);
      $tosend = "F" . " " . $tS;
      $tosend = $tosend . "\n";
      $this->sendRigMessage($tosend);
      $response = $this->getRigMessage(11);
      $tosend = "V" . " VFOA"; //$this->config["VFOA"];
      $tosend = $tosend . "\n";
      $this->sendRigMessage($tosend);
      $response = $this->getRigMessage(11);
 //      $tosend = "S" . $this->config["VFOA"] . " 0 " . $this->config["VFOA1"];
      //      $tosend = $tosend . "\n";
      //      $this->sendRigMessage($tosend);
      //      $response = $this->getRigMessage(11);
      $tosend = "I" . " " . $tS;
      $tosend = $tosend . "\n";
      $this->sendRigMessage($tosend);
      $response = $this->getRigMessage(11);
      $this->config["splitF"] = $tS; //save split freq for use when no poll or no split provided by hamlib
      $tField = "SubIn";
      $tData = $tS;
      $this->setRigInterface($tRadio, $tField, $tData);
    } //switches
    //split
    if ($tSpCk == 1) {
      //split status changed by RSS
      $this->setRigInterface($tRadio, "SplitOutCk", "0");
      if ($tSp == 1) {
        $command = "S" . $this->config["VFOA"] . " 1 " . $this->config["VFOB1"];
        $tosend = $command;
      } else {
        $command = "S" . $this->config["VFOA"] . " 0 " . $this->config["VFOA1"];
        $tosend = $command;
      }
      $tosend = $tosend . "\n";
      $this->sendRigMessage($tosend);
      $response = $this->getRigMessage(11);
    }
    if ($tSlaveCk == 1) {
      $this->setRigInterface($tRadio, "SlaveCk", "0");
      exec("sudo /sbin/modprobe -r ftdi_sio");
      exec("sudo /usr/share/rigpi/bitmode $tSlave");
      exec("sudo /sbin/modprobe ftdi_sio");
    }
    if ($this->config["sliderOK"] == 1) {
      if ($tRFCk == 1) {
        $this->setRigInterface($tRadio, "RFGainCk", "0");
        $tRF = ceil($tRF);
        $tRF = $tRF / 100;
        if ($tRF > 1) {
          $tRF = 1;
        }
        $command = "L RF " . $tRF;
        $tosend = $command . "\n";
        $this->sendRigMessage($tosend);
        $response = $this->getRigMessage(11);
        $this->config["levelCount"] = 0;
      }
      if ($tAFCk == 1) {
        $this->setRigInterface($tRadio, "AFGainCk", "0");
        $tAF = ceil($tAF);
        $tAF = $tAF / 100;
        if ($tAF > 1) {
          $tAF = 1;
        }
        $command = "L AF " . $tAF;
        $tosend = $command . "\n";
        $this->sendRigMessage($tosend);
        $response = $this->getRigMessage(11);
        //        echo "taf: " . $tAF;
        $this->config["levelCount"] = 0;
      }
      if ($tPwrCk == 1) {
        $this->setRigInterface($tRadio, "PwrOutCk", "0");
        $tPwr = ceil($tPwr);
        $tPwr = $tPwr / 100;
        if ($tPwr > 1) {
          $tPwr = 1;
        }
        $command = "L RFPOWER " . $tPwr;
        $tosend = $command . "\n";
        $this->sendRigMessage($tosend);
        $response = $this->getRigMessage(11);
        $this->config["levelCount"] = 0;
      }
      if ($tMicCk == 1) {
        $this->setRigInterface($tRadio, "MicLvlCk", "0");
        $tMic = ceil($tMic);
        $tMic = $tMic / 100;
        if ($tMic > 1) {
          $tMic = 1;
        }
        $command = "L MICGAIN " . $tMic;
        $tosend = $command . "\n";
        $this->sendRigMessage($tosend);
        $response = $this->getRigMessage(11);
        $this->config["levelCount"] = 0;
      }
    }
    return true;
  }
}
*/
  public function infinite_loop()
  {
    $test = true;
    do {
      $test = $this->loop_once();
      usleep(100000);
    } while ($test);
    echo "ALL DONE";
  }

  /*		public function infinite_Check()
    {
      $test = true;
      do
      {
        $test = $this->loop_CheckOnce();
        usleep(100000);
      }
      while($test);
      echo "ALL DONE";
    }
*/
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

  public static function rig_socket_write_smart(&$sock, $string, $crlf = "")
  {
    if ($crlf) {
      $string = "{$string}{$crlf}";
    }
    $ret = fwrite($sock, $string);
    return $ret;
  }

  public static function rig_socket_read(&$sock, $count)
  {
    stream_set_timeout($sock, 5);
    $read = fread($sock, $count);
    return $read;
  }

  function &__get($name)
  {
    return $this->{$name};
  }

  public function SetPTT($on)
  {
    if ($this->config["PTTMode"] > 0) {
      //Hardware PTT ON for transmit when 1, GPIO = 2, ON when radio connected = 3, OFF = 0
      if ($this->config["Keyer"] == "RigPi Keyer") {
        $this->config["inTransmit"] = $on;
        doPTT($on);
      }
    }
  }
}

?>
