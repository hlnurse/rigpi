<?php

//update.php

ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL);
ini_set("display_errors", "on");
$dRoot = "/var/www/html";
require_once $dRoot . "/programs/sqldata.php";
require_once $dRoot . "/classes/MysqliDb.php";
if (isset($_POST["title"])) {
  $tTitle = $_POST["title"];
  $tStart = $_POST["start_event"];
  $tEnd = $_POST["end_event"];
  $tID = $_POST["id"];
  $tAD = $_POST["allDay"];
  $db = new MysqliDb(
    "localhost",
    $sql_radio_username,
    $sql_radio_password,
    $sql_radio_database
  );
  if ($tAD == false) {
    $tAD = 0;
  } else {
    $tAD = 1;
  }
  if (($tEnd == "" || $tEnd == null || $tEnd == "Invalid date") && tAD == 0) {
    $tT = strtotime($tStart);
    $tT = $tT + 3600;
    $tEnd = date("c", $tT);
  }
  //  echo "here";
  $st = $tStart; //date_format($tStart, "Y-m-d H:i:s");
  $data = [
    "title" => $tTitle,
    "start_event" => $st,
    "end_event" => $tEnd,
    "allDay" => $tAD,
    "id" => $tID,
  ];
  $db->where("id", $tID);
  $res = $db->update("Events", $data);
  //  echo "result: " . $res;
}
?>
