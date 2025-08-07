<?php

/**
 * @author Howard Nurse, W6HN
 *
 * This routine returns the modes for a list. Mode list comes from LogStyles database, comma separated.
 *
 * It must live in the programs folder
 */

ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
function getModes($tMyRadio)
{
  $sql_radio_username = "";
  $sql_radio_password = "";
  $sql_radio_database = "";
  $dRoot = "/var/www/html";
  require $dRoot . "/programs/sqldata.php";
  require_once $dRoot . "/classes/MysqliDb.php";
  $db = new MysqliDb(
    "localhost",
    $sql_log_username,
    $sql_log_password,
    $sql_log_database
  );
  if (!$db) {
    die("Connection failed: " . $db->connect_error);
  }
  $db->where("Radio", $tMyRadio);
  $styles = $db->getOne("MySettings");
  $style = $styles["LogStyle"];
  $db->where("Name", "$style");
  $db->where("Field", "Mode");
  $modes = $db->getOne("LogStyles");
  $mode = $modes["ListContents"];
  //	echo $mode;
  if (strlen($mode) > 0) {
    $list = explode(",", $mode);
    $data = "";
    $i = 0;
    foreach ($list as $mode) {
      $data =
        $data .
        "<div class='mymode'><li><a class='dropdown-item' id='m0' href='#'>" .
        $mode .
        "</a></li></div>\n";
    }
  } else {
    $db->orderBy("Name", "ASC");
    $data = "";
    $i = 0;
    $cols = ["name"];
    $modes = $db->get("Modes", null, $cols);
    foreach ($modes as $mode) {
      $data =
        $data .
        "<div class='mymode'><li><a class='dropdown-item' id='m0' href='#'>" .
        $mode["name"] .
        "</a></li></div>\n";
      $i = $i + 1;
    }
  }
  return $data;
}

?>
