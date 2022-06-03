<?php
/**
 * @author Howard Nurse W6HN
 *
 * This routine gets QRZ info for specified call.
 *
 * It must live in the programs folder
 */
ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
function getCallbookFunc($call, $getWhat, $user)
{
  //$call = "W6AB";
  //$getWhat = "QRZData";
  //$user = "1";
  $call = strtoupper($call);
  $dRoot = "/var/www/html";
  require $dRoot . "/programs/sqldata.php";
  require_once $dRoot . "/classes/MysqliDb.php";
  $db = new MysqliDb(
    "localhost",
    $sql_radio_username,
    $sql_radio_password,
    $sql_radio_database
  );
  $db->where("User", $user);
  $rowBook = $db->getOne("Callbook");

  if ($rowBook["Callsign"] != $call) {
    //		echo $call . " ". $rowBook['Callsign'] . " " . $getWhat;
    $db->where("uID", $user);
    $row = $db->getOne("Users");
    $key = $row["qrzKey"];
    $pwd = $row["qrzPWD"];
    $qrzUser = $row["qrzUser"];
    $result = "";
    $qrzOK = true;
    clearCallbookRow($user, $db);
    //		echo $getWhat."\n";
    if ($getWhat == "QRZData") {
      if (connection_status() == CONNECTION_NORMAL) {
        if (!($sock = @fsockopen(getHostByName("qrz.com"), 80, $T, $U, 1))) {
          $qrzOK = false;
        } else {
          fclose($sock);
        }
      } else {
        $qrzOK = false;
      }
      if (strlen($call) > 0) {
        if ($qrzOK == true) {
          if (strlen($key) == 0) {
            $qKey = getKey($qrzUser, $pwd, $db, $user);
            $db->where("uID", $user);
            $db->update("Users", ["qrzKey" => $qKey]);
          }
          $result = getQRZData($key, $call, $user, $qrzUser, $db);
          //          print_r($result);
          if (
            $result == "Invalid session key" ||
            $result == "Username / password required" ||
            $result == "Session Timeout"
          ) {
            $qKey = getKey($qrzUser, $pwd, $db, $user);
            $db->where("uID", $user);
            $db->update("Users", ["qrzKey" => $qKey]);
            $result = getQRZData($qKey, $call, $user, $qrzUser, $db);
            echo $qKey;
            //            echo $qrzUser;
            if (
              $result == "Invalid session key" ||
              $result == "Username / password required" ||
              $result == "Session Timeout"
            ) {
              getFCCData($call, $user, $db);
            }
          }
        } else {
          $result = "NO";
        }
        if ($result == "OK") {
          fillGeo($user, $call, $db, "QRZ");
          getISize($user, $db);
        } else {
          getFCCData($call, $user, $db);
        }
      } else {
        return 0;
      }
      fillMyUserDefaults($user, $db);
      fillRadio($user, $db);
      fillDXDistance($user, $db);
    } elseif ($getWhat == "QRZdxcc") {
      $result = getQRZDXCC($key, $call, $user, $qrzUser, $db);
      if (
        $result == "Invalid session key" ||
        $result == "Username / password required" ||
        $result == "Session Timeout"
      ) {
        $qKey = getKey($qrzUser, $pwd, $db, $user);
        $result = getQRZDXCC($qKey, $call, $user, $qrzUser, $db);
      }
    } elseif ($getWhat == "QRZbio") {
      $result = getBio($key, $call, $user, $qrzUser, $db);
      if ($result == "Invalid session key") {
        $qKey = getKey($qrzUser, $pwd, $db, $user);
        $result = getBio($qKey, $call, $user, $qrzUser, $db);
      }
    } elseif ($getWhat == "FCCData") {
      //			echo "FCC data!!!!!!!!!";
      $result = getFCCData($call, $user, $db);
      //			echo $result."\n";
      fillMyUserDefaults($user, $db);
      fillRadio($user, $db);
      fillDXDistance($user, $db);
    }
    //		echo $getWhat;
  }
  return 0;
}

