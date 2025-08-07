<?php
/**
 * @author Howard Nurse, W6HN
 * 
 * This is part of thee login processor
 * 
 * It must live in the classes folder   
 */
$tPWD=md5("x");
ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
require "/var/www/html/programs/sqldata.php";
require_once "/var/www/html/classes/MysqliDb.php";

$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
if (!$db) {
  die("Connection failed: " . mysqli_connect_error());
}
$data = [
  "Password" => $tPWD,
];
$db->where("uID", 1);
if ($db->update("Users", $data)) {
  echo "<br>Username updated.<p>";
} else {
  echo "<br>Username must be unique!<p><p>Changes not saved.<p>";
}


?>