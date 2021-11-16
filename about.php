<?php
if (!isset($GLOBALS["htmlPath"])) {
  $GLOBALS["htmlPath"] = $_SERVER["DOCUMENT_ROOT"];
}
$dRoot = $GLOBALS["htmlPath"];
$tCall = $_GET["c"];
$tUserName = $_GET["x"];
require_once $dRoot . "/classes/Membership.php";
$membership = new Membership();
$membership->confirm_Member($tUserName);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html lang="en">
	<head>
		<meta charset="utf-8">

		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title><?php echo $tCall; ?> RigPi About</title>
		<meta name="description" content="">
		<meta name="author" content="Howard Nurse">

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<!-- Bootstrap CSS -->
		<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
		<link rel="shortcut icon" href="/favicon.ico">
		<link rel="apple-touch-icon" href="/favicon.ico">
		<link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">
		<script src="/Bootstrap/jquery.min.js" ></script>
		<script defer src="./awe/js/all.js" ></script>
		<link href="./awe/css/all.css" rel="stylesheet">
		<link href="./awe/css/fontawesome.css" rel="stylesheet">
		<link href="./awe/css/solid.css" rel="stylesheet">	

		<?php require $_SERVER["DOCUMENT_ROOT"] . "/includes/styles.php"; ?>

		 <script type="text/javascript">
			 var tMyRadio=1;
			 var dxLat='';
			 var dxLon='';
			 var tDX='';
			var tUserName=<?php echo "'" . $tUserName . "'"; ?>;
			var tUser='';
			$(document).ready(function() {
				$.post('./programs/GetSelectedRadio.php', {un: tUserName}, function(response) 
				{
					tMyRadio=response;
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
					{
						$('#searchText').val(response);
						var tDX=response;
					});
				});	
				$.getScript("/js/modalLoad.js");
			});

			$(document).on('click', '#logoutButton', function() 
			{
				openWindowWithPost("/login.php", {
					status: "loggedout",
					username: tUserName});
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

			  $.post('/programs/version.php', function(response){
				  var te=response;
				  te= "RigPi Station Server, v "+te;
				  $("#version").text(te);
			  });

			$("input").bind("keydown", function(event) 
			{
				// track enter key
				var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
				if (keycode == 13) { // keycode for enter key
					if ($('#searchText').val()==''){
						return false;
					}
					var tDX=$('#searchText').val().toUpperCase();
					$('#searchText').val(tDX);
					document.getElementById('searchButton').click();
					$.post("/programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tDX, table: "MySettings"});
					return false;
				} else  {
					return true;
				}
			});

			$(document).on('click', '#searchButton', function() 
			{
				var dx=$('#searchText').val().toUpperCase();
				if (dx.length==0 || ~dx.indexOf('*')){
					return;
				}
					$.post('/programs/GetUserField.php',{un: tUserName, field: 'uID'}, function(response) {
						tUser=response;
						$.post("/programs/GetCallbook.php", {call: dx, what: 'QRZData', user: tUser, un: tUserName},function(response){
							$(".modal-body").html(response);
						  $.post("/programs/GetCallbook.php", {call: dx, what: 'QRZpix', user: tUser, un: tUserName},function(response){
							  var aPix=response.split('|');
							  var h=aPix[1];
							  var w=aPix[2];
							  if (h>0){
								  var wP=(aPix[2]/280);
								  var tW=w/wP;
								  var tH=h/wP;
								  $(".modal-pix").attr("height",tH+"px");
								  $(".modal-pix").attr("width",tW+"px");
								  $(".modal-pix").attr("src",aPix[0]);
							  }else{
								  $(".modal-pix").attr("height","0px");
								  $(".modal-pix").attr("width","0px");
								  $(".modal-pix").attr("src",'about:blank');
							  }
							  $('.modal-title').html(dx);
							  $('#myModal').modal({show:true});
						});
					});
					$.post("/programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: dx, table: "MySettings"});
				})
			
			});
			
			var tUpdate = setInterval(updateTimer,1000);
			function updateTimer(){
				var now = new Date();
				var now_hours=now.getUTCHours();
				now_hours=("00" + now_hours).slice(-2);
				var now_minutes=now.getUTCMinutes();
				now_minutes=("00" + now_minutes).slice(-2);
				$("#time").text(now_hours+":"+now_minutes+' utc');
				$.post("./programs/GetRotorIn.php", {rotor: tMyRadio},function(response){
					var tAData=response.split('`');
					tAData=response;
					if (tAData=="+"){
						tAData="--";
					}
					var tAz=Math.round(tAData)+"&#176;";
					$(".angle").html(tAz);
				});
			  }

//			function relativeRedir(redir){
//			  location.pathname = location.pathname.replace(/(.*)\/[^/]*/, "$1/"+redir);
//			}
		</script>
	</head>
	<body class="body-black  margin-left:auto;margin-right:auto;" id="About">
		<?php require $dRoot . "/includes/header.php"; ?>
		<div class="container-fluid">
	<?php require $dRoot . "/includes/modal.txt"; ?>
	<div class="row">
		<div class="mx-auto" style="color: white; margin-top:100px; width=400px;">
			RigPi Station Server by Howard Nurse, W6HN
		</div>
	</div>
		<div class="label label-success text-center" id="version" style="color: #fff; margin-top: 75px"></div>
	<div class="row">
		<div class="mx-auto" style="color: white; margin-top:100px; width=400px;">
			RigPi Station Server is open-source and licensed under the GNU GPL License.
		</div>
	</div>
	<div class="row">
		<div class="mx-auto" style="color: white; margin-top:10px; width=400px;">
			Other included software is licensed under other licenses, see the associated code for details.
		</div>
	</div>
	<div class="row">
		<div class="mx-auto" style="color: white; margin-top:10px; width=400px;">
			RigPi is a trademark (tm) of Howard Nurse.
		</div>
	</div>
	<div class="row">
	<div class="mx-auto" style="color: white; width=400px;margin-top:10px; ">
		https://www.rigpi.com
	</div>
	</div>
		</div>
		 <script src="Bootstrap/popper.min.js"</script>
		<link rel="stylesheet" href="Bootstrap/jquery-ui.css">
		<script src="Bootstrap/jquery-ui.js"></script>
		<script src="Bootstrap/bootstrap.min.js"></script>
		<script src="js/nav-active.js"></script>
</body>
