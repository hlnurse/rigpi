<?php
if (!isset($GLOBALS['htmlPath'])){
	$GLOBALS['htmlPath']=$_SERVER['DOCUMENT_ROOT'];
}
$dRoot=$GLOBALS['htmlPath'];
$tCall=$_GET["c"];
$tUserName=$_GET["x"];
require_once($dRoot.'/classes/Membership.php');
$membership = New Membership();
$membership->confirm_Member($tUserName);	
require ($dRoot.'/programs/GetMyRadioFunc.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<meta charset="utf-8">

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo $tCall;?> Express User Settings</title>
	<meta name="RigPi Log Editor" content="">
	<meta name="author" content="Howard Nurse, W6HN">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">
    <script src="/Bootstrap/jquery.min.js" ></script>
	<script defer src="./awe/js/all.js" ></script>
	<link href="./awe/css/all.css" rel="stylesheet">
	<link href="./awe/css/fontawesome.css" rel="stylesheet">
	<link href="./awe/css/solid.css" rel="stylesheet">	
	<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
	<link rel="shortcut icon" href="./favicon.ico">
	<link rel="apple-touch-icon" href="./favicon.ico">
	<?php 
		ini_set('error_reporting', E_ALL);
		ini_set('display_errors', 1);
		require($dRoot."/includes/styles.php");
		require($dRoot."/programs/sqldata.php");
		require($dRoot."/programs/timeDateFunc.php");
		require($dRoot."/programs/GetSettingsFunc.php");
		require_once ($dRoot.'/classes/MysqliDb.php');	
		$id=$_GET['id'];
		$tMyRadio=$id;
		$main1='0';
		$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
		if ($id>0){
			$db->where ("uID", $id);
			$row = $db->getOne ("Users");
			if ($row){
				$tID=$row['uID'];
				$call=$row['MyCall'];
				$rpiUser=$row['Username'];
				$access=$row['Access_Level'];
				$accessEdit="";
				if ($tID==1){
					$accessEdit="readonly";
				}
				$fName=$row['FirstName'];
				$lName=$row['LastName'];
				$password='';
				$qrzPWD=$row['qrzPWD'];
				$qrzUser=$row['qrzUser'];
				$qth=$row['QTH'];
				$country=$row['MyCountry'];
				$state=$row['MyState'];
				$county=$row['MyCounty'];
				$city=$row['MyCity'];
				$zip=$row['MyZIP'];
				$continent=$row['MyContinent'];
				$email=$row['My_Email'];
				$phone=$row['My_Phone'];
				$lat=$row['My_Latitude'];
				$lon=$row['My_Longitude'];
				$grid=$row['My_Grid'];
				$mlat=$row['Mobile_Lat'];
				$mlon=$row['Mobile_Lon'];
				$mgrid=$row['Mobile_Grid'];
				$logFldigi=$row['LogFldigi'];
				$logWSJTX=$row['LogWSJTX'];
				$theme=$row['Theme'];
			}
		}else{
				$cols=Array("uID");
				$users = $db->getValue ("Users", "uID", null);
				$numID=$db->count;
				$lastID=0;
				$realID=0;
				$radioID=0;
				if ($numID>0){ //this reuses empty users
					$radioID=$radioID+1;
					foreach ($users as $user) { 
						if ($user-$lastID>1){
							$realID=$lastID;
							break;
						}else{
							$lastID=$user;
						}
    				}
				}
				$tID=$lastID+1;
				$call='';
				$rpiUser='';
				$access='1';
				$accessEdit="";
				$fName='';
				$lName='';
				$password='';
				$qth='';
				$country='';
				$state='';
				$county='';
				$city='';
				$email='';
				$phone='';
				$lat='';
				$lon='';
				$grid='';
				$mlat='';
				$mlon='';
				$mgrid='';
				$qrzPWD='';
				$qrzUser='';
				$zip='';
				$continent='';
				$logFldigi='';
				$logWSJTX='';
				$theme='0';
		}
	 ?>
	<script type="text/javascript">
	var tMyCall="<?php echo $tCall;?>";
	var tCall=tMyCall
    var tUserName="<?php echo $tUserName;?>";
	var tID=<?php echo "'" . $tID . "'"; ?>;
	
	var $el;
	var oldCall=<?php echo "'" . $tCall . "'"; ?>;
	var tLogFldigi=<?php echo "'" . $logFldigi . "'"; ?>;
	var tLogWSJTX=<?php echo "'" . $logWSJTX . "'"; ?>;
	var oldUser=tUserName;
	var mustReenter=false;
	var tTheme=<?php echo "'" . $theme . "'"; ?>;
	$(document).ready(function(){
		var idV=<?php print $_GET['id'];?>;
		$.post('/programs/GetSelectedRadio.php', {un:tUserName}, function(response) 
		{
			tMyRadio=idV;//response;
	
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
					$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tDX, table: "MySettings"});
	                return false;
	            } else  {
	                return true;
	            }
	        });
	
	        $.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
	        {
				$('#searchText').val(response);
		    });
		});
		
		$(document).on('click', '#userCancel', function() {
			openWindowWithPost("/login.php", {
			    status: "loggedout",
			    username: ''});
		});
		
		$(document).on('click', '#fillUser', function(){
			if ($('#callValue').val()=='ADMIN'){
				alert("Please change ADMIN in the Call field to your call and try again.");
			}else{
				$.post("/programs/GetCallbook.php", {call:$('#callValue').val(), what: 'QRZData', user: '1', un: tUserName },function(response){	
					$.post("./programs/GetCallbookField.php", {uID: 1, field: 'His_Name'},function(response){
							var fName=response.substr(0, response.indexOf(" "));
							$("#fnameValue").val(fName);
					});
					$.post("./programs/GetCallbookField.php", {uID: 1, field: 'His_State'},function(response){
							var fName=response;
							$("#stateValue").val(fName);
					});
					$.post("./programs/GetCallbookField.php", {uID: 1, field: 'His_Grid'},function(response){
							var fName=response;
							$("#gridValue").val(fName);
					});
				});
			};
		});
		
		$(document).on('click', '#userSave', function() {
			if (($('#callValue').val().length==0) || ($('#callValue').val()=='ADMIN')){
				$("#modalA-body").html("Please enter your callsign.");			  				
				$("#modalA-title").html("User Settings");
			  	$("#myModalAlert").modal({show:true});
				return;
			}
			if ($('#userValue').val().length==0){
				$("#modalA-body").html("Please enter a unique Username.");			  				
				$("#modalA-title").html("Account Editor");
			  	$("#myModalAlert").modal({show:true});
				return;
			}
			if ($('#userValue').val().indexOf(" ")>0){
				$("#modalA-body").html("Usernames must not contain a space.");			  				
				$("#modalA-title").html("Account Editor");
			  	$("#myModalAlert").modal({show:true});
				return;
			}

			var data = {
				'refID':idV,
				'ID':tID,
				'Access_Level':1,
				'MyCall':$('#callValue').val().toUpperCase(),
				'FirstName':$('#fnameValue').val(),
				'Username':$('#userValue').val(),
				'Password':$('#passwdValue').val(),
				'qrzUser':$('#qrzUserValue').val(),
				'qrzPWD':$('#qrzPWDValue').val(),
				'MyState':$('#stateValue').val(),
				'My_Grid':$('#gridValue').val(),
				'LogFldigi':1,
				'LogWSJTX':1,
				'LastName':'',
				'QTH':'',
				'MyZIP':'',
				'MyContinent':'',
				'MyCountry':'',
				'MyCounty':'',
				'MyCity':'',
				'My_Email':'',
				'My_Phone':'',
				'My_Latitude':'',
				'My_Longitude':'',
				'Mobile_Lat':'',
				'Mobile_Lon':'',
				'Mobile_Grid':'',
				'Theme':'0',
				'DeadMan':10,
				'LastVisit':Date.now()/1000 | 0
			};
			var acc = [];
			$.each(data, function(index, value) {
			    acc.push(index + ': ' + value);
			});
	        $.post("/programs/SetLoggedIn.php", {field: "Username", data: $('#userValue').val()});
	        $.post("/programs/SetLoggedIn.php", {field: "Callsign", data: $('#callValue').val().toUpperCase()});
			$.post('/programs/SetUsers.php',data, function(response) {
				openWindowWithPost("/wizardSettings.php", {
				    c: $('#callValue').val().toUpperCase(),
				    x: $('#userValue').val()
				});
			});
		})
	
		$.getScript("/js/modalLoad.js");
	
	    $(document).on('click', '#logoutButton', function() 
	    {
			openWindowWithPost("/login.php", {
			    status: "loggedout",
			    username: ''});
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
		    window.open(url,"_self");
		    form.submit();
		};                
	
		var tUpdate = setInterval(updateTimer,1000);
		function updateTimer(){
	       $.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:<?php echo "'" . $tCall . "'"; ?>}, function(response) 
	        {
				updateFooter();
			});
	        var now = new Date();
	        var now_hours=now.getUTCHours();
	        now_hours=("00" + now_hours).slice(-2);
	        var now_minutes=now.getUTCMinutes();
	        now_minutes=("00" + now_minutes).slice(-2);
	        $("#fPanel5").text(now_hours+":"+now_minutes+'z');
	  	}
	
		$.getScript("/js/addPeriods.js");
		$("#callValue").focus();
	});

	$(document).on('click', '.userReview', function() {
		var set = "<p>"+"Call: "+$('#callValue').val().toUpperCase()+"</p>";
		set += "<p>"+"Username: "+$('#userValue').val()+"</p>";
		set += "<p>"+"RigPi Password: Encrypted"+"</p>";
		set += "<p>"+"First Name: "+$('#fnameValue').val()+"</p>";
		set += "<p>"+"State: "+$('#stateValue').val()+"</p>";
		set += "<p>"+"Grid Square: "+$('#gridValue').val()+"</p>";
		set += "<p>"+"QRZ Username: "+$('#qrzUserValue').val()+"</p>";
		set += "<p>"+"QRZ Password: Encrypted</p>";
		set += "<hr>"
		set += "<p><p><i><center>"+"Make note of this list, or use select, copy and paste to save these user settings for future reference.</center></i></p></p>";
		$("#modalA-body").html(set);			  				
		$("#modalA-title").html("User Settings Review");
	  	$("#myModalAlert").modal({show:true});
	});


	</script>