function clearCallbookRow($user, $db)
{
  $tArray = [
    "Callsign" => "",
    "Aliases" => "",
    "DXCC" => "0",
    "His_Grid" => "",
    "His_Name" => "",
    "His_QTH" => "",
    "His_Street" => "",
    "His_City" => "",
    "Note" => "",
    "His_State" => "",
    "His_QTH" => "",
    "His_Latitude" => "",
    "His_Longitude" => "",
    "His_Distance_Mi" => "",
    "His_Distance_KM" => "",
    "His_Email" => "",
    "His_URL" => "",
    "His_County" => "",
    "His_Section" => "",
    "His_Country" => "",
    "His_Continent" => "",
    "His_Entity" => "",
    "Abbreviation" => "",
    "His_FIPS" => "",
    "His_Zip" => "",
    "IOTA" => "",
    "CQZone" => "",
    "ITUZone" => "",
    "WPX_Prefix" => "",
    "Ten_Ten" => "",
    "VE_Province" => "",
    "His_Power" => "",
    "Beam_Heading" => "0",
    "LoTW" => "0",
    "eQSL" => "0",
    "mQSL" => "0",
    "ClubLogQSL" => "0",
    "QRZQSL" => "0",
    "Club" => "",
    "My_Section" => "",
    "My_Contest_Class" => "",
    "Contest_ID" => "",
    "LicenseClass" => "",
    "ImageH" => "",
    "ImageW" => "",
    "ImageURL" => "",
    "TimeZone" => "",
    "DST" => "",
    "GMTOffset" => "",
    "QSLMgr" => "",
    "Transmitter" => "",
    "Receiver" => "",
    "Amplifier" => "",
    "TX_Power" => "",
    "RX_Antenna" => "",
    "Sat_Name" => "",
    "Sat_Mode" => "",
    "Propagation_Mode" => "",
    "His_Bio" => "",
  ];
  $db->where("User", $user);
  $row = $db->update("Callbook", $tArray);
  return "OK";
}

function getKey($qrzUser, $pwd, $db, $user)
{
  $key = "";
  $url = "https://xmldata.qrz.com/xml/current";
  $data = ["username" => $qrzUser, "password" => $pwd, "agent" => "RigPi_1.0"];
  $options = [
    "http" => [
      "header" => "Content-type: application/x-www-form-urlencoded\r\n",
      "method" => "POST",
      "content" => http_build_query($data),
    ],
  ];
  $context = stream_context_create($options);
  $result = file_get_contents($url, false, $context);
  if ($result === false) {
  }

  $xml = simplexml_load_string($result);
  if (strlen((string) $xml->Session->Error) != 0) {
    $db->where("User", $user);
    $tArray = ["qrzError" => "Key: " . (string) $xml->Session->Error];
    $row = $db->update("Callbook", $tArray);
    $key = "";
  } else {
    $db->where("User", $user);
    $tArray = ["qrzError" => "Key: OK"];
    $row = $db->update("Callbook", $tArray);
    $key = (string) $xml->Session->Key;
  }

  return $key;
}

function getQRZData($key, $call, $user, $qrzUser, $db)
{
  $url = "https://xmldata.qrz.com/xml/current";
  $data = ["s" => $key, "callsign" => $call];
  $options = [
    "http" => [
      "header" => "Content-type: application/x-www-form-urlencoded\r\n",
      "method" => "POST",
      "content" => http_build_query($data),
    ],
  ];
  $context = stream_context_create($options);
  $result = file_get_contents($url, false, $context);
  $xml = simplexml_load_string($result);
  $tArray = "";
  $hisName = $xml->Callsign->fname . " " . (string) $xml->Callsign->name;
  $hisName1 = ucwords(strtolower($hisName));
  $wpx = getWPX($call);
  if (strlen((string) $xml->Session->Error) != 0) {
    $db->where("User", $user);
    $tArray = ["qrzError" => (string) $xml->Session->Error];
    $row = $db->update("Callbook", $tArray);
    return (string) $xml->Session->Error;
  }
  if (strlen($xml->Callsign->call) > 0) {
    $tArray = [
      "Callsign" => (string) $xml->Callsign->call,
      "Aliases" => (string) $xml->Callsign->aliases,
      "DXCC" => (string) $xml->Callsign->dxcc,
      "His_Grid" => (string) $xml->Callsign->grid,
      "His_Name" => (string) $hisName1,
      "His_State" => (string) $xml->Callsign->state,
      "His_Street" => (string) $xml->Callsign->addr1,
      "His_City" => (string) $xml->Callsign->addr2,
      "His_QTH" =>
        (string) $xml->Callsign->addr1 .
        ", " .
        (string) $xml->Callsign->addr2 .
        ", " .
        (string) $xml->Callsign->state .
        ", " .
        (string) $xml->Callsign->land .
        " " .
        (string) $xml->Callsign->zip,
      "Note" => "QRZ",
      "His_Email" => (string) $xml->Callsign->email,
      "His_URL" => (string) $xml->Callsign->url,
      "His_County" => (string) $xml->Callsign->county,
      "His_Country" => (string) $xml->Callsign->land,
      "His_FIPS" => (string) $xml->Callsign->fips,
      "His_Continent" => "",
      "His_Zip" => (string) $xml->Callsign->zip,
      "His_Latitude" => (string) $xml->Callsign->lat,
      "His_Longitude" => (string) $xml->Callsign->lon,
      "IOTA" => (string) $xml->Callsign->iota,
      "CQZone" => (string) $xml->Callsign->cqzone,
      "ITUZone" => (string) $xml->Callsign->ituzone,
      "WPX_Prefix" => $wpx,
      "Ten_Ten" => "",
      "VE_Province" => "",
      "LoTW" => (string) $xml->Callsign->lotw,
      "eQSL" => (string) $xml->Callsign->eqsl,
      "mQSL" => (string) $xml->Callsign->mqsl,
      "ClubLogQSL" => 0,
      "QRZQSL" => 0,
      "LicenseClass" => (string) $xml->Callsign->class,
      "ImageURL" => (string) $xml->Callsign->image,
      "TimeZone" => (string) $xml->Callsign->TimeZone,
      "DST" => (string) $xml->Callsign->DST,
      "GMTOffset" => (string) $xml->Callsign->GMTOffset,
      "QSLMgr" => (string) $xml->Callsign->qslmgr,
      "qrzError" => "OK",
    ];
    $db->where("User", $user);
    $row = $db->update("Callbook", $tArray);
    getBio($key, $call, $user, $qrzUser, $db);
    $dxcc = (string) $xml->Callsign->dxcc;
    fillEntity($dxcc, $user, $db);
    return "OK";
  } else {
    fillDXCC($call, $user, $db);
  }
}

