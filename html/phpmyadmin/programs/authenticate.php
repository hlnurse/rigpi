
<?php
session_start();
if (!isset($_SESSION)) {

echo "start: " . ini_get('session.auto_start'); 
	echo "none: " . PHP_SESSION_NONE;
	echo "\n";
	print_r($_SESSION);
}else{
	echo "Session is already active.";//
}
$tUsername=$_POST['username'];//4534';//$_SESSION['myPort'];//4534;
$tPort=$_POST['port'];
$dRoot="/var/www/html";
require_once($dRoot . "/programs/GetUserFieldFunc.php");
$tCall=getUserField($tUsername, 'MyCall');
$_SESSION['errorMessage']=null;
// Fake authentication check (replace with database verification)
if (1==1){//(($_POST['username'] == $_SESSION['myUsername']) && $_POST['password'] == '') {
	$_SESSION['myUsername'] = $tUsername;  // Set session
	$_SESSION['myCall'] = $tCall;//getUserField($_SESSION['myUsername'],'MyCall');
  // Set session
  	$_SESSION['myPort']=$tPort;
//s	  print_r($_SESSION);
///	  header("Location: /test3.php");

///	exit();
} else {
	$_SESSION['errorMessage']="Invalid credentials. Please try again.";
	exit();
}
?>
