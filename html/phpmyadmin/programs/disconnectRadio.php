<?php
//session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$dRoot="/var/www/html";
$tc=""; //my call
require_once $dRoot . "/programs/disconnectRadioFunc.php";
require_once $dRoot . "/liteSettings.php"; //used when started by itaself
if (!empty($_SESSION["myCall"])){
  $tc=$_SESSION["myCall"];//=$tc;
};
 if(!empty($_POST["id"])){
  $id = $_POST["id"];
}else{
  $id = $tid;
} 
if(!empty($_POST["radio"])){
  $tMyRadio = $_POST["radio"];
}else{
  $tMyRadio = $tr;
}
if(!empty($_POST["user"])){
  $tUserName = $_POST["user"];
}else{
  $tUserName = $tu;
}
if(!empty($_POST["rotor"])){
  $tMyRotor = $_POST["rotor"];
}else{
  $tMyRotor = $tro;
}
if(!empty($_POST["instance"])){
  $tMyInstance = $_POST["instance"];
}else{
  $tMyInstance = '0';
}
$what= "Closing radio--user: " . $_SESSION['myUsername'] . " " . " post: " . $_POST['user'] . " " . $tMyRadio ." " . $tUserName . " " . $tMyRotor . " " . $tMyInstance;
    error_log(
  date("Y-m-d H:i:s", time()) . " " . $what . PHP_EOL,
  3,
  "/var/log/rigpi-radio.log"
);
disRadio($tMyRadio, $tUserName, $tMyRotor, $tMyInstance);

?>