</head>

<body class="body-black" >
	<?php require($dRoot."/includes/header.php");?>
	<div class="container-fluid">
		<div class="row"  style="margin-bottom:10px;">
			<div class="col-12  col-lg-4 btn-padding">
			</div>						
			<div class="col-12 col-lg-4 text-center">
				<div class="label label-success text-white pageLabel" style="margin-top:10px;">Express User Settings</div>
			</div>
			<div class="col-12 col-lg-4 btn-padding">
				<button class='btn btn-color' id="userCancel" type='button'>
					< BACK
				</button>
				<button class='btn btn-color userReview' type='button'>
					REVIEW
				</button>
				<button class='btn btn-color' id="userSave" type='button'>
					NEXT >
				</button>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-12 col-lg-2 text-spacer">
			</div>
			<div class="col-12 col-lg-8 text-spacer text-white">
				<div class="row">
					<div class="col text-center">
						Please enter or edit the information in the boxes below. Click REVIEW to confirm the changes, then click NEXT to continue.
					</div>
				</div>
				<div class="row">
					<div class="col-12 col-lg-12 text-spacer text-white-small">
						Express Setup helps you enter your amateur call and location, select a radio, and get started rapidly.<br><br>
						Express Setup appears when you log into RigPi with the Call set to ADMIN, below.<br><br>
						<div class="row>">
						<ol>
						<li><div class="col-12">Replace ADMIN in the Call box with your call.</div></li>
						<li><div class="col-12">Replace admin in the Username box with a unique word to use when you log in.  
						The Username is case insensitive, do not include space characters.</div></li>
						<li><div class="col-12">Enter a password in RigPi PWD if you will be connecting to RSS through the Internet.</div></li>
						<li><div class="col-12">Your optional state abbreviation and grid square can be used to help filter Telnet sites for spot monitoring.</div></li>
						<li><div class="col-12">Callbook settings can be left blank to use the onboard FCC database or enter your QRZ XML credentials 
						to use the QRZ subscription service.</div></li>
						</ol>
						These settings and more can be accessed through the SETTINGS menu.<br><br>
						To skip Express Setup, click any menu tab, above.<br><br>
						</div>
					</div>
				</div> 
			</div>
			<div class="col-12 col-lg-2 text-spacer">
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text input-sm" >Call</span>
					</div>
					<input type="text" class="form-control text-uppercase"  value="<?php echo htmlentities($call); ?>" id="callValue" placeholder="Enter your callsign" title="Callsign for this account, need not be unique" aria-lable="call" aria-describedby="call-addon" autofocus>
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Username</span>
					</div>
					<input type="text" class="form-control" value="<?php echo htmlentities($rpiUser); ?>" id="userValue" aria-lable="user"  placeholder="Enter username" title="Unique username for this account" aria-describedby="user-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">RigPi PWD</span>
					</div>
					<input type="password" class="form-control" value="" id="passwdValue"  placeholder="Enter new password (optional)" title="Leave blank so no password required, not blank when open to Internet" aria-lable="time" aria-describedby="time-addon">
				</div>
			</div>
		</div>

		<div class="row">
		</div>
		<div class="row">
			<div class="col-12 text-spacer text-center">
				<button class="btn btn-outline-success btn-sm my-2 my-sm-0 text-white" id="fillUser"  title="Click to fill fields from Callbook" type="button">
					Fill Below from Callbook
				</button>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">First</span>
					</div>
					<input type="text" class="form-control" value="<?php echo htmlentities($fName); ?>" id="fnameValue" aria-lable="name"  placeholder="Your first name" title="Your first name" aria-describedby="name-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text" >State</span>
					</div>
					<input type="text" class="form-control"  value="<?php echo htmlentities($state); ?>" id="stateValue"  placeholder="Your state 2-letter abbrev (if applicable)" title="Account owner's home state" aria-lable="rsts" aria-describedby="rsts-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Grid Sq</span>
					</div>
					<input type="text" class="form-control"  value="<?php echo htmlentities($grid); ?>" id="gridValue"  placeholder="Your Maidenhead grid" title="Account owner's home Maidenhead gridsquare" aria-lable="grid" aria-describedby="grid-addon">
				</div>
			</div>
		</div>
		<div class="row" style="margin-bottom:10px;">
			<div class="col-12 col-sm-12 text-center">
				<span class="label label-success text-white" style="margin-top:10px;">Callbook</span>
				<hr>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">QRZ User</span>
					</div>
					<input type="text" class="form-control" value="<?php echo htmlentities($qrzUser); ?>" id="qrzUserValue"  placeholder="QRZ username for XML access" title="QRZ username for XML access" value="aria-lable="time" aria-describedby="time-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">QRZ PWD</span>
					</div>
					<input type="password" class="form-control" value="<?php echo htmlentities($qrzPWD); ?>" id="qrzPWDValue"  placeholder="QRZ password for XML access" title="QRZ password for XML access" aria-lable="time" aria-describedby="time-addon">
				</div>
			</div>
			<div class="col-md-4 text-center text-spacer">
			</div>
		</div>
		<div class="row">
			<div id = "result"></div>
		</div>
	</div>
</div>
    <?php require($dRoot.'/includes/modal.txt'); ?>
    <?php require($dRoot.'/includes/footer.php'); ?>
    <?php require($dRoot.'/includes/modalAlert.txt'); ?>

<script src="./Bootstrap/popper.min.js"</script>
<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
<script src="./Bootstrap/jquery-ui.js"></script>
<script src="./Bootstrap/bootstrap.min.js"></script>
</body>
</html>
