<?php
ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
$tRadio = 2; //$radio;
$dRoot = "/var/www/html";
require $dRoot . "/programs/sqldata.php";
$sQuery = "SELECT *, UNIX_TIMESTAMP() - UNIX_TIMESTAMP(Close_Watch) as Diff from RadioInterface where Radio='$tRadio'";
$con = new mysqli(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
if ($con->connect_error) {
  die("Connection failed: " . $con->connect_error);
}
$result = $con->query($sQuery);
if ($row = $result->fetch_assoc()) {
  $data =
    $row["MainOut"] .
    "`" .
    $row["MainOutCk"] .
    "`" .
    $row["SubOut"] .
    "`" .
    $row["SubOutCk"] .
    "`" .
    $row["ModeOut"] .
    "`" .
    $row["ModeOutCk"] .
    "`" .
    $row["SplitOut"] .
    "`" .
    $row["SplitOutCk"] .
    "`" .
    $row["CWOut"] .
    "`" .
    $row["CWOutCk"] .
    "`" .
    $row["CWIn"] .
    "`" .
    $row["CWChangeCk"] .
    "`" .
    $row["PTTOut"] .
    "`" .
    $row["PTTOutCk"] .
    "`" .
    $row["CommandOut"] .
    "`" .
    $row["CommandOutCk"] .
    "`" .
    $row["Slave"] .
    "`" .
    $row["SlaveCk"] .
    "`" .
    $row["CWDeadman"] .
    "`" .
    $row["RFGain"] .
    "`" .
    $row["RFGainCk"] .
    "`" .
    $row["AFGain"] .
    "`" .
    $row["AFGainCk"] .
    "`" .
    $row["PwrOut"] .
    "`" .
    $row["PwrOutCk"] .
    "`" .
    $row["MicLvl"] .
    "`" .
    $row["MicLvlCk"] .
    "`" .
    $row["Diff"];
} else {
  $data = "NG";
}
echo $data;
?>
