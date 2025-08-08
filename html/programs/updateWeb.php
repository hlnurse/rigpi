<?php

/**
 * @author COMMSOFT, Inc.
 *
 * This routine sends data from interface database to radio
 *
 * It must live in the programs folder
 */

ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
require_once "./programs/GetMyRadioFunc.php";
$radio = 1;
$dataIn = GetOut($radio);
$data = explode(chr(96), $dataIn);
$tF = $data[0];
$tFCk = $data[1];
$tS = $data[2];
$tSCk = $data[3];
$tM = $data[4];
$tMCk = $data[5];
$tSpCk = $data[6];
$tSpCk = $data[7];
if ($tFCk == 1) {
  SetInterface($radio, "MainOutCk", "0");
  $command = "F";
  $tosend = $command . " " . $tF;
  $tosend = $tosend . "\n";
  $server->sendRigMessage($tosend);
  $response = $server->getRigMessage(11);
  echo "from web:" . $response;
  $rigdata = $rigdata . "FA" . str_pad($data[0], 11, "0", STR_PAD_LEFT) . ";";
}

?>
