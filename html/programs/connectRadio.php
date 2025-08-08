<?php
ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
$dRoot = "/var/www/html";
require_once $dRoot . "/programs/sqldata.php";
require_once $dRoot . "/classes/MysqliDb.php";
if (isset($_POST["radio"])) {
  $tMyRadio = $_POST["radio"];
} else {
  $tMyRadio = 1;
}
if (isset($_POST["user"])) {
  $tUsername = $_POST["user"];
} else {
  $tUsername = "admin";   //only if user not specified
}

$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->where("Radio", $tMyRadio);
$row = $db->getOne("MySettings");
$id = $row["ID"];
$db->where("NUMBER", $id);
$row1 = $db->getOne("Radios");
if ($row1["SAMEAS"] > 0) {
  $id = $row1["SAMEAS"];
}
//$tExec = "php /var/www/html/programs/hamlibDo.php";
//exec($tExec);
?>
