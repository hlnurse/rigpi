<?php
/*
 * @author Howard Nurse, W6HN
 *
 * This routine sets various settings data
 *
 * It must live in the programs folder
 */
function SetField($tRadio, $tField, $tData)
{
  ini_set("error_reporting", E_ALL);
  ini_set("display_errors", 1);
  require "/var/www/html/programs/sqldata.php";
  $sQuery = "UPDATE MySettings SET $tField='$tData' where Radio='$tRadio'";
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
}

function SetKeyer($tRadio, $tField, $tData)
{
  // $tData = 0x12;
  //note was Keyer, now RadioInterface
  ini_set("error_reporting", E_ALL);
  ini_set("display_errors", 1);
  require "/var/www/html/programs/sqldata.php";
  //  $tC = str_replace(chr(30), "", $tData);
  $sQuery =
    "UPDATE Keyer SET $tField = " . $tData . " where Radio = " . $tRadio . ";";
  //  echo "query: " . $sQuery . " " . bin2hex($tData) . "end\n";
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
  //  echo mysqli_error($con) . "\n";
}

function SetKeyerSpeed($tRadio, $tField, $tData)
{
  // $tData = 0x12;
  //note was Keyer, now RadioInterface
  ini_set("error_reporting", E_ALL);
  ini_set("display_errors", 1);
  require "/var/www/html/programs/sqldata.php";
  //  $tC = str_replace(chr(30), "", $tData);
  $sQuery =
    "UPDATE Keyer SET $tField = " . $tData . " where Radio = " . $tRadio . ";";
  //  echo "query: " . $sQuery . " " . $tData . " end\n";
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
  //  echo mysqli_error($con) . "\n";
}

function SetKeyerPartial($tRadio, $tField, $tData)
{
  ini_set("error_reporting", E_ALL);
  ini_set("display_errors", 1);
  require "/var/www/html/programs/sqldata.php";
  $sQuery = "UPDATE Keyer SET $tField=replace($tField,'$tData','') where Radio='$tRadio'";
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
}

function SetCWOutAdditive($tRadio, $tData)
{
  //$tRadio = 1;
  //$tData = "cq";
  ini_set("error_reporting", E_ALL);
  ini_set("display_errors", 1);
  require "/var/www/html/programs/sqldata.php";
  //echo "additive query: " . $sQuery . "\n";
  $con = new mysqli(
    "localhost",
    $sql_radio_username,
    $sql_radio_password,
    $sql_radio_database
  );
  $con->query("LOCK TABLE RadioInterface WRITE;");
  $sQuery = "update RadioInterface set CWOut= concat(CWOut, '$tData') where Radio='$tRadio'";
  //  if ($con->connect_error) {
  //    die("Connection failed: " . $con->connect_error);
  //  }
  $result = $con->query($sQuery);
  $con->query("UNLOCK TABLES;");
}

function SetCWInAdditive($tRadio, $tData)
{
  //$tRadio = 1;
  //$tData = "cq";
  ini_set("error_reporting", E_ALL);
  ini_set("display_errors", 1);
  require "/var/www/html/programs/sqldata.php";
  $con = new mysqli(
    "localhost",
    $sql_radio_username,
    $sql_radio_password,
    $sql_radio_database
  );
  // echo "tdata: " . $tData . "\n";
  $con->query("LOCK TABLE RadioInterface WRITE;");
  $sQuery = "UPDATE RadioInterface SET CWIn = CONCAT(CWIn, '$tData') WHERE Radio='$tRadio'";
  //  $sQuery1 = "UPDATE RadioInterface SET CWInUncleared = CONCAT(CWInUncleared, '$tData') WHERE Radio='$tRadio'";
  if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
  }
  $result = $con->query($sQuery);
  //  $result = $con->query($sQuery1);
  $con->query("UNLOCK TABLES;");
}

function SetCWInUnclearedAdditive($tRadio, $tData)
{
  //$tRadio = 1;
  //$tData = "cq";
  ini_set("error_reporting", E_ALL);
  ini_set("display_errors", 1);
  require "/var/www/html/programs/sqldata.php";
  $sQuery = "update RadioInterface set CWInUncleared= concat(CWInUncleared, '$tData') where Radio='$tRadio'";
  $con = new mysqli(
    "localhost",
    $sql_radio_username,
    $sql_radio_password,
    $sql_radio_database
  );
  $con->query("LOCK TABLE RadioInterface WRITE;");
  if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
  }
  $result = $con->query($sQuery);
  $con->query("UNLOCK TABLES;");
}

