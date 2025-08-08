<?php

require_once "/var/www/html/classes/TCPServer.class.php";
require_once "/var/www/html/programs/sqldata.php";
require_once "/var/www/html/programs/SetSettingsFunc.php";
require_once "/var/www/html/programs/GetInterfaceOut.php";

if (isset($argv[1])){
  $tUser = $argv[1];
}else{
  $tUser="admin";   //only if user not specified
}
if (isset($argv[2])){
  $tMyRadio=$argv[2];
}else{  
  $tMyRadio="radio1";
}
if (isset($argv[3])){
  $tMyPort=$argv[3];
}else{
  $tMyPort = "30001";
}
$tMyRadio = substr($tMyRadio, strlen($tMyRadio) - 1);

$gotCtrl = 0;
$cwServer;

$tMyIP = shell_exec("hostname -I");
if (strpos($tMyIP, " ") > 0) {
  $tMyIP = substr($tMyIP, 0, strpos($tMyIP, " "));
}
$TCPServer = new TCPServer($tMyIP, $tMyPort, $tUser, $tMyRadio);
echo "IP: $tMyIP port: $tMyPort\n";
//echo "TCPServer started\n";
$TCPServer->max_clients = 10; // Allow no more than 10 people to connect at a time
$TCPServer->hook("CONNECT", "handle_connect"); // Run handle_connect every time someone connects
$TCPServer->hook("INPUT", "handle_input"); // Run handle_input whenever text is sent to the server
$TCPServer->hook("SPEED", "handle_speed"); // Run handle_input whenever text is sent to the server
$TCPServer->hook("ECHO", "handle_echo"); // Run handle_input whenever text is sent to the server
echo "starting infinite loop\n";
$TCPServer->infinite_loop(); // Run Server Code Until Process is terminated.
//$TCPServer->read_loop();
$splitState = 0;

function microtime_float()
{
  list($usec, $sec) = explode(" ", microtime());
  return (float) ($usec = (float) $sec);
}

function handle_connect(&$server, &$client, $input, $call, $radio)
{
  $cwServer = $server;
  //  echo "radio: " . $radio . " call: " . $call . " connected!" . "\n";
}

function ascii2hex($ascii)
{
  $hex = "";
  for ($i = 0; $i < strlen($ascii); $i++) {
    $byte = strtoupper(dechex(ord($ascii[$i])));
    $byte = str_repeat("0", 2 - strlen($byte)) . $byte;
    $hex .= $byte . " ";
  }
  return $hex;
}

function prepareRigCommand($what, $command)
{
  $tosend = ltrim($what, "0");
  $tosend = rtrim($what, ";");
  $tosend = $command . " " . $what . "\n";
}

function handle_echo(&$server, &$client, $input, $call, $radio)
{
  //  echo "incoming ECHO: " . $input . "\n";
  if (strpos($input, "KR") !== false) {
    //    echo "now ECHO: " . $input . "\n";
    for ($i = 0; $i < count($client); $i++) {
      //      echo "i: " . $i . "\n";
      ///  echo "client: " . $client . "\n";
      /*      echo "handle ECHO: " .
        print_r($client[$i]) .
        " " .
        $i .
        " " .
        $input .
        "\n";
*/ //    if ($i > 0) {
      $server->socket_write_smart($client[$i]->socket, $input, "");
      //    }
      //  $server->socket_write_smart($client, $input . "\n", "");
    }
  }
}

function handle_speed(&$server, &$client, $input, $call, $radio)
{
  echo "incoming SPEED: " . hexdec($input) . "\n";
//  $comm = bin2hex(substr($input, 3, 1));
//  $commI = hexdec($comm);
  $comm1 = $input; //0x3f & hexdec(bin2hex($input));
//  $commI=hexdec($comm1);
    echo "after 80 xxxKeyer speed: len: " .
  strlen($comm1) .
    " " .
    $comm1 .
    " lenhexdec: " .
    strlen($comm1) .
    "\n";
  SetKeyerSpeed($radio, "WKSpeed", $comm1);//hexdec($comm1));
  $tData = "1";
  SetInterface($radio, "CWChangeCk", $tData);
/*  for ($i = 0; $i < count($client); $i++) {
    echo "socket: " . $client[$i]->socket . "\n";
    $server->socket_write_smart($client[$i]->socket, "KS " . $comm1 . ";", "");
  }
 */ //  }
}

