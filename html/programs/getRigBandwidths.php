<?php

/**
 * @author COMMSOFT, Inc.
 *
 * This routine returns the specified radio data from rigctl
 *
 * It must live in the programs folder
 */

ini_set("error_reporting", E_ALL);
ini_set("display_errors", "on");
$dRoot = "/var/www/html";
require_once $dRoot . "/programs/sqldata.php";
require_once $dRoot . "/classes/MysqliDb.php";
if (isset($_POST['myRadioName'])){
  $tMyRadioName = $_POST["myRadioName"];  //this is model, might be at station end.
}else{
  $tMyRadioName = "IC-7300";
}
if (isset($_POST['myRadioID'])){
  $transRadioID = $_POST["myRadioID"];  //this is id, might be at station end.
}else{
  $transRadioID = "3073";
}
if (isset($_POST['myRadio'])){
  $tMyRadio = $_POST["myRadio"];  //this is radio, might be at station end.
}else{
  $tMyRadio = "1";
}
//$tMyRadio=2;
if (isset($_POST["mode"])) {
  $tCap = $_POST["mode"];
  if ($tCap == "USB-D" || $tCap == "PKTUSB/USB-D") {
    $tCap = "PKTUSB";
  }
} else {
  $tCap = "FM"; //////////////
}
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
  $tHamlibNr = $transRadioID;
//$tComPort = 4530+ 2*($tMyradio);//z$row["Port"];
if ($tCap == "") {
  $db->where("Radio",$tMyRadio);
  $row = $db->getOne("RadioInterface");
  $tBW=$row['BWIn'];
  $tCaps = "Current=" . $tBW;
} else {
  $t =
    "rigctl -m " . $tHamlibNr .
    " -u | grep 'Normal' | grep -w " .
     "'$tCap' | grep -v 'FM-D'";
 $t=trim($t);
 //   echo $t;
  $tCaps = shell_exec($t);
  $tCaps = str_replace(": ", "=", $tCaps);
  $tCaps = str_replace(",", "", $tCaps);
  $tCaps=trim($tCaps);
  $aCaps2=explode("=", $tCaps);
//  $tCaps=$aCaps2[0]."=".$aCaps2[1]."=" . "0.0 Hz\tWide=3488.0 Hz";

  if (strpos($tCaps, "=0")>0){
    $tCaps=getCandidateBW($aCaps2);
//    echo $tCaps;
  }
}
echo $tCaps . "\n";

function getCandidateBW($aCaps){
  $tCaps="";
//  echo $aCaps[0] . "\n";

//  echo strpos("CW", $aCaps[0]) . "\n";

  if (strpos($aCaps[0],"CW")>-1){
    $tCaps=$aCaps[0]."=".$aCaps[1]."=" . "250.0 Hz\tWide=1200.0 Hz";
  }elseif(strpos($aCaps[0],"SB")>-1){
    $tCaps=$aCaps[0]."=".$aCaps[1]."=" . "1800.0 Hz\tWide=3000.0 Hz";
  }elseif(strpos($aCaps[0],"AM")>-1){
    $tCaps=$aCaps[0]."=".$aCaps[1]."=" . "3000.0 Hz\tWide=9000.0 Hz";
  }elseif(strpos($aCaps[0],"FM")>-1){
    $tCaps=$aCaps[0]."=".$aCaps[1]."=" . "7000.0 Hz\tWide=15000.0 Hz";
  }elseif(strpos($aCaps[0],"PKT")>-1){
    $tCaps=$aCaps[0]."=".$aCaps[1]."=" . "250.0 Hz\tWide=1200.0 Hz";
  }elseif(strpos($aCaps[0],"USB-D")>-1){
    $tCaps=$aCaps[0]."=".$aCaps[1]."=" . "1800.0 Hz\tWide=3000.0 Hz";
  }elseif(strpos($aCaps[0],"RTTY")>-1){
    $tCaps=$aCaps[0]."=".$aCaps[1]."=" . "250.0 Hz\tWide=2400.0 Hz";
  }   
  return $tCaps;
}
?>
