<?php

/**
 * @author Howard Nurse, W6HN
 *
 * This routine gets the specified field contents from specified table
 *
 * It must live in the programs folder
 */

ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
$tField = $_POST["field"];
$tRadio = $_POST["radio"];
$tTable = $_POST["table"];
require_once "/var/www/html/programs/sqldata.php";
require_once "/var/www/html/classes/MysqliDb.php";
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->where("Radio", $tRadio);
$row = $db->getOne($tTable);
if ($row) {
  $data = $row[$tField];
} else {
  $data = "NG";
}
echo $data;
?>
