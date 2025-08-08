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
//    echo "query: " . $sQuery . " " . bin2hex($tData) . "end\n";
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
//   $tData = hexdec('08');
  ini_set("error_reporting", E_ALL);
  ini_set("display_errors", 1);
  //$tData=substr($tData, 3,1);
  require "/var/www/html/programs/sqldata.php";
  //  $tC = str_replace(chr(30), "", $tData);
  $sQuery =
    "UPDATE Keyer SET $tField = " . $tData . " where Radio = " . $tRadio . ";";
  echo "query: " . $sQuery . " " . $tData . " end\n";
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
//  $tData = "73";
  ini_set("error_reporting", E_ALL);
  ini_set("display_errors", 1);
  require "/var/www/html/programs/sqldata.php";
   $con = new mysqli(
    "localhost",
    $sql_radio_username,
    $sql_radio_password,
    $sql_radio_database
  );
//  $con->query("LOCK TABLE RadioInterface WRITE;");
  $sQuery = "update RadioInterface set CWOut= concat(CWOut, '$tData') where Radio='$tRadio'";
 echo "additive query out: " . $sQuery . "\n";
  //  if ($con->connect_error) {
  //    die("Connection failed: " . $con->connect_error);
  //  }
  $result = $con->query($sQuery);
//  $con->query("UNLOCK TABLES;");
}

function SetCWInAdditive($tRadio, $tData)
{
  //$tRadio = 1;
//  $tData = "73";
echo "setting cwin additive $tData on radio $tRadio";
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
  $result = $con->query($sQuery);
  $con->query("UNLOCK TABLES;");
//}

//echo "additive query in: " . $sQuery . "\n";
//    $sQuery1 = "UPDATE RadioInterface SET CWInUncleared = CONCAT(CWInUncleared, '$tData') WHERE Radio='$tRadio'";
//  if ($con->connect_error) {
//    die("Connection failed: " . $con->connect_error);
//  }
//    $result = $con->query($sQuery1);
}

function SetCWInUnclearedAdditive($tRadio, $tData)
{
  //$tRadio = 1;
  $tData = "xx";
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
//  $tData = "73";
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
//  $tField="SplitOutCk";
//  $tData=1;
//  $tRadio=1;
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
  $sQuery="";
  echo "setinterface $tField $tData\n";
  
  if ($tField == "MainOut" ) {
    $sQuery = "UPDATE RadioInterface SET MainOut='$tData', MainIn='$tData', MainOutCk='1' WHERE Radio=" . $tRadio . " AND MainOutCk='0'";
  } elseif ($tField == "CWIn") {
    $sQuery = "UPDATE RadioInterface SET CWIn='$tData' WHERE Radio=" . $tRadio ;
  } elseif ($tField == "MainIn") {
    $sQuery = "UPDATE RadioInterface SET MainIn='$tData'  WHERE Radio=" . $tRadio ;
  } elseif ($tField == "CWOutWK") {
//    $sQuery = "UPDATE RadioInterface SET CWOutWK='$tData', CWOUtWKCk='1'  WHERE Radio=" . $tRadio . " AND MainOutCk='0'";
    $sQuery = "UPDATE RadioInterface SET CWOutWK='$tData', CWOUtWKCk='$tData'  WHERE Radio=" . $tRadio;
  } elseif ($tField == "SubOut") {
    $sQuery = "UPDATE RadioInterface SET Test='$tData', SubIn='$tData', SubOut='$tData', SubOutCk='1' WHERE Radio=" . $tRadio;
  } elseif ($tField == "ModeOut") {
    $sQuery = "UPDATE RadioInterface SET ModeOut='$tData', ModeOutCk='1' WHERE Radio=" . $tRadio;
  } elseif ($tField == "SplitOut") {
    $sQuery = "UPDATE RadioInterface SET SplitIn='$tData',SplitOut='$tData',SplitOutCk='1' WHERE Radio=" . $tRadio;
  } elseif ($tField == "PTTOut") {
    $sQuery = "UPDATE RadioInterface SET PTTOut='$tData', PTTOutCk='1' WHERE Radio=" . $tRadio . " AND PTTOutCk='0'";
  } elseif ($tField == "Transmit") {
    $sQuery = "UPDATE RadioInterface SET Transmit='$tData' WHERE Radio=" . $tRadio;
  } elseif ($tField == "CommandOut") {
    $sQuery = "UPDATE RadioInterface SET CommandOut='$tData', CommandOutCk='1' WHERE Radio=" . $tRadio . " AND CommandOutCk='0'";
  } elseif ($tField == "Slave") {
    $sQuery = "UPDATE RadioInterface SET Slave='$tData', SlaveCk='1' WHERE Radio=" . $tRadio . " AND SlaveCk='0'";
  } elseif ($tField == "PwrOut") {
    $sQuery = "UPDATE RadioInterface SET PwrOut='$tData', PwrOutCk='1' WHERE Radio=" . $tRadio . " AND PwrOutCk='0'";
  } elseif ($tField == "SplitOutCk") {
    $sQuery = "UPDATE RadioInterface SET SplitOutCk='$tData' WHERE Radio=" . $tRadio ;
  } elseif ($tField == "CWInWKCk") {
    $sQuery = "UPDATE RadioInterface SET CWInWKCk='$tData' WHERE Radio=" . $tRadio ;
  } elseif ($tField == "CWInWK") {
    $sQuery = "UPDATE RadioInterface SET CWInWK='$tData' WHERE Radio=" . $tRadio ;
  } else {
    $sQuery = "UPDATE RadioInterface SET $tField='$tData' WHERE Radio=" . $tRadio;
  }
  $test=$db->rawQuery($sQuery);
  return;// print_r($test);
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
