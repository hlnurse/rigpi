<?php
/**
 * @author COMMSOFT, Inc.
 *
 * This routine returns the radio data
 *
 * It must live in the programs folder
 */

ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
if (isset($_POST["tRadio"])){
  $model = $_POST["tRadio"];
}else{
  $model="IC-7300";
}
require "/var/www/html/programs/sqldata.php";
require_once "/var/www/html/classes/MysqliDb.php";
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->where("MODEL", $model);
$row = $db->getOne("Radios");
if ($row) {
  $data = $row["NUMBER"];
  echo $data;
} else {
  echo mysqli_error($row);
  //"Data not found.";
}
?>