function handle_input(&$server, &$client, $input, $call, $radio)
{
  $trim = $input;
  $input = "";
  //echo "TRIM: $trim for radio $radio\n";
  $tMyRadio = $radio;
  if (strtolower($trim) == "quit") {
    // User Wants to quit the server
    $server->disconnect($client->server_clients_index); // Disconnect this client.
    return; // Ends the function
  }
  $comms1 = str_replace("'*", $call, $trim);
  $comms = explode(";", $comms1);
  $tcpData = "";
  $dupedata = "";
  foreach ($comms as $comm) {
    if (strlen($comm) > 0) {
      echo "comm: " . $comm . "\n";
      if(strstr($comm,"!ESC")){
        $comm="";
        $tcpData='';
        $comm='';
        echo "sending chr(10) to CWIn\n";
        SetInterface($tMyRadio, "CWIn", chr(10));
      }

      $comm = $comm . ";";
      switch (true) {
        case strstr($comm, "KY"):
          $comm = str_replace(";", "", $comm);
          $comm = str_replace("KY ", "", $comm);
  //                   echo "comm KY: " . $comm . "\n";
          //          echo "KY Here: " . $comm . " " . bin2hex($comm) . "\n";
          //$commX = $comm; //substr($comm, 3, 1);
          $commX = $comm; // (already stripped) substr($comm, 3, 1);
          //         echo "comm=commX: " . bin2Hex($commX) . "\n";
          //          $gotCtrl = 0;
          if (ord(substr($commX, 0, 1)) == 0) {
            $GLOBALS["gotCtrl"] = 1;
            $commX = "";
          }
          if ($GLOBALS["gotCtrl"] == 1) {
            $GLOBALS["gotCtrl"] = 0;
            //echo back
            if (ord(substr($comm, 1, 1)) == 4) {
              $tV = substr($comm, 2, 1);
              //              echo "gotyz: " . bin2hex($comm) . "\n";
              //             $commX = substr($comm, 3, 1);
              //              SetCWOutWKAdditive($tMyRadio, $tV); //$commX);
              SetInterface($tMyRadio, "CWOutWK", $tV);
              //             SetInterface($tMyRadio, "CWOutWKCk", "1");
              $commX = "";
              //              $tcpData = "KR" . $tV . ";";
            }
            if (ord(substr($comm, 1, 1)) == 2) {
              //              echo "gotxx: " . bin2hex($comm) . "\n";
              //             SetInterface($tMyRadio, "CWOutWK", chr(0x1f));
              $commX = "";
              $tcpData = "KR " . chr(0x1f) . ";"; // intval(0x12);
            }
            if (ord(substr($comm, 1, 1)) == 3) {
              //              echo "gotxw: " . bin2hex($comm) . "\n";
              SetInterface($tMyRadio, "CWOutWK", chr(0x00) . chr(0x03));
              $commX = "";
              //              $tcpData = "KR" . chr(0x1f) . ";"; // intval(0x12);
            }
          }

          if (strlen($commX) > 0) {
                        echo "setting CWOutWK: " . bin2hex($commX) . "\n";
            if (intval($commX) < 96 && ord($commX) > 31) {
                            echo "setting KY additive: " . $commX . "\n";
              SetCWInAdditive($tMyRadio, $commX);
              //             SetCWInUnclearedAdditive($tMyRadio, $commX);
            } else {
              SetInterface($tMyRadio, "CWOutWK", $commX);
                           echo "setting cwoutck: " . bin2hex($commX) . "\n";
            }
            //           echo "done setting out: " . $commX . "\n";
            $tcpData = $comm . ";"; // intval(0x12);

          }
          break;
        case strstr($comm, "FA;"):
          $trig = getField($tMyRadio, "MainIn", "RadioInterface");
          $trig = "FA" . str_pad($trig, 11, "0", STR_PAD_LEFT) . ";";
          $tcpData = $tcpData . $trig;
          break;
        case strstr($comm, "FA"):
          $tosend = substr($comm, 2);
          $tosend = ltrim($tosend, "0");
          $tosend = rtrim($tosend, ";");
          $tField = "MainOut";
          $tData = $tosend;
          SetInterface($tMyRadio, $tField, $tData);
          $tcpData = $comm;
          break;
        case strstr($comm, "FB;"):
          $trig = getField($tMyRadio, "SubIn", "RadioInterface");
          $trig = "FB" . str_pad($trig, 11, "0", STR_PAD_LEFT) . ";";
          $tcpData = $tcpData . $trig;
          break;
        case strstr($comm, "FB"):
          $tosend = substr($comm, 2);
          $tosend = ltrim($tosend, "0");
          $tosend = rtrim($tosend, ";");
          $tField = "SubOut";
          $tData = $tosend;
          SetInterface($tMyRadio, $tField, $tData);
          break;
        case strstr($comm, "SM;"):
          $trig = getField($tMyRadio, "SMeterIn", "RadioInterface");
            if ($trig!==""){
            $trig = $trig + 54;
            $trig = intval((2.55 / 15) * $trig);
            $trig = str_pad($trig, 4, "0", STR_PAD_LEFT);
            $trig = "SM" . $trig . ";";
            $tcpData = $tcpData . $trig;
          }
          break;
        case strstr($comm, "SA;"):
          $trig = "SA0000000XXXXXXXX;";
          $tcpData = $tcpData . $trig;
          break;
        case strstr($comm, "SC;"):
          $trig = "SC0;";
          $tcpData = $tcpData . $trig;
          break;
        case strstr($comm, "DQ;"):
          $trig = "DQ0;";
          $tcpData = $tcpData . $trig;
          break;
        case strstr($comm, "LK;"):
          $trig = "LK00;";
          $tcpData = $tcpData . $trig;
          break;
        case strstr($comm, "MF;"):
          $trig = "MF0;";
          $tcpData = $tcpData . $trig;
          break;
        case strstr($comm, "TO;"):
          $trig = "TO0;";
          $tcpData = $tcpData . $trig;
          break;
        case strstr($comm, "CA;"):
          $trig = "CA0;";
          $tcpData = $tcpData . $trig;
          break;
        case strstr($comm, "SM0;"):
          $trig = getField($tMyRadio, "SMeterIn", "RadioInterface");
          $trig = $trig + 54;
          $trig = intval((2.55 / 15) * $trig);
          $trig = str_pad($trig, 4, "0", STR_PAD_LEFT);
          $trig = "SM0" . $trig . ";";
          $tcpData = $tcpData . $trig;
          break;
        case strstr($comm, "SM1;"):
          $trig = getField($tMyRadio, "SMeterIn", "RadioInterface");
          $trig = $trig + 54;
          $trig = intval((2.55 / 15) * $trig);
          $trig = str_pad($trig, 4, "0", STR_PAD_LEFT);
          $trig = "SM1" . $trig . ";";
          $tcpData = $tcpData . $trig;
          break;
        case strstr($comm, "FT;"):
          $trig = getField($tMyRadio, "SplitIn", "RadioInterface");
          if ($trig == "0") {
            $trig = "FT0;";
          } else {
            if ($trig == "1") {
              $trig = "FT1;";
            }
          }
          $tcpData = $tcpData . $trig;
          break;
        case strstr($comm, "FT"):
          $tData = substr($comm, 2, 1);
          $tData = ltrim($tData, "0");
          $tData = rtrim($tData, ";");
          $tField = "SplitOut";
          SetInterface($tMyRadio, $tField, $tData);
          break;
        case strstr($comm, "MC"):
          //    $server->sendRigMessage($comm);
          break;
        case strstr($comm, "MD;"):
          $trig = getField($tMyRadio, "ModeIn", "RadioInterface");
          $smode = "";
          switch (true) {
            case $trig == "LSB":
              $smode = "1";
              break;
            case $trig == "USB":
              $smode = "2";
              break;
            case $trig == "CW":
              $smode = "3";
              break;
            case $trig == "FM":
              $smode = "4";
              break;
            case $trig == "AM":
              $smode = "5";
              break;
            case $trig == "RTTY":
              $smode = "6";
              break;
            case $trig == "CWR":
              $smode = "7";
              break;
            case $trig == "RTTYR":
              $smode = "9";
              break;
          }
          $trig = "MD" . $smode . ";";
          $tcpData = $tcpData . $trig;
          break;
        case strstr($comm, "MD"):
          $comm = str_replace("$", "", $comm);
          $kMode = substr($comm, 2);
          $kMode = str_replace(";", "", $kMode);
          $smode = "";
          switch (true) {
            case $kMode == 1:
              $smode = "LSB";
              break;
            case $kMode == 2:
              $smode = "USB";
              break;
            case $kMode == 3:
              $smode = "CW";
              break;
            case $kMode == 4:
              $smode = "FM";
              break;
            case $kMode == 5:
              $smode = "AM";
              break;
            case $kMode == 6:
              $smode = "RTTY";
              break;
            case $kMode == 7:
              $smode = "CWR";
              break;
            case $kMode == 8:
              break;
            case $kMode == 9:
              $smode = "RTTYR";
              break;
          }
          $tData = $smode;
          SetInterface($tMyRadio, "ModeOut", $tData);
          break;
        case strstr($comm, "TX;"):
          //          echo "TRANSMIT";
          $tData = "1";
          SetInterface($tMyRadio, "PTTOut", $tData);
          break;
        case strstr($comm, "TX1;"):
          $tData = "1";
          SetInterface($tMyRadio, "PTTOut", $tData);
          break;
        case strstr($comm, "TX0;"):
          $tData = "0";
          SetInterface($tMyRadio, "PTTOut", $tData);
          break;
        case strstr($comm, "RX;"):
          //          echo "RECEIVE";
          $tData = "0";
          SetInterface($tMyRadio, "PTTOut", $tData);
          break;
        case strstr($comm, "TQ;"):
          $tcpData = $tcpData . "TQ0;";
          break;
        case strstr($comm, "TB;"):
          $tcpData = $tcpData . "TB0000;";
          break;
        case strstr($comm, "KR"):
          //          echo "KR found: " . bin2hex($comm) . " " . $client . "\n";
          $tcpData = $tcpData . $comm;
          //          $server->socket_write_smart($client->socket, $comm);
          break;
        case strstr($comm, "PS0;"):
          $data = sprintf("\x87\x30\x13");
          break;
        case strpos($comm, "KS") !== false:
          $comm = substr($comm, 2, 1);
          //          echo "new speed from KS: " . $comm . "\n";
          $comm = str_replace(";", "", $comm);
          //          echo "Keyer speed: " . bin2hex($comm) . "\n";
          SetKeyerSpeed($tMyRadio, "WKSpeed", $comm);
          $tData = "1";
          SetInterface($tMyRadio, "CWChangeCk", $tData);
          //         $tcpData = "KS" . $comm . ";";
          break;
        case strstr($comm, "HEX"):
          if (strstr($comm, "0W")) {
            $comm = str_replace("HEX0W", "*0w", $comm);
          } else {
            $comm = str_replace("HEX1W", "*1w", $comm);
          }
          $comm = str_replace(";", "", $comm);
          $comm = str_replace("X", "x", $comm);
          SetInterface($tMyRadio, "CommandOut", $comm);
          break;
        case strstr($comm, "ASC"):
          if (strstr($comm, "0W")) {
            $comm = str_replace("ASC0W", "*0w", $comm);
          } else {
            $comm = str_replace("ASC1W", "*1w", $comm);
          }
          SetInterface($tMyRadio, "CommandOut", $comm);
          break;
      }
echo "here $tcpData\n";
      if (strlen($tcpData) > 0) {
        //        echo "client: " . $client . "\n";
      for ($i = 0; $i < count($client); $i++) {
          ///  echo "client: " . $client . "\n";
/*                    echo "handle ECHO: " .
            print_r($client[$i]) .
            " " .
            $i .
            " " .
            $input .
            "\n";
 *///    if ($i > 0) {
          $tcpData = $tcpData . ";";
          $tcpData = str_replace(";;", ";", $tcpData);
         $server->socket_write_smart($client[$i]->socket, $tcpData, "");
          //    }
          //  $server->socket_write_smart($client, $input . "\n", "");
//        }
        //        if (substr($tcpData, "KY") > 0) {
        //        $server->socket_write_smart($client, $tcpData, "");
                }
                echo "TCPData: " . $tcpData . "\n";
      }
    }
  }
}
?>