function getISize($user, $db)
{
  $db->where("User", $user);
  $rowBook = $db->getOne("Callbook");
  $uri = $rowBook["ImageURL"];
  if (strlen($uri) > 0) {
    list($width, $height) = getimagesize($uri);

    $tArray = ["ImageH" => $height, "ImageW" => $width];
    $db->where("User", $user);
    $rowBook = $db->update("Callbook", $tArray);
  } else {
    $tArray = ["ImageH" => 0, "ImageW" => 0];
    $db->where("User", $user);
    $row = $db->update("Callbook", $tArray);
  }
}

function fillGeo($user, $call, $db, $which)
{
  $dRoot = "/var/www/html";
  require_once $dRoot . "/programs/GetCountyFromZIPFunc.php";
  $db->where("User", $user);
  $rowBook = $db->getOne("Callbook");
  if (
    strlen($rowBook["His_Zip"]) > 0 &&
    ($rowBook["DXCC"] == 291 ||
      $rowBook["DXCC"] == 6 ||
      $rowBook["DXCC"] == 110 ||
      $rowBook["DXCC"] == 202)
  ) {
    $countyInfo = getCountyInfo($rowBook["His_Zip"]);

    if (strlen($countyInfo) > 15) {
      $aCountyInfo = explode("|", $countyInfo);
      if ($which == "FCC") {
        $tArray = [
          "His_County" => $aCountyInfo[0],
          "His_Latitude" => $aCountyInfo[1],
          "His_Longitude" => $aCountyInfo[2],
          "His_Grid" => $aCountyInfo[3],
          "His_FIPS" => $aCountyInfo[4],
          "His_Entity" => $aCountyInfo[5],
          "His_Continent" => $aCountyInfo[6],
          "CQZone" => $aCountyInfo[7],
          "ITUZone" => $aCountyInfo[8],
          "DXCC" => $aCountyInfo[9],
          "His_Section" => $aCountyInfo[10],
          //				'WPX_Prefix'=>$wpx
        ];
      } else {
        $tArray = [
          "His_County" => $aCountyInfo[0],
          "His_Grid" => $aCountyInfo[3],
          "His_FIPS" => $aCountyInfo[4],
          "His_Entity" => $aCountyInfo[5],
          "His_Continent" => $aCountyInfo[6],
          "CQZone" => $aCountyInfo[7],
          "ITUZone" => $aCountyInfo[8],
          "DXCC" => $aCountyInfo[9],
          "His_Section" => $aCountyInfo[10],
        ];
      }
      $db->where("User", $user);
      $row = $db->update("Callbook", $tArray);
    }
  }
  return "OK";
}

