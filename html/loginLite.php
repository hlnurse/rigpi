<?php
session_start();
$dRoot="/var/www/html";
require_once($dRoot . "/programs/GetUserFieldFunc.php");
require_once($dRoot . "/programs/GetSettingsFunc.php");
//echo $_SERVER['REQUEST_METHOD'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$username =$_POST['username'] ?? '';
	$username=strtolower($username);
//	echo "un: $username";
	$password = trim($_POST['password']) ?? '';
	$password1 = md5($password);
	$_SESSION['myUsername'] = $username;
	$_SESSION['myRadio'] = getUserField($username,'SelectedRadio');
	$tP1=getUserField($username,'SelectedRadio');
	if ($tP1>0){
		$tP=4530 + (2 * $tP1);
	}else{
		$tP=4534;
	}
//	$tP=4534;
	$_SESSION['myPort']=GetField($_SESSION['myRadio'],'Port','MySettings');
	if (strstr($_SESSION['myPort'],":")){
	  $p=explode(":", $_SESSION['myPort']);
	  $port=$p[1];
	  $ip=$p[0];
	}elseif(strstr($_SESSION['myPort'],"/dev")){
	  $port=$_SESSION['myRadio']*2 + 4530;
	  $ip="127.0.0.1";
	}elseif(strstr($_SESSION['myPort'],"45")){
	  $port=$_SESSION['myPort'];
	  $ip="127.0.0.1";
	}else{
	  $port=4530 + 2 * $_SESSION['myRadio'];
	  $ip='127.0.0.1';
	}
	$_SESSION['port']=$port;
	$_SESSION['ip']=$ip;

//	$_SESSION['myPort']='172.16.0.43:4534';
	$_SESSION['myRadioName'] = GetField($_SESSION['myRadio'],'RadioName','MySettings');
	$instance = "1234" . $_SESSION['myRadio'];//mt_rand(1, 10000); 
	$_SESSION['myInstance']=$instance;
//	echo($_SESSION['myRadioName']);
	$tMyUN=getUserField($username,'Username');
	$validUsername = strtolower($tMyUN);
	if ($password!==""){
		$validPassword = getUserField($username,'Password');
	}else{
		$validPassword = getUserField($username,'Password');
		$password1="";
	}

	// Check if credentials are correct
	if ($username === $validUsername && $password1 === $validPassword) {
		$_SESSION['myCall']=getUserField($username,'MyCall');
		$tP1=getUserField($username,'SelectedRadio');
		$tP=4530 + (2 * $tP1);
//		$_SESSION['myPort']=$tP;
		if (strlen($_SESSION['myRadioName'])>0){
			header('Location: /lite/index.php'); // Redirect to a dashboard or protected page
			exit();
		}else{
			$errorMessage = 'Please try again.';
		}
	} else {
		$errorMessage = 'Invalid username or password!';
	}
	echo($errorMessage);
}else{
//	echo "now";
//	print_r($_SERVER);
}

// Logout logic
//if (isset($_GET['logout'])) {
//	session_destroy();
//	header('Location: loginLite.php'); // Redirect to login page
//	exit();
//}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="author" content="Howard Nurse, W6HN">
	<meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0" />
	<title>Login Lite</title>
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
   </a>RigPi Lite Login
 </p>

	<div class="row justify-content-center">
		<div class="col-md-4">
			<div class="card rounded-lg shadow-lg" style="width: 20rem;">
				<div class="card-body rounded-lg">
					<?php if (!empty($errorMessage)): ?>
						<div class="alert alert-danger"><?php echo htmlspecialchars($errorMessage); ?></div>
					<?php endif; ?>
					<form method="POST" action="">
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
	<div class="label label-success text-center noselect" id="version" style="font-size: 120%; color: #999; margin-top: 10px"></div>
	
	<div class="text-center noselect" style="color: #999; margin-top: 40px">RigPi is crafted by Howard Nurse, W6HN</div>
	<div class="text-center noselect" style="color: white; margin-top: 20px">
		<a href="https://www.rigpi.net" class="hover-underline" title="Go to https://www.rigpi.net">https://www.rigpi.net</a>
	</div>
	<div class="text-center noselect" style="font-style: italic; font-size: 70%; color: #999; margin-top: 20px">RigPi is a Trademark of Howard Nurse, W6HN</div>
</div>
<script src="/Bootstrap/jquery.min.js"></script>
<script src="./Bootstrap/popper.min.js"</script>
<script src="./Bootstrap/bootstrap.min.js"></script>
</body>
</html>