function SetCWOutWKAdditive($tRadio, $tData)
{
  //$tRadio = 1;
  //$tData = "cq";
  ini_set("error_reporting", E_ALL);
  ini_set("display_errors", 1);
  require "/var/www/html/programs/sqldata.php";
  //  require_once "/var/www/html/classes/MysqliDb.php";
  //$sQuery = "update RadioInterface set CWOutWK= concat(CWOutWK, '$tData'), CWOutWKCk=1 where Radio=$tRadio";

  $tNew = "";
  $con = new mysqli(
    "localhost",
    $sql_radio_username,
    $sql_radio_password,
    $sql_radio_database
  );
  $con->query("LOCK TABLE RadioInterface WRITE;");
  $result = $con->query(
    "SELECT CWOutWK from RadioInterface where Radio=$tRadio;"
  );
  if ($row = $result->fetch_assoc()) {
    $tNew = $row["CWOutWK"];
  }
  $tNew = $tNew . $tData;
  $sQuery = "update RadioInterface set CWOutWK= '$tNew', CWOutWKCk=1 where Radio=$tRadio;";
  //  $con->where("Radio", $tRadio);
  // echo "In concat update: " . $sQuery . "\n";
  //  if ($con->connect_error) {
  //    die("Connection failed: " . $con->connect_error);
  //  }
  //  $con->where("Radio", $tRadio);
  //  $dT = $con->getOne("RadioInterface");
  //  echo "Before: " . $dT["CWOutWK"] . "\n";
  $result = $con->query($sQuery);
  //  $con->where("Radio", $tRadio);
  //  $dT = $con->getOne("RadioInterface");
  //  echo "After: " . $dT["CWOutWK"] . "\n";
  //  if ($dT["CWOutWK"] == "") {
  //    $result = $con->setQueryOption("HIGH_PRIORITY")->rawQuery($sQuery);
  //  }
  //  echo "result: " . $result . "\n";
  $con->query("UNLOCK TABLES;");
  //  $sQuery = "UPDATE RadioInterface SET CWOutWKCk=1, CWOutWK='' where Radio='$tRadio'";
  //  $result1 = $con->query($sQuery);
  //  echo "result1: " . $result . "\n";
  //  return $result;
}

function SetInterface($tRadio, $tField, $tData)
{
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
  $db->where("Radio", $tRadio);
  if ($tField == "MainOut") {
    $data = [
      "MainOut" => $tData,
      "MainOutCk" => "1",
      "MainIn" => $tData,
    ];
  } elseif ($tField == "CWOutWK") {
    $data = [
      "CWOutWK" => $tData,
      "CWOutWKCk" => "1",
    ];
  } elseif ($tField == "SubOut") {
    $data = [
      "SubOut" => $tData,
      "SubOutCk" => "1",
      "SubIn" => $tData,
    ];
  } elseif ($tField == "ModeOut") {
    $data = [
      "ModeOut" => $tData,
      "ModeOutCk" => "1",
      "ModeIn" => $tData,
    ];
  } elseif ($tField == "SplitOut") {
    $data = [
      "SplitOut" => $tData,
      "SplitOutCk" => "1",
      "SplitIn" => $tData,
    ];
  } elseif ($tField == "PTTOut") {
    $data = [
      "PTTOut" => $tData,
      "PTTOutCk" => "1",
      "PTTIn" => $tData,
    ];
  } elseif ($tField == "Transmit") {
    $data = [
      "Transmit" => $tData,
    ];
  } elseif ($tField == "CommandOut") {
    $data = [
      "CommandOut" => $tData,
      "CommandOutCk" => "1",
    ];
  } elseif ($tField == "Slave") {
    $data = [
      "Slave" => $tData,
      "SlaveCk" => "1",
    ];
  } else {
    $data = [
      $tField => $tData,
    ];
  }
  $db->where("Radio", $tRadio);
  $db->update("RadioInterface", $data);
  return "OK";
}

function SetInterfacePartial($tRadio, $tField, $tData)
{
  ini_set("error_reporting", E_ALL);
  ini_set("display_errors", 1);
  require "/var/www/html/programs/sqldata.php";
  $sQuery = "UPDATE RadioInterface SET $tField=replace($tField,'$tData','') where Radio='$tRadio'";
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
}

?>
