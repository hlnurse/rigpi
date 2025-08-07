<?php

/**
 * @author Howard Nurse, W6HN
 *
 * This routine returns a list, html, of users from the Users table
 *
 * It must live in the programs folder
 */

//function getUsersText()
//{
ini_set("error_reporting", E_ALL);
ini_set("display_errors", "on");
$dRoot = "/var/www/html";
require_once $dRoot . "/programs/sqldata.php";
require_once $dRoot . "/classes/MysqliDb.php";
$db = new MysqliDb(
  "localhost",
  $sql_log_username,
  $sql_log_password,
  $sql_log_database
);
$db->orderBy("uID", "ASC");
$row = $db->get("Users");
$i = 0;
$tB = [];
$tCount = $db->count;
while ($i < $tCount) {
  $tRow = $row[$i];
  $tRS = $tRow["SelectedRadio"];
  $db->where("Radio", $tRS);
  $row1 = $db->getOne("MySettings");
  $tP = $row1["Port"];
  //echo $tP;
  $tRname = "";
  if (intval($tP) > 4530 and intval($tP) < 4600) {
    $tReal = ($tP - 4530) / 2;
    $db->where("Radio", $tReal);
    $row2 = $db->getOne("MySettings");
    $tRName = $row2["RadioName"];
  } else {
    $tRName = $row1["RadioName"];
  }
  $ts = $tRow["LastVisit"];
  $date = new DateTime("@$ts");
  $date = $date->format("Y-m-d H:i:s");
  $on = $tRow["Active"];
  if ($on == 1) {
    $on = "Y";
  } else {
    $on = "";
  }
  $tA = [
    "MyCall" => $tRow["MyCall"],
    "Username" => $tRow["Username"],
    "FirstName" => $tRow["FirstName"],
    "RadioName" => $tRName,
  ];
  array_push($tB, $tA);
  /*  $tTable =
    $tTable .
    "`" .
    //$on .
    //"`" .
    $tRow["MyCall"] .
    "`" .
    $tRow["Access_Level"] .
    "`" .
    $tRow["Username"] .
    "`" .
    $tRow["FirstName"] .
    "`" .
    //$tRow["LastName"] .
    //"`" .
    //$tRow["QTH"] .
    //"`" .
    //$date .
    //"`" .
    $tRName .
    "~";
*/ $i =
    $i + 1;
}
array_multisort(array_column($tB, "RadioName"), SORT_DESC, $tB);

echo json_encode($tB);
//}
?>
