<?php
/**
 * @author Howard Nurse W6HN
 *
 * This function returns the various data for a given callsign
 *
 * It must live in the RigPi programs folder
 */
ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
require_once "/var/www/html/programs/sqldata.php";
require_once "/var/www/html/programs/GetCallbookFunc.php";
require_once "/var/www/html/programs/GetUserFieldFunc.php";
require_once "/var/www/html/programs/testInternetFunc.php";
require_once "/var/www/html/classes/MysqliDb.php";
$call = strtoupper($_POST["call"]); //call to look up
$getWhat = $_POST["what"];
$user = $_POST["user"]; //user number here
$tUserName = $_POST["un"]; //username
$entity = "";
$whatData = "";
$QRZPassword = getUserField($tUserName, "qrzPWD");
//echo $QRZPassword; //
$internetBad = getInternetOK();
if (strlen($QRZPassword) > 0) {
  //&& $internetBad==0){
  $whatData = "QRZData";
  //  echo $call . $user;
  $result = getCallbookFunc($call, "QRZData", $user);
  //  echo $result;
} else {
  $whatData = "FCC";
  $result = getCallbookFunc($call, "FCCData", $user);
  //  echo $result;
  if ($getWhat == "QRZData") {
    $getWhat = "FCCData";
  }
}
$db = new MysqliDb(
  "localhost",
  $sql_log_username,
  $sql_log_password,
  $sql_log_database
);
$db->where("User", $user);
$row = $db->getOne("Callbook");
//echo $getWhat . $row["Callsign"];
if ($getWhat == "QRZData") {
  if ($row && strlen($row["Callsign"]) > 0) {
    $street = ucwords(strtolower($row["His_Street"]));
    $city = ucwords(strtolower($row["His_City"]));
    $state = $row["His_State"];
    $country = $row["His_Country"];
    $zip = $row["His_Zip"];
    $grid = $row["His_Grid"];
    $name = ucwords(strtolower($row["His_Name"]));
    $email = $row["His_Email"];
    $dMi = $row["His_Distance_Mi"];
    $dKM = $row["His_Distance_KM"];
    $dBearing = $row["Beam_Heading"];
    if (strtolower($dBearing) == "nan") {
      $dBearing = "--";
    }
    $wpx = $row["WPX_Prefix"];
    $cqZ = $row["CQZone"];
    $ituZ = $row["ITUZone"];
    $dxcc = $row["DXCC"];
    $entity = $row["His_Entity"];
    $dxLat = $row["His_Latitude"];
    $dxLon = $row["His_Longitude"];
    $county = $row["His_County"];
    $source = $row["Note"];
    if (strlen($county) > 0) {
      $countyData = "County: $county<br>";
    } else {
      $countyData = "";
    }
    if (strlen($name) > 0) {
      $data =
        "$name, <b>$call</b><br>$street<br>$city, $state $zip " .
        strtoupper($country) .
        "<br><a href='mailto:" .
        $email .
        "' target='_top'>" .
        $email .
        "</a>";
    } else {
      $data = strtoupper($country);
    }
    $data .=
      "<br><br>" .
      "$countyData" .
      "Latitude: $dxLat<br>Longitude: $dxLon<br>Distance: $dMi mi, $dKM km<br>Bearing: $dBearing Deg<br>Grid: $grid<br>DXCC: $entity Number: $dxcc<br>CQ Zone: $cqZ&nbsp;&nbsp;&nbsp;ITU Zone: $ituZ<br>WPX: $wpx<br>Source: $source";
    echo $data;
  } else {
    if ($call == "NG") {
      $call = "Call";
    }
    echo "$call not found in QRZ database.";
  }
} elseif ($getWhat == "FCCData") {
  //	echo "getting FCC data";
  $dxcc = 0;
  if ($row && strlen($row["Callsign"]) > 0) {
    $street = ucwords(strtolower($row["His_Street"]));
    $city = ucwords(strtolower($row["His_City"]));
    $state = $row["His_State"];
    $country = $row["His_Country"];
    $zip = $row["His_Zip"];
    $grid = $row["His_Grid"];
    $name = ucwords(strtolower($row["His_Name"]));
    $email = $row["His_Email"];
    $dMi = $row["His_Distance_Mi"];
    $dKM = $row["His_Distance_KM"];
    $dBearing = $row["Beam_Heading"];
    if ($dBearing == "NaN") {
      $dBearing = "--";
    }
    $wpx = $row["WPX_Prefix"];
    $cqZ = $row["CQZone"];
    $ituZ = $row["ITUZone"];
    $dxcc = $row["DXCC"];
    $entity = $row["His_Entity"];
    $dxLat = $row["His_Latitude"];
    $dxLon = $row["His_Longitude"];
    $county = $row["His_County"];
    if (strlen($county) > 0) {
      $countyData = "County: $county<br>";
    } else {
      $countyData = "";
    }
    if (strlen($name) > 0) {
      $data =
        "$name, <b>$call</b><br>$street<br>$city, $state $zip " .
        strtoupper($country) .
        "<br><a href='mailto:" .
        $email .
        "' target='_top'>" .
        $email .
        "</a>";
    } else {
      $data = strtoupper($country);
    }
    if ($dxcc == 291 || $dxcc == 110 || $dxcc == 6 || $dxcc == 202) {
      $dataS = "From onboard FCC database (locations approximate)";
    } else {
      $dataS = "From onboard databases (locations approximate)";
    }
    $data .=
      "<br><br>" .
      "$countyData" .
      "Latitude: $dxLat<br>Longitude: $dxLon<br>Distance: $dMi mi, $dKM km<br>Bearing: $dBearing Deg<br>Grid: $grid<br>";
    $data .= "DXCC: $entity ($dxcc)<br>CQ Zone: $cqZ&nbsp;&nbsp;&nbsp;ITU Zone: $ituZ<br>WPX: $wpx<br>Source: $dataS";
    echo $data;
  } else {
    if ($call == "NG") {
      $call = "Call";
    }
    echo "$call not found in FCC Database.";
  }
} elseif ($getWhat == "Entity") {
  $entity = $row["His_Entity"] . " (" . $row["DXCC"] . ")";
  echo $entity;
} elseif ($getWhat == "QRZpix") {
  if ($whatData == "FCC") {
    echo "";
  } else {
    $imageURL = $row["ImageURL"];
    $imageHeight = $row["ImageH"];
    $imageWidth = $row["ImageW"];
    if ($imageHeight > 0) {
      echo $imageURL . "|" . $imageHeight . "|" . $imageWidth . "|";
    } else {
      echo "";
    }
  }
} elseif ($getWhat == "QRZbio") {
  if ($whatData == "FCC") {
    echo "";
  } else {
    $bio = $row["His_Bio"];
    echo $bio;
  }
} elseif ($getWhat == "QRZdistMi") {
  $dMi = $row["His_Distance_Mi"];
  echo $dMi;
} elseif ($getWhat == "Abbreviation") {
  $abb = $row["Abbreviation"];
  echo $abb;
} elseif ($getWhat == "QRZdistKM") {
  $dKM = $row["His_Distance_Mi"];
  echo $dKM;
} elseif ($getWhat == "QRZBearing") {
  $dBear = $row["His_Bearing"];
  if ($dBear == "NaN") {
    $dBear = "--";
  }
  echo $dBear;
} elseif ($getWhat == "His_Latitude") {
  $dlat = $row["His_Latitude"];
  echo trim($dlat);
} elseif ($getWhat == "His_Longitude") {
  $dlon = $row["His_Longitude"];
  echo trim($dlon);
} elseif ($getWhat == "Bearing") {
  $dbear = $row["Beam_Heading"];
  echo trim($dbear);
} elseif ($getWhat == "flagSmallURL") {
  $dabr = $row["Abbreviation"];
  echo "/flags/" . $dabr . "-flag.jpg";
}

?>
