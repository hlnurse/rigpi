<?php

//delete.php

if (isset($_POST["id"])) {
  ini_set("display_errors", 1);
  ini_set("error_reporting", E_ALL);
  $dRoot = "/var/www/html";
  require_once $dRoot . "/programs/sqldata.php";
  require_once $dRoot . "/classes/MysqliDb.php";
  $tID = $_POST["id"];
  $db = new MysqliDb(
    "localhost",
    $sql_radio_username,
    $sql_radio_password,
    $sql_radio_database
  );
  $db->where("id", $tID);
  $db->delete("Events");
}

?>
