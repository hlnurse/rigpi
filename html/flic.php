<?php
ini_set("error_reporting", E_ALL);
ini_set("display_errors", 1);
$dRoot="/var/www/html";
require $dRoot . "/programs/sqldata.php";
require_once $dRoot . "/classes/MysqliDb.php";
require_once $dRoot . "/programs/SetSettingsFunc.php";
require_once $dRoot . "/programs/relayOn.php";
$num = $_GET["n"];
if (isset($_GET["u"])){
  $username = $_GET["u"];
}else{
  echo "Error: username not found.";
}
if (isset($_GET["p"])) {
  $param = $_GET["p"];
}
//echo $num . " " . $username;
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->where("Username", $username);
$dt = $db->getOne("Users");
if (!$dt){
  echo "Error: username $username not found.";
  exit;
}
$radio = $dt["SelectedRadio"];
$db->where("Radio", $radio);
if ($num == 0) {
  echo "OK, successful test.";
} elseif ($num == 1) {
  //PTT On
  $data = [
    "PTTOut" => "1",
    "PTTOutCk" => "1",
    "PTTIn" => "1",
  ];
  //  echo "here";
  $db->update("RadioInterface", $data);
} elseif ($num == 2) {
  //PTT Off
  $data = [
    "PTTOut" => "0",
    "PTTOutCk" => "1",
    "PTTIn" => "0",
  ];
  $db->update("RadioInterface", $data);
} elseif ($num == 3) {
  //Power On
  $data = [
    "radio" => $radio,
    "user" => $username,
  ];
  //  echo $radio . $username;
  httpPost("127.0.0.1/programs/powerOn.php", $data);
  echo "Power ON";
} elseif ($num == 4) {
  //Power Off
  $data = [
    "radio" => $radio,
    "user" => $username,
  ];
  httpPost("127.0.0.1/programs/powerOff.php", $data);
  echo "Power OFF";
} elseif ($num == 5) {
  //relay 1
  $data = [
    "CommandOut" => "!SW1 " . $param,
    "CommandOutCk" => "1",
  ];
  $db->update("RadioInterface", $data);
} elseif ($num == 6) {
  //relay 1
  $data = [
    "CWIn" => $param,
    //    "CWInCk" => "1"
  ];
  $db->update("RadioInterface", $data);
} elseif ($num == 7) {
  //frequency
  $data = [
    "CommandOut" => str_replace("+", " ", $param),
    "CommandOutCk" => "1",
  ];
  $db->update("RadioInterface", $data);
  echo "<H2>" . $param . " Param OK</H2>";
} elseif ($num == 8) {
  //ft8 on band
  switch ($param) {
    case 80:
      $param = "*F 3573000";
      break;
    case 40:
      $param = "*F 7074000";
      break;
    case 30:
      $param = "*F 10136000";
      break;
    case 20:
      $param = "*F 14074000";
      break;
    case 17:
      $param = "*F 18100000";
      break;
    case 15:
      $param = "*F 21074000";
      break;
    case 12:
      $param = "*F 24915000";
      break;
    case 10:
      $param = "*F 28074000";
      break;
    case 6:
      $param = "*F 50313000";
      break;
    case 2:
      $param = "*F 144174000";
      break;
  }
  $data = [
    "CommandOut" => $param,
    "CommandOutCk" => "1",
  ];
  $db->update("RadioInterface", $data);
  usleep(1000000);
  $param = "*M PKTUSB 3000";
  $data = [
    "CommandOut" => $param,
    "CommandOutCk" => "1",
  ];
  $db->update("RadioInterface", $data);
  echo "<H1>FT8 OK</H2>";
} elseif ($num == 9) {
  //Radio On and connected
  $data = [
    "radio" => $radio,
    "user" => $username,
  ];
  //  echo $radio . $username;
  httpPost("127.0.0.1/programs/connectRadio.php", $data);
} elseif ($num == 10) {
  //Radio USB audio mute
  $param = 0;
  $data = [
    "CommandOut" => "*L USB_AF " . $param,
    "CommandOutCk" => "1",
  ];
  $db->update("RadioInterface", $data);
} elseif ($num == 11) {
  //Radio USB audio unmute
  $param = 0.5;
  $data = [
    "CommandOut" => "*L USB_AF " . $param,
    "CommandOutCk" => "1",
  ];
  $db->update("RadioInterface", $data);
} elseif ($num == 12) {
  //Radio af audio mute
  $param = 0;
  $data = [
    "CommandOut" => "*L AF " . $param,
    "CommandOutCk" => "1",
  ];
  $db->update("RadioInterface", $data);
} elseif ($num == 13) {
  //Radio af audio unmute
  $param = 0.5;
  $data = [
    "CommandOut" => "*L AF " . $param,
    "CommandOutCk" => "1",
  ];
  $db->update("RadioInterface", $data);
}
echo "OK";
function httpPost($url, $data)
{
  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($curl);
  curl_close($curl);
  echo $response;
  return $response;
}
?>
