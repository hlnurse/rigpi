<?php

/**
 * @author W6HN
 *
 * This routine returns table header and rows with logged Q's
 *
 * It must live in the programs folder
 */

ini_set("error_reporting", E_ALL);
ini_set("display_errors", "on");
require_once "/var/www/html/programs/sqldata.php";
require_once "/var/www/html/classes/MysqliDb.php";
$tMyRadio = "1";
$tBand = "";
$tFolder = "Inbox";
$tNeed = "";
$tFrequency = "";

if (!empty($_POST["radio"])) {
  $tMyRadio = $_POST["radio"];
} else {
  $tMyRadio = "1";
}

if (!empty($_POST["band"])) {
  $tBand = $_POST["band"];
} else {
  $tBand = "23";
}

if (!empty($_POST["band"])) {
  $tBand = $_POST["band"];
} else {
  $tBand = "23";
}

if (!empty($_POST["folder"])) {
  $tFrequency = $_POST["frequency"];
} else {
  $tFrequency = "1244600000";
}

if (!empty($_POST["need"])) {
  $tNeed = $_POST["need"];
} else {
  $tNeed = "";
}

$db = new MysqliDb(
  "localhost",
  $sql_log_username,
  $sql_log_password,
  $sql_log_database
);
$dbStyle = new MysqliDb(
  "localhost",
  $sql_log_username,
  $sql_log_password,
  $sql_log_database
);

$db->where("Folder", $tFolder);
$db->where("Band", $tBand);
$db->where("Radio", $tMyRadio);
$db->orderBy("Frequency", "ASC");
$row = $db->get("Spots");
$tUp = "";
switch ($tBand) {
  case "160":
    $tUp = 2100000;
    break;
  case "80":
    $tUp = 4050000;
    break;
  case "60":
    $tUp = 5500000;
    break;
  case "40":
    $tUp = 7400000;
    break;
  case "30":
    $tUp = 10250000;
    break;
  case "20":
    $tUp = 14450000;
    break;
  case "17":
    $tUp = 18270000;
    break;
  case "15":
    $tUp = 21500000;
    break;
  case "12":
    $tUp = 25090000;
    break;
  case "10":
    $freq = $tFrequency - 28000000;
    $freq = intval($freq / 500000);
    $tUp = $freq * 500000 + 550000 + 28000000;
    break;
  case "6":
    $freq = $tFrequency - 50000000;
    $freq = intval($freq / 500000);
    $tUp = $freq * 500000 + 550000 + 50000000;
    break;
  case "2":
    $freq = $tFrequency - 144000000;
    $freq = intval($freq / 500000);
    $tUp = $freq * 500000 + 550000 + 144000000;
    break;
  case "1.25":
    $freq = $tFrequency - 222000000;
    $freq = intval($freq / 500000);
    $tUp = $freq * 500000 + 550000 + 222000000;
    break;
  case "70":
    $freq = $tFrequency - 420000000;
    $freq = intval($freq / 500000);
    $tUp = $freq * 500000 + 550000 + 420000000;
    break;
  case "23":
    $freq = $tFrequency - 1240000000;
    $freq = intval($freq / 500000);
    $tUp = $freq * 500000 + 550000 + 1240000000;
    break;
}

$i = 0;
$spots = "";
$aLines = "";
$pTop = 50000; //previous top
$tRowCount = $db->count;
while ($i < $tRowCount) {
  $tRow = $row[$i];
  $DX = $tRow["DX"];
  $tDXCC = $tRow["DXCC"];
  $tBand = $tRow["Band"];
  $tBandM = $tRow["Band"] . "M";
  $tID = $tRow["id"];
  $frequency = $tRow["Frequency"];
  $tMode = $tRow["Mode"];
  $spotter = $tRow["Spotter"];

  switch ($tNeed) {
    case "callWorked":
      $db->where("Callsign", $DX);
      break;
    case "callWorkedBand":
      $db->where("Callsign", $DX);
      $db->where("(Band = '$tBand' or Band = '$tBandM')");
      break;
    case "callConfirmed":
      $db->where("Callsign", $DX);
      $db->where("QSL_R", "Y");
      $db->where("(Band = '$tBand' or Band = '$tBandM')");
      break;
    case "callConfirmedBand":
      $db->where("Callsign", $DX);
      $db->where("QSL_R", "Y");
      break;
    case "entityWorked":
      $db->where("DXCC", $tDXCC);
      break;
    case "entityWorkedBand":
      $db->where("DXCC", $tDXCC);
      $db->where("(Band = '$tBand' or Band = '$tBandM')");
      break;
    case "entityConfirmed":
      $db->where("DXCC", $tDXCC);
      $db->where("QSL_R", "Y");
      break;
    case "entityConfirmedBand":
      $db->where("DXCC", $tDXCC);
      $db->where("QSL_R", "Y");
      $db->where("(Band = '$tBand' or Band = '$tBandM')");
      break;
    default:
      break;
  }

  $tRowLog = $db->getOne("Logbook");
  $tColor = "class='btn btn-secondary btn-pointer btn-sm BSbutton'";
  $tText = "No results";

  if ($db->count == 1 && $tNeed == "callWorked") {
    $tColor = "class='btn btn-info btn-pointer btn-sm BSbutton'";
    $tText = "Station Worked";
  }

  if ($db->count == 1 && $tNeed == "callConfirmed") {
    $tColor = "class='btn btn-success btn-pointer btn-sm BSbutton'";
    $tText = "Station confirmed";
  }

  if ($db->count == 1 && $tNeed == "callWorkedBand") {
    $tColor = "class='btn btn-warning btn-pointer btn-sm BSbutton'";
    $tText = "Station Worked this Band";
  }

  if ($db->count == 1 && $tNeed == "callConfirmedBand") {
    $tColor = "class='btn btn-sdanger btn-pointer btn-sm BSbutton'";
    $tText = "Station Confirmed this Band";
  }

  $tColorB = "";
  if ($db->count == 0 && $tNeed == "entityWorked") {
    $tColor = "class='btn btn-info btn-pointer btn-sm BSbutton'";
    $tText = "Entity Worked";
  }

  if ($db->count == 0 && $tNeed == "entityConfirmed") {
    $tColor = "class='btn btn-success btn-pointer btn-sm BSbutton'";
    $tText = "Entity Confirmed";
  }

  if ($db->count == 0 && $tNeed == "entityWorkedBand") {
    $tColor = "class='btn btn-warning btn-pointer btn-sm BSbutton'";
    $tText = "Entity Worked this Band";
  }

  if ($db->count == 0 && $tNeed == "entityConfirmedBand") {
    $tColor = "class='btn btn-danger btn-pointer btn-sm BSbutton'";
    $tText = "Entity Confirmed this Band";
  }

  $top = -15 + ($tUp - $frequency) / 100;
  //  echo "top: " . $top . " " . $tUp . " " . $frequency . "\n"; //-15px is half height of button
  $ftop = $top;
  $top1 = $pTop - $top;
  $spottertext =
    $tText .
    "\nMins: " .
    number_format((time() - $tRow["Webdate"]) / 60, 2, ".", ",") .
    "\nDE: " .
    $spotter;
  if ($top1 <= 30) {
    $top = $top - 38 + $top1;
  }
  $pTop = $top;
  $top = $top . "px";
  $spots .=
    "<button " .
    $tColor .
    " id='b" .
    $tID .
    "' call='$DX' style='top: $top;' ftop='$ftop' band='$tBand' frequency='$frequency' mode='$tMode' title='$spottertext' >$DX</button><br>";
  $i++;
}

echo $spots;

//echo 'here';

?>
