
<?php
session_start();
setcookie(session_name(), '', time() - 3600, '/');
//if (strpos($_SESSION['myInstance'],"L")>-1){
//	header("Location: /loginLite.php"); // Redirect to the login page
//}else{
	header("Location: /login.php"); // Redirect to the login page	
//}
exit();
?>
