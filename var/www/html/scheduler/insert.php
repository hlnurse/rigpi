<?php

//insert.php
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
  $tRN = $_POST["RadioName"];
  $tUN = $_POST["username"];
  $tCall = $_POST["callsign"];
  $tDesc = $_POST["description"];
  $tAD = $_POST["allDay"];
  if ($tAD == false) {
    $tAD = 0;
  } else {
    $tAD = 1;
  }
  $db = new MysqliDb(
    "localhost",
    $sql_radio_username,
    $sql_radio_password,
    $sql_radio_database
  );
  $db->where("MyCall", $tCall);
  $row = $db->getOne("Users");
  $tName = $row["FirstName"];
  $data = [
    "title" => $tTitle,
    "start_event" => $tStart,
    "end_event" => $tEnd,
    "allDay" => $tAD,
    "RadioName" => $tRN,
    "callsign" => $tCall,
    "FirstName" => $tName,
    "username" => $tUN,
    "description" => $tDesc,
  ];
  $id = $db->insert("Events", $data);
  if ($id) {
    echo $id;
  } else {
    echo $db->getLastError();
  }
}
?>
