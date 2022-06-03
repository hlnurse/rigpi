<?php

//load.php

ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL);
$dRoot = "/var/www/html";
require_once $dRoot . "/programs/sqldata.php";
require_once $dRoot . "/classes/MysqliDb.php";
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->orderBy("id", "Asc");
$result = $db->get("Events");
$data = [];
$dataOut = [];
foreach ($result as $row) {
  $tBAllDay = false;
  if (!$row["allDay"]) {
    $tBAllDay = false;
  } else {
    $tBAllDay = true;
  }
  //echo strtotime($row['start_event'])."\n\n";
  $st = $row["start_event"]; //date("Y-m-d H:i:s", strtotime($row["start_event"]));
  $data = [
    "title" => $row["title"],
    "start" => $st,
    "end" => $row["end_event"],
    "id" => $row["id"],
    "callsign" => $row["callsign"],
    "description" => "x", //$row["FirstName"] . ", " . $row["callsign"],
    "firstname" => $row["FirstName"],
    "username" => $row["username"],
    "allDay" => $tBAllDay,
  ];
  array_push($dataOut, $data);
}
//echo print_r($data);
echo json_encode($dataOut);
//print_r($dataOut);
?>
