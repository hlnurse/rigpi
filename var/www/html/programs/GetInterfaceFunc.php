<?php

/**
 * @author Howard Nurse W6HN
 *
 * This function gets the radio data
 *
 * It must live in the programs folder
 */
function getRadioData($tRadio, $tField, $tTable)
{
  ini_set("error_reporting", E_ALL);
  ini_set("display_errors", 1);
  $dRoot = "/var/www/html";
  $data = "";
  require $dRoot . "/programs/sqldata.php";
  require_once $dRoot . "/classes/MysqliDb.php";
  $db = new MysqliDb(
    "localhost",
    $sql_radio_username,
    $sql_radio_password,
    $sql_radio_database
  );
  if ($db->tableExists($tTable)) {
    $db->where("Radio", $tRadio);
    $row = $db->getOne($tTable);
    if ($db->count > 0) {
      $data = $row[$tField];
    } else {
      $data = "";
    }
  } else {
    $tData = "";
  }
  return $data;
}
?>
