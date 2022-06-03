<?php

/**
 * @author COMMSOFT, Inc.
 *
 * This routine gets the settings data
 *
 * It must live in the programs folder
 */

ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
$tField = $_POST["field"];
$tRadio = $_POST["radio"];
$tTable = "RadioInterface"; //$_POST["db"];
$dRoot = "/var/www/html";
require $dRoot . "/programs/sqldata.php";
require_once $dRoot . "/classes/MysqliDb.php";
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->where("Radio", $tRadio);
$row = $db->getOne($tTable);
$data = $row[$tField];

echo $data;
?>