function fillRadio($user, $db)
{
  $dRoot = "/var/www/html";
  require_once $dRoot . "/programs/GetInterfaceFunc.php";
  require_once $dRoot . "/programs/GetSettingsFunc.php";
  $db->where("uID", $user);
  $rowUser = $db->getOne("Users");
  $tMyRadio = $rowUser["SelectedRadio"];
  $db->where("User", $user);
  $rowBook = $db->getOne("Callbook");
  $pOut = getRadioData($tMyRadio, "powerOut", "RadioInterface");
  $model = GetField($tMyRadio, "Model", "MySettings");
  $name = GetField($tMyRadio, "RadioName", "MySettings");
  $newRow = [];
  if (strlen($rowBook["Transmitter"]) == 0) {
    if (strlen($name) == 0) {
      $newRow["Transmitter"] = $model;
      $newRow["Receiver"] = $model;
    } else {
      $newRow["Transmitter"] = $name;
      $newRow["Receiver"] = $name;
    }
  }
  if (strlen($rowBook["TX_Power"]) == 0) {
    $newRow["TX_Power"] = $pOut;
  }
  if (count($newRow) > 0) {
    $db->where("User", $user);
    $row = $db->update("Callbook", $newRow);
  }
  return "OK";
}

function getQRZDXCC($key, $call, $user, $qrzUser, $db)
{
  $url = "https://xmldata.qrz.com/xml/current";
  $data = ["s" => $key, "dxcc" => $call];
  $options = [
    "http" => [
      "header" => "Content-type: application/x-www-form-urlencoded\r\n",
      "method" => "POST",
      "content" => http_build_query($data),
    ],
  ];
  $context = stream_context_create($options);
  $result = file_get_contents($url, false, $context);
  if ($result === false) {
  }

  $xml = simplexml_load_string($result);
  if (strlen((string) $xml->Session->Error) != 0) {
    $db->where("User", $user);
    $tArray = ["qrzError" => "DXCC" . (string) $xml->Session->Error];
    $row = $db->update("Callbook", $tArray);
    return (string) $xml->Session->Error;
  } else {
    $tArray = [
      "DXCC" => (string) $xml->dxcc,
      "His_Continent" => (string) $xml->continent,
      "His_Zip" => (string) $xml->zip,
      "CQZone" => (string) $xml->cqzone,
      "ITUZone" => (string) $xml->ituzone,
    ];
  }
  $db->where("User", $user);
  $row = $db->update("Callbook", $tArray);
  return "OK";
}

function getBio($key, $call, $user, $qrzUser, $db)
{
  $url = "https://xmldata.qrz.com/xml/current";
  $data = ["s" => $key, "html" => $call];
  $options = [
    "http" => [
      "header" => "Content-type: application/x-www-form-urlencoded\r\n",
      "method" => "POST",
      "content" => http_build_query($data),
    ],
  ];
  $context = stream_context_create($options);
  $result = file_get_contents($url, false, $context);
  if ($result === false) {
  }
  $tArray = ["His_Bio" => $result];
  $db->where("User", $user);
  $row = $db->update("Callbook", $tArray);
  return "OK";
}

function getFCCData($call, $user, $db)
{
  $db->addConnection("slave", [
    "host" => "localhost",
    "username" => "ham",
    "password" => "7388",
    "db" => "fcc_amateur",
  ]);
  $Callsign = $call;
  $db->where("callsign", $Callsign);
  $db->where("status", "A");
  $row = $db->getOne("fcc_amateur.hd");
  $db->where("fccid", $row["fccid"]);
  $row = $db->getOne("fcc_amateur.en");
  if ($row) {
    $address1 = ucwords(strtolower($row["address1"]));
    $city = ucwords(strtolower($row["city"]));
    $state = $row["state"];
    $zip = substr($row["zip"], 0, 5);
    $name =
      ucwords(strtolower($row["first"])) .
      " " .
      ucwords(strtolower($row["middle"])) .
      " " .
      ucwords(strtolower($row["last"]));
    $tArray = [
      "Callsign" => $Callsign,
      "Aliases" => "",
      "DXCC" => 0,
      "His_Grid" => "",
      "His_Name" => $name,
      "Note" => "FCC",
      "His_Street" => $address1,
      "His_City" => $city,
      "His_State" => $state,
      "His_QTH" => $address1 . ", " . $city . ", " . $state . " " . $zip,
      "His_Email" => "",
      "His_URL" => "",
      "His_County" => "",
      "His_Country" => "US",
      "His_Continent" => "NA",
      "His_Zip" => $zip,
      "IOTA" => "",
      "CQZone" => "3, 4, 5",
      "ITUZone" => "4, 5, 6",
      "WPX_Prefix" => getWPX($Callsign),
      "Ten_Ten" => "",
      "VE_Province" => "",
      "LoTW" => "",
      "eQSL" => "",
      "mQSL" => "",
      "ClubLogQSL" => 0,
      "QRZQSL" => 0,
      "LicenseClass" => "",
      "ImageURL" => "",
      "TimeZone" => "",
      "DST" => "",
      "GMTOffset" => "",
      "QSLMgr" => "",
      "His_Bio" => "",
    ];
    $db->where("User", $user);
    $row = $db->update("Callbook", $tArray);
    fillDXCC($call, $user, $db);
    fillGeo($user, $call, $db, "FCC");
    fillRadio($user, $db);
    return "OK";
  } else {
    fillDXCC($call, $user, $db);
  }
}

