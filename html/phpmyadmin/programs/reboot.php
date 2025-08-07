<?php
sleep(4);
$un='admin';////////////////////////////////////////////////////$_SESSION['myUsername'];
require_once "/var/www/html/classes/MysqliDb.php";
require_once "/var/www/html/programs/sqldata.php";
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$data = [
  "MainIn" => "OFF",
  "SubIn" => "OFF",
];
$db->update("RadioInterface", $data);
$db->where("Username", $un);
$data = [
  "Active" => "0",
];
$db->update("Users", $data);
$db->where("Username", $un);
$db->delete("LoggedIn");
session_destroy();
 //   error_log(date("[Y-m-d H:i:s]")."\t[".$level."]\t[".basename(__FILE__)."]\t".$text."\n", 3, 'errorlog.txt');
 	system("sudo reboot now");
?>