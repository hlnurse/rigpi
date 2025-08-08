<?php

/**
 * @author Howard Nurse, W6HN
 *
 * This routine gets radio, rotor and keyer data
 *
 * It must live in the programs folder
 */
function GetOut($radio)
{
  ini_set("error_reporting", E_ALL);
  ini_set("display_errors", 1);
  $tRadio = $radio;
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
//  $con->query("LOCK TABLE RadioInterface WRITE;");
  $result = $con->query($sQuery);
  if ($row = $result->fetch_assoc()) {
//    echo print_r($row) . "\n";
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
      "" . //$row["CWOut"] .
      "`" .
      "" . //$row["CWOutCk"] .
      "`" .
      "" .
//      $row["CWIn"] .
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
      $row["USBAFGain"] .
      "`" .
      $row["USBAFGainCk"] .
      "`" .
      $row["Diff"] .
      "`" .
      $row["waitReset"] .
      "`" .
      $row["BWOut"] .
      "`" .
      $row["BWOutCk"] .
      "`";
  } else {
    $data = "NG";
  }  
//  $con->query("UNLOCK TABLES;");
  return $data;
//  echo $data;
}

function GetOut_PTT($radio)
{
  ini_set("error_reporting", E_ALL);
  ini_set("display_errors", 1);
  $tRadio = $radio;
  $dRoot = "/var/www/html";
  require $dRoot . "/programs/sqldata.php";
  $sQuery = "SELECT *, UNIX_TIMESTAMP() - UNIX_TIMESTAMP(Close_Watch) as Diff from RadioInterface where Radio='$tRadio'";
//  echo $sQuery;
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
      $row["Diff"] .
      "`" .
      $row["waitRefresh"] .
      "`";
  } else {
    $data = "NG";
  }
  return $data;
}

function GetCWOut($tRadio)
{
  require "/var/www/html/programs/sqldata.php";
  $con = new mysqli(
    "localhost",
    $sql_radio_username,
    $sql_radio_password,
    $sql_radio_database
  );
//  $tRadio=2;
  $con->query("LOCK TABLE RadioInterface WRITE;");
  $result = $con->query(
    "SELECT CWOutCk,CWIn,CWChangeCk,CWInitCk, CWOutWK, CWOutWKCk from RadioInterface where Radio=$tRadio;"
  );
  //  $result1 = $con->query(
  //    "UPDATE RadioInterface SET CWOutWK='' where Radio='$tRadio';"
  //  );

  if ($row = $result->fetch_assoc()) {
//    echo "data in $tRadio func: " . $row['CWOutCk'] ."\n";

    $data =
      "" .
      "`" .
      $row["CWOutCk"] .
      "`" .
      $row["CWIn"] .
      "`" .
      $row["CWChangeCk"] .
      "`" .
      $row["CWInitCk"] .
      "`" .
      $row["CWOutWK"] .
      "`" .
      $row["CWOutWKCk"];
    /*    if ($row["CWOutWK"] != "") {
      $result = $con->query(
        "SELECT CWOutWK, CWOutWKCk from RadioInterface where Radio=$tRadio;"
      );
      if ($row1 = $result->fetch_assoc()) {
        echo "CWOutWK: " . $row1["CWOutWK"] . "\n";
        echo "CWOutWKCk: " . $row1["CWOutWKCk"] . "\n";
        if (strlen($row1["CWOutWKCk"]) > 0) {
          $data =
            "" .
            "`" .
            $row["CWOutCk"] .
            "`" .
            $row["CWIn"] .
            "`" .
            $row["CWChangeCk"] .
            "`" .
            $row["CWInitCk"] .
            "`" .
            $row1["CWOutWK"] .
            "`" .
            1;
        }
      }
*/
  }
  $result1 = $con->query(
    "UPDATE RadioInterface SET CWIn='', CWOutWK='' where Radio='$tRadio';"
  );
  $con->query("UNLOCK TABLES;");
  //    echo "CLEARED: " . $row1["CWOutWK"] . "\n";
  //  } else {
  //    $data = "NG";
  //  }
  return $data;
}

function GetWKOut($tRadio)
{
  require "/var/www/html/programs/sqldata.php";
  $con = new mysqli(
    "localhost",
    $sql_radio_username,
    $sql_radio_password,
    $sql_radio_database
  );
  $con->query("LOCK TABLE RadioInterface WRITE;");
  $result = $con->query(
    "SELECT CWOutWKCk, CWOutWK from RadioInterface where Radio=$tRadio;"
  );
  if ($row = $result->fetch_assoc()) {
    $data = $row["CWOutWKCk"] . "`" . $row["CWOutWK"];
  } else {
    $data = "NG";
  }
  $con->query("UNLOCK TABLES;");

  return $data;
}

function GetKeyerOut($radio, $field)
{
  ini_set("error_reporting", E_ALL);
  ini_set("display_errors", 1);
  $tRadio = $radio;
  $tField = $field;
  $dRoot = "/var/www/html";
  require $dRoot . "/programs/sqldata.php";
  $sQuery = "SELECT $tField from Keyer where Radio='$tRadio'";
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
  echo mysqli_error($con);
  if ($row = $result->fetch_assoc()) {
    $data = $row[$tField];
  } else {
    $data = "NG";
  }
  //		echo $data[1];
  return $data;
}

function GetRotorOut($rotor)
{
  ini_set("error_reporting", E_ALL);
  ini_set("display_errors", 1);
  $tRotor = $rotor;
  $dRoot = "/var/www/html";
  require $dRoot . "/programs/sqldata.php";
  $sQuery = "SELECT * from RadioInterface where Radio='$tRotor'";
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
      $row["RotorAzOut"] .
      "`" .
      $row["RotorCk"] .
      "`" .
      $row["RotorElOut"] .
      "`" .
      $row["RotorStop"];
  } else {
    $data = "NG";
  }
  return $data;
}

?>
