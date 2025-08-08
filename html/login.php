<?php
session_start();
session_reset();
$dRoot="/var/www/html";
require_once($dRoot . "/programs/GetUserFieldFunc.php");
require_once($dRoot . "/programs/GetSettingsFunc.php");
require_once($dRoot . "/classes/Membership.php");
$membership = new Membership();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username =$_POST['username'] ?? '';
	$username=strtolower($username);
	$password = trim($_POST['password']) ?? '';
	$password1 = md5($password);
	$_SESSION['myUsername'] = $username;
	$_SESSION['myRadio'] = getUserField($username,'SelectedRadio');
	$_SESSION['myRadioName'] = GetField($_SESSION['myRadio'],'RadioName','MySettings');
	$instance = "1234" . $_SESSION['myRadio'];//unique to this account 
	$_SESSION['myInstance']=$instance;

//	echo($_SESSION['myRadioName']);
	$tMyUN=$username;//getUserField($username,'Username');
	$validUsername = strtolower($username);
	if (strlen($password)>0){
		$validPassword = getUserField($username,'Password');
	}else{
		$validPassword = getUserField($username,'Password');
		$password1="";
	}
if (!empty($_POST["status"]) && trim($tMyUN) !== "") {
//	echo $status;
	  $un = $tMyUN;
	  $stat = $_POST["status"];
	  if ($stat == "loggedout") {
		$_POST["username"] = "";
		session_destroy();
		$membership->log_User_Out($un);  //////////////
	  }
	  if ($stat == "reboot") {
		  $membership->Reboot_User($un);
		  include "./programs/reboot.php";
		}
	  if ($stat == "shutdown") {
		  $membership->PowerDown_User_Out($un);
	}
} elseif (strlen($username) > 0) {
  $response1 = $membership->check_user($username, $password1);
  if (!isset($response1)) {
	$userOK = "0";
	$last = filemtime("/var/www/html/my/rc_start.txt"); //this display prevents showing of errors when everything hasn't settled down after reboot
	$elapsed = time() - $last;
	if ($elapsed < 10) {
	  while ($elapsed < 10) {
		sleep(10);
		$last = filemtime("/var/www/html/my/rc_start.txt");
		$elapsed = time() - $last;
	  }
	}
  } else {
	if ($response1 == "NG") {
	  $userOK = "1";
	}
  }
}

	// Check if credentials are correct
	if ($tMyUN === $validUsername && $password1 === $validPassword) {
		$_SESSION['myCall']=getUserField($username,'MyCall');
		$tP1=getUserField($username,'SelectedRadio');
		if ($tP1>0){
			$tP=4534;//4530 + (2 * $tP1);
		}else{
			$tP=4534;
		}
		$_SESSION['myPort']=$tP;
		if (strlen($_SESSION['myRadioName'])>0){
//			header('Location: /index.php'); // Redirect to a dashboard or protected page
//			exit();
		}else{
			$errorMessage = 'Please try again.';
		}
	} else {
		$errorMessage = 'Invalid username or password!';
	}
//	echo($errorMessage);
}else{
//	echo "now";
//	print_r($_SERVER);
}

?>
<script src="/Bootstrap/jquery.min.js"></script>
<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
<script>
$(document).ready(function() {
	$("#username").focus();
	$.post('/programs/checkReser.php', function(response){
		var tID = document.getElementById('reservations'); 
		tID.innerHTML=response;
	});
	
	  $.post('/programs/version.php', function(response){
		  var te=response;
		  te= "RigPi&trade; Station Server, v "+te;
		  $("#version").html(te);
	  });
  });
</script>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>RigPi Login</title>
	<link rel="stylesheet" href="/Bootstrap/bootstrap.min.css">
	<style>
	body {
	   background-color: #444;
	}
	.text-above {
		text-align: center; /* Centers the text */
		color:white;
		font-size: 32px;
	}
	.card {
		border-radius: 15px !important; /* Adjust the rounding */
	}
	.card-body {
		border-radius: 15px !important; /* Apply rounding to the body */
	}
	.card-header {
		color:black;
		font-size: 18px;
	}
	.noselect {
		user-select: none; /* Prevent text selection */
		-webkit-user-select: none; /* Safari */
		-moz-user-select: none; /* Firefox */
		-ms-user-select: none; /* IE/Edge */
		cursor:default;
	}
	.hover-underline:hover {
		color:white;
		text-decoration: underline;
	}
	</style>
</head>
<body>
<div class="container mt-4">
	
<p class="text-above noselect">
   <a target="_blank" rel="noopener noreferrer" id="rigpi" href="https://www.rigpi.net" title="Go to https://www.rigpi.net">
		   <img src="/Images/RigPiW.png" alt="RigPi" title="Go to https://www.rigpi.net" style="margin-top:-8px;width:30px;height:30px;"></img>
   </a>RigPi Login
 </p>

	<div class="row justify-content-center">
		<div class="col-md-4">
			<div class="card rounded-lg shadow-lg" style="width: 20rem;">
				<div class="card-body rounded-lg">
					<?php if (!empty($errorMessage)): ?>
						<div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
					<?php endif; ?>
					<form method="POST" action="/login.php">
						<div class="form-group">
							<label class="noselect" for="username">Username</label>
							<input type="text" name="username" placeholder="Username for account to be used" id="username" class="form-control" required>
						</div>
						<div class="form-group">
							<label class="noselect" for="password">Password</label>
							<input type="password" name="password" placeholder="Password if assigned" id="password" class="form-control">
						</div>
						<button type="submit" class="btn btn-primary btn-block rounded-pill">Login</button>
					</form>
				</div>
			</div>
		</div>
	</div>
	<div class="text-center noselect" style="color: #999; margin-top: 40px" id='reservations'></div>
	<div class="label label-success text-center noselect" id="version" style="font-size: 120%; color: #999; margin-top: 10px"></div>
	
	<div class="text-center noselect" style="color: #999; margin-top: 40px">RigPi is crafted by Howard Nurse, W6HN</div>
	<div class="text-center noselect" style="color: white; margin-top: 20px">
		<a href="https://www.rigpi.net" class="hover-underline" title="Go to https://www.rigpi.net">https://www.rigpi.net</a>
	</div>
	<div class="text-center noselect" style="font-style: italic; font-size: 70%; color: #999; margin-top: 20px">RigPi is a Trademark of Howard Nurse, W6HN</div>
</div>
<script src="./Bootstrap/popper.min.js"</script>
<script src="./Bootstrap/bootstrap.min.js"></script>
</body>
</html>
