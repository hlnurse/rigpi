<?php

session_start();
$newUser = false;
$userOK = 0;
ini_set("error_reporting", E_ALL);
ini_set("display_errors", "on");
require_once "/var/www/html/programs/sqldata.php";
require_once "/var/www/html/classes/MysqliDb.php";
require_once "./classes/Membership.php";
if ($_POST && !empty($_POST["inputPassword"])) {
  $pwd = $_POST["inputPassword"];
} else {
  $pwd = "";
}
if ($_POST && !empty($_POST["username"])) {
  $uname = $_POST["username"];
} else {
  $uname = "";
}
$membership = new Membership();
if (!empty($_POST["status"]) && trim($uname) != "") {
  $un = $uname;
  $stat = $_POST["status"];
  if ($stat == "loggedout") {
    $_POST["username"] = "";
    $membership->log_User_Out($un);
  }
  if ($stat == "reboot") {
    $membership->Reboot_User($un);
    include "./programs/reboot.php";
  }
  if ($stat == "shutdown") {
    $membership->PowerDown_User_Out($un);
  }
} elseif ($_POST && strlen($uname) > 0) {
  $response1 = $membership->check_user($uname, $pwd);
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
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="RigPi Login">
    <meta name="author" content="Howard Nurse, W6HN">
    <link rel="icon" href="./favicon.ico">

    <title>RigPi Signin</title>

    <!-- Bootstrap core CSS -->
	<link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">

    <!-- Custom styles for this template -->
    <link href="./includes/signin.css" rel="stylesheet">
    <script src="/Bootstrap/jquery.min.js" ></script>
    <script src="./js/mscorlib.js" type="text/javascript"></script>
	<script>
	    $(document).ready(function() {
			$.post('/programs/checkReser.php', function(response){
				var tID = document.getElementById('reservations'); 
				tID.innerHTML=response;
			});
			
	  		$.post('/programs/version.php', function(response){
		  		var te=response;
		  		te= "RigPi&trade; Station Server, v "+te;
		  		$("#version").html(te);
	  		});
	  		var tOK=<?php echo "'" . $userOK . "';"; ?>
	  		if(tOK=='1') {
	  			$("#modalA-body").html("<br>Please try again.<br><br>");			  				
	  			$("#modalA-title").html("RigPi Login");
	  			$("#myModalAlert").modal({show:true});
	  			<?php $userOK = 0; ?>
	  		}
		});
		
			$(document).on('click', '#closeModal', function() {
		  		$("#username").focus();
			});

			$(document).on('click', '#shutDownButton', function() {
				$("#modalS-body").html("<br>OK to shut down RigPi?<br><br>Wait for the green activity light to stop blinking before removing power.");
				$("#modalS-title").html("Shut Down RigPi");
			  	$("#myShutDownAlert").modal({show:true});
			});

			$(document).on('click', '#modalShutDownOK', function() {
				$("#myShutDownAlert").modal('hide');
		  		$.post('./programs/shutdown.php');
			});

			function openWindowWithPost(url, data) {
			    var form = document.createElement("form");
			    form.target = "_self";
			    form.method = "POST";
			    form.action = url;
			    form.style.display = "none";
			
			    for (var key in data) {
			        var input = document.createElement("input");
			        input.type = "hidden";
			        input.name = key;
			        input.value = data[key];
			        form.appendChild(input);
			    }
			
			    document.body.appendChild(form);
  				window.location.replace(url);
			    form.submit();
			};                
		  	
	</script>
    </head>

  <body>

    <div class="container">

      <form method="post" action="" class="form-signin">
	  <div class="image" id="pi">
	  		<img src="./Images/RigPiW.png" alt="RigPi">
	  </div>
	  <p>
        <h4 class="form-signin-heading">Please sign in:</h4>
        <label for="username" class="sr-only">Username</label>
        <input type="text" id="username" autofocus onfocus="this.select()" name="username" class="form-control" placeholder="Username"></input>
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="Password"></input>
        <button class="btn btn-lg btn-primary btn-block" id="login" >Sign In</button>
		<div class="text-center" style="margin-left: -250px; width: 800px; color: #999; margin-top: 40px" id='reservations'></div>
		<div class="label label-success text-center" id="version" style="font-size: 120%; color: #999; margin-top: 50px"></div>
        
		<div class="text-center" style="color: #999; margin-top: 40px">by Howard Nurse, W6HN</div>
        <div class="text-center" style="color: #999; margin-top: 20px">https://www.rigpi.net</div>
        <div class="text-center" style="font-style: italic; font-size: 70%; color: #999; margin-top: 20px">RigPi is a Trademark of Howard Nurse, W6HN</div>
      </form>
	  </div>
	  <!--The Shut Down button can be enabled by uncommenting.  Since anyone can see the login window, it has been commented for security reasons.
			<div class="col-md-12 text-center text-spacer">
				<button class="btn btn-outline-danger btn-sm my-2 my-sm-0 text-white" id="shutDownButton"  title="Click to Shut Down RigPi" type="button">
					<i class="fas fa-power-off"></i>
					Shut Down RigPi
				</button>
			</div>
		-->
<?php  ?>
	<!-- The Modal -->
	<div class="modal" id="loginModal" tabindex="-1" role="dialog">
	    <div class="modal-dialog" role="document">
	    	<div class="modal-content">
				<div class="modal-header">
					<h2 class="modal-title">Shutdown RigPi?</h2>
					<button type="button" class="close" id = "closeModal" data-dismiss="modal">&times;</button>
					</button>
	      		</div>
		  		<div class="modal-body">
	    		</div>
				<div class="modal-footer">
					<button class="loginClose btn btn-primary" type="button" data-dismiss="modal">Close</button>
	    		</div>
	    	</div>
	  	</div>
	</div>
</div> <!-- /container -->
<?php require dirname(__FILE__) . "/includes/modalAlert.txt"; ?>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<?php require "/var/www/html/includes/modalCancelShutdown.txt"; ?>
<script src="./Bootstrap/popper.min.js"</script>
<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
<script src="./Bootstrap/jquery-ui.js"></script>
<script src="./Bootstrap/bootstrap.min.js"></script>
</body>
</html>