function getWPX($call)
{
  $aCall = explode("/", $call);
  if (count($aCall) > 1) {
    if (strlen($aCall[0] > strlen($aCall[1]))) {
      $call = $aCall[0];
    } else {
      $call = $aCall[1];
    }
  }
  $len = strlen($call);
  $find = $call;
  $callWPX = "";
  if (strlen($find) > 2) {
    for ($i = 0; $i <= $len; $i++) {
      if (ord(substr($find, -1)) > 47 && ord(substr($find, -1)) < 58) {
        $callWPX = $find;
        break;
      }
      $find = substr($find, 0, strlen($find) - 1);
      $callWPX = $callWPX . $find . "|";
    }
  }
  return $callWPX;
}

function fillEntity($dxcc, $user, $db)
{
  $db->where("DXCC", $dxcc);
  $row = $db->getOne("Prefixes");
  $entity = $row["Country"];
  $tArray = ["His_Entity" => $entity, "Abbreviation" => $row["Abbreviation"]];
  //	print_r($tArray);
  $db->where("User", $user);
  $row = $db->update("Callbook", $tArray);
  return "OK";
}

function fillMyUserDefaults($user, $db)
{
  $db->where("uID", $user);
  $rowUser = $db->getOne("Users");
  $db->where("User", $user);
  $rowBook = $db->getOne("Callbook");
  $newRow = [];
  if ($rowUser == 1) {
    foreach ($rowUser as $key => $value) {
      if (array_key_exists($key, $rowBook)) {
        if ($rowBook[$key] == "" && strlen($value) > 0) {
          $newRow[$key] = $value;
        }
      }
    }
  }
  if (count($newRow) > 0) {
    $db->where("User", $user);
    $row = $db->update("Callbook", $newRow);
  }
  return "OK";
}

function fillDXCC($call, $user, $db)
{
  $dRoot = "/var/www/html";
  require_once $dRoot . "/programs/GetDXCC.php";
  $response = GetLocationData($call);
  $aResponse = explode("|", $response);
  //	return $Callsign . "|" . $DXCC . "|" . $country . "|" . $latitude . "|" . $longitude . "|" . $time_offset . "|" . $cont . "|" . $abbr . "|" . $itu . "|" . $cq . "|";
  $DXCC = $aResponse[1];
  $tArray = [
    "Callsign" => $call,
    "His_Country" => $aResponse[2],
    "His_Latitude" => $aResponse[3],
    "His_Longitude" => $aResponse[4],
    "His_Continent" => $aResponse[6],
    "Abbreviation" => $aResponse[7],
    "ITUZone" => $aResponse[8],
    "CQZone" => $aResponse[9],
    "Note" => "RigPi, all numbers and map center are approximate",
    "WPX_Prefix" => getWPX($call),
    "DXCC" => $DXCC,
  ];
  $db->where("User", $user);
  $row = $db->update("Callbook", $tArray);
  fillEntity($DXCC, $user, $db);
  //	print_r($tArray);
  return "OK";
}

function fillDXDistance($user, $db)
{
  $dRoot = "/var/www/html";
  require_once $dRoot . "/programs/GetDistanceFunc.php";
  $db->where("User", $user);
  $rowDist = $db->getOne("Callbook");
  $dxlat = $rowDist["His_Latitude"];
  $dxlon = $rowDist["His_Longitude"];
  $db->where("uID", $user);
  $rowDist = $db->getOne("Users");
  $mylat = $rowDist["My_Latitude"];
  $mylon = $rowDist["My_Longitude"];
  $dist = getDistance($dxlat, $dxlon, $mylat, $mylon);
  $aDist = explode("|", $dist);
  $tArray = [
    "His_Distance_Mi" => $aDist[1],
    "His_Distance_KM" => $aDist[2],
    "Beam_Heading" => $aDist[3],
  ];
  $db->where("User", $user);
  $row = $db->update("Callbook", $tArray);
  return "OK";
}

?>
