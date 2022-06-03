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
require $dRoot . "/programs/GetMyRadioFunc.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<meta charset="utf-8">

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo $tCall; ?> RigPi User Editor</title>
	<meta name="RigPi Account Editor" content="">
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
 ini_set("error_reporting", E_ALL);
 ini_set("display_errors", 1);
 require $dRoot . "/includes/styles.php";
 require $dRoot . "/programs/sqldata.php";
 require $dRoot . "/programs/timeDateFunc.php";
 require $dRoot . "/programs/GetSettingsFunc.php";
 require_once $dRoot . "/classes/MysqliDb.php";
 $id = $_GET["id"];
 $tMyRadio = $id;
 $main1 = "0";
 $db = new MysqliDb(
   "localhost",
   $sql_radio_username,
   $sql_radio_password,
   $sql_radio_database
 );
 if ($id > 0) {
   $db->where("uID", $id);
   $row = $db->getOne("Users");
   if ($row) {
     $tID = $row["uID"];
     $call = $row["MyCall"];
     $rpiUser = $row["Username"];
     $access = $row["Access_Level"];
     $accessEdit = "";
     if ($tID == 1) {
       $accessEdit = "readonly";
     }
     $fName = $row["FirstName"];
     $lName = $row["LastName"];
     $password = "";
     $qrzPWD = $row["qrzPWD"];
     $qrzUser = $row["qrzUser"];
     $qth = $row["QTH"];
     $country = $row["MyCountry"];
     $state = $row["MyState"];
     $county = $row["MyCounty"];
     $city = $row["MyCity"];
     $zip = $row["MyZIP"];
     $continent = $row["MyContinent"];
     $email = $row["My_Email"];
     $phone = $row["My_Phone"];
     $lat = $row["My_Latitude"];
     $lon = $row["My_Longitude"];
     $grid = $row["My_Grid"];
     $mlat = $row["Mobile_Lat"];
     $mlon = $row["Mobile_Lon"];
     $mgrid = $row["Mobile_Grid"];
     $logFldigi = $row["LogFldigi"];
     $logWSJTX = $row["LogWSJTX"];
     $theme = $row["Theme"];
     $deadman = $row["DeadMan"];
   }
 } else {
   $cols = ["uID"];
   $db->orderBy("uID", "asc");
   $users = $db->get("Users", null, $cols);
   $numID = $db->count;
   $lastID = 0;
   $realID = 0;
   $radioID = 0;
   //				if ($numID>0){ //this reuses empty users
   $radioID = $radioID + 1;
   foreach ($users as $value) {
     $t = $value["uID"] - $lastID;
     if ($t > 1) {
       break;
     } else {
       $lastID = $value["uID"];
     }
   }
   $realID = $lastID;
   //				}
   $tID = $realID + 1;
   $call = "";
   $rpiUser = "";
   $access = "1";
   $accessEdit = "";
   $fName = "";
   $lName = "";
   $password = "";
   $qth = "";
   $country = "";
   $state = "";
   $county = "";
   $city = "";
   $email = "";
   $phone = "";
   $lat = "";
   $lon = "";
   $grid = "";
   $mlat = "";
   $mlon = "";
   $mgrid = "";
   $qrzPWD = "";
   $qrzUser = "";
   $zip = "";
   $continent = "";
   $logFldigi = "";
   $logWSJTX = "";
   $theme = "0";
   $deadman = 10;
 }
 ?>
	<script type="text/javascript">
		var tMyCall="<?php echo $tCall; ?>";
		var tCall=tMyCall
        var tUserName="<?php echo $tUserName; ?>";
		var tID=<?php echo "'" . $tID . "'"; ?>;
		var $el;
		var oldCall=<?php echo "'" . $tCall . "'"; ?>;
		var tLogFldigi=<?php echo "'" . $logFldigi . "'"; ?>;
		var tLogWSJTX=<?php echo "'" . $logWSJTX . "'"; ?>;
		var oldUser=tUserName;
		var mustReenter=false;
		var tTheme=<?php echo "'" . $theme . "'"; ?>;
		var tStamp=Date.now();
  		$(document).ready(function(){
	  		var idV=<?php print $_GET["id"]; ?>;
			$.post('/programs/GetSelectedRadio.php', {un:tUserName}, function(response) 
			{
				tMyRadio=response;

	 			$("input").bind("keydown", function(event) 
				{
	                // track enter key
	                var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
	                if (keycode == 13) { // keycode for enter key
		                event.preventDefault();
		                var $this=$(event.target);
		                var index = parseFloat($this.attr('data-index'));
		                $('[data-index="' + (index+1).toString() + '"]').focus();
/*	                    if ($('#searchText').val()==''){
	                    	return false;
	                    }
		                var tDX=$('#searchText').val().toUpperCase();
		                $('#searchText').val(tDX);
	                    document.getElementById('searchButton').click();
						$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tDX, table: "MySettings"});
	                    return false;
*/	                } else  {
	                    return true;
	                }
	            });

		        $.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
		        {
					$('#searchText').val(response);
			    });
			})
			
		
			$(document).on('click', '#userCancel', function() {
				window.open("/users.php?c="+tMyCall+"&x="+tUserName,"_self");
			});
			
			$(document).on('click', '.mycont', function() {
				var text = $(this).text();
				$("#continentVal").val(text);
			});
		
			$(document).on('click', '.mytheme', function() {
				var text = $(this).text();
  				$("#curTheme").val(text);
  				if (text=="Orange"){
	  				tTheme=0;
  				}else if (text=="Night"){
	  				tTheme=1;
  				}else if (text=="LCD"){
	  				tTheme=2;
  				}else if (text=="High Contrast"){
	  				tTheme=3;
  				}
			});

			var textT = '';
			if (tTheme==0){
				textT="Orange";
			}else if (tTheme==1){
				textT="Night";
			}else if (tTheme==2){
				textT="LCD";
			}else if (tTheme==3){
				textT="High Contrast";
			}
			$("#curTheme").val(textT);
			
			$(document).on('click', '#fillUser', function(){
				if ($('#callValue').val()=='ADMIN'){
					alert("Please change ADMIN in the Call field to your call and try again.");
				}else{  
					$.post("/programs/GetCallbook.php", {call:$('#callValue').val(), what: 'QRZData', user: tID, 
						un: tUserName },function(response){	
						$.post("./programs/GetCallbookField.php", {uID: tID, field: 'His_Name'},function(response){
								var fName=response.substr(0, response.indexOf(" "));
								$("#fnameValue").val(fName);
						});
						$.post("./programs/GetCallbookField.php", {uID: tID, field: 'His_State'},function(response){
								var fName=response;
								$("#stateValue").val(fName);
						});
						$.post("./programs/GetCallbookField.php", {uID: tID, field: 'His_Grid'},function(response){
								var fName=response;
								$("#gridValue").val(fName);
						});
						$.post("./programs/GetCallbookField.php", {uID: tID, field: 'His_Name'},function(response){
								var fName=response.substr(response.lastIndexOf(" ")+1);
								$("#lnameValue").val(fName);
						});
						$.post("./programs/GetCallbookField.php", {uID: tID, field: 'His_County'},function(response){
								var fName=response;
								$("#countyValue").val(fName);
						});
						$.post("./programs/GetCallbookField.php", {uID: tID, field: 'His_Street'},function(response){
								var fName=response;
								$("#qthValue").val(fName);
						});
						$.post("./programs/GetCallbookField.php", {uID: tID, field: 'His_City'},function(response){
								var fName=response;
								$("#cityValue").val(fName);
						});
						$.post("./programs/GetCallbookField.php", {uID: tID, field: 'His_Country'},function(response){
								var fName=response;
								$("#countryValue").val(fName);
						});
						$.post("./programs/GetCallbookField.php", {uID: tID, field: 'His_Zip'},function(response){
								var fName=response;
								$("#zipValue").val(fName);
						});
						$.post("./programs/GetCallbookField.php", {uID: tID, field: 'His_Continent'},function(response){
								var fName=response;
								$("#continentVal").val(fName);
						});
						$.post("./programs/GetCallbookField.php", {uID: tID, field: 'His_Email'},function(response){
								var fName=response;
								$("#emailValue").val(fName);
						});
						$.post("./programs/GetCallbookField.php", {uID: tID, field: 'His_Latitude'},function(response){
								var fName=response;
								$("#latValue").val(fName);
						});
						$.post("./programs/GetCallbookField.php", {uID: tID, field: 'His_Longitude'},function(response){
								var fName=response;
								$("#lonValue").val(fName);
						});
					});
				};
			});

			$(document).on('click', '#userSave', function() {
				if ($('#callValue').val().length==0){
					$("#modalA-body").html("Please enter a callsign.");			  				
					$("#modalA-title").html("Account Editor");
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
				if ($('#callValue').val()!=oldCall || $('#userValue').val()!=oldUser){
					mustReenter=true;
				}else{
					mustReenter=false;
				}
				
				var tFl=document.getElementById("logFldigi").checked
			  	if (tFl){
				  	tFl=1;
			  	}else{
				  	tFl=0;
			  	}
				
			  	var tWS=document.getElementById("logWSJTX").checked
			  	if (tWS){
				  	tWS=1;
			  	}else{
				  	tWS=0;
			  	}
				
				var data = {
					'refID':idV,
					'ID':tID,
					'MyCall':$('#callValue').val().toUpperCase(),
					'Username':$('#userValue').val(),
					'Access_Level':$('#accessValue').val(),
					'FirstName':$('#fnameValue').val(),
					'LastName':$('#lnameValue').val(),
					'Password':$('#passwdValue').val(),
					'qrzUser':$('#qrzUserValue').val(),
					'qrzPWD':$('#qrzPWDValue').val(),
					'QTH':$('#qthValue').val(),
					'MyContinent':$('#continentVal').val(),
					'MyCountry':$('#countryValue').val(),
					'MyState':$('#stateValue').val(),
					'MyCounty':$('#countyValue').val(),
					'MyCity':$('#cityValue').val(),
					'MyZIP':$('#zipValue').val(),
					'My_Email':$('#emailValue').val(),
					'My_Phone':$('#phoneValue').val(),
					'My_Latitude':$('#latValue').val(),
					'My_Longitude':$('#lonValue').val(),
					'My_Grid':$('#gridValue').val(),
					'Mobile_Lat':$('#mlatValue').val(),
					'Mobile_Lon':$('#mlonValue').val(),
					'Mobile_Grid':$('#mgridValue').val(),
					'LogFldigi':tFl,
					'LogWSJTX':tWS,
					'Theme':tTheme,
					'LastVisit':tStamp,
					'DeadMan':$('#deadmanValue').val()
				};
				var acc = [];
				$.each(data, function(index, value) {
				    acc.push(index + ': ' + value);
				});

				$.post('/programs/SetUsers.php',data, function(response) {
					$("#modalA-body").html(response);			  				
					$("#modalA-title").html("Account Editor");
				  	$("#myModalAlert").modal({show:true});
				  	if (response.indexOf('unique')>0){
				  		mustReenter=false;
				  	}
				});
			  	
			  	$( "#myModalAlert" ).on("hidden.bs.modal", function(e) {
					if (mustReenter==false){
						window.open("./users.php?c="+tMyCall+"&x="+tUserName,"_self");
					}else{
				  		$.post('./login.php',{status:'loggedout'});
				  		window.location.replace("login.php");
					}
				});
			});
			
				

			if (tLogWSJTX!=1){
				document.getElementById("logWSJTX").checked=false;
			}else{
				document.getElementById("logWSJTX").checked=true;
			}
			if (tLogFldigi!=1){
				document.getElementById("logFldigi").checked=false;
			}else{
				document.getElementById("logFldigi").checked=true;
			}
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
				    window.open("/login.php","_self");
				    form.submit();
				};                

			var tUpdate = setInterval(updateTimer,1000);
			function updateTimer(){
		       $.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:<?php echo "'" .
           $tCall .
           "'"; ?>}, function(response) 
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

			var tUpdate = setInterval(bearingTimer,1000)
			function bearingTimer()
			{
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
	  		$.getScript("/js/addPeriods.js");
	</script>
</head>

<body class="body-black" >
	<?php require $dRoot . "/includes/header.php"; ?>
	<div class="container-fluid">
		<div class="row"  style="margin-bottom:10px;">
			<div class="col-12  col-md-4 btn-padding">
			</div>						
			<div class="col-6 col-md-4 text-center">
				<div class="label label-success text-white pageLabel" style="margin-top:10px;">Account Editor (User: <?php echo $tUserName; ?>)</div>
			</div>
			<div class="col-6 col-md-4 btn-padding">
				<button class='btn btn-color' id="userCancel" type='button'>
					<i class="fas fa-ban fa-lg"></i>
				</button>
				<button class='btn btn-color' id="userSave" type='button'>
					<i class="fas fa-cloud-upload-alt fa-lg"></i>
				</button>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Access</span>
					</div>
					<input type="text" class="form-control" value="<?php echo htmlentities(
       $access
     ); ?>" <?php echo htmlentities(
  $accessEdit
); ?> onfocus="this.select();" data-index="3" id="accessValue" aria-lable="time" placeholder="Access level, 1-10 (least to most restrictive)" title="Use 1 for admin accounts, higher than 1 for others" aria-describedby="time-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text input-sm" >Call</span>
					</div>
					<input type="text" class="form-control text-uppercase"  value="<?php echo htmlentities(
       $call
     ); ?>" onfocus="this.select();" data-index="4"  id="callValue" placeholder="Enter user's callsign" title="Callsign for this account, need not be unique" aria-lable="call" aria-describedby="call-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Username</span>
					</div>
					<input type="text" class="form-control" value="<?php echo htmlentities(
       $rpiUser
     ); ?>" onfocus="this.select();" data-index="5" id="userValue" aria-lable="user"  placeholder="Enter username" title="Unique username for this account" aria-describedby="user-addon">
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">RigPi PWD</span>
					</div>
					<input type="password" class="form-control" value="<?php echo htmlentities(
       $password
     ); ?>" onfocus="this.select();" data-index="6" id="passwdValue"  placeholder="Enter new password" title="Leave blank so no password required, not blank when open to Internet" aria-lable="time" aria-describedby="time-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Deadman</span>
					</div>
					<input type="text" class="form-control" value="<?php echo htmlentities(
       $deadman
     ); ?>" onfocus="this.select();" data-index="7" id="deadmanValue"  placeholder="Enter transmit max mins" title="Use 0 for no limit, or number of minutes allowed for transmit" aria-lable="deadman" aria-describedby="deadman-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Theme</span>
					</div>
					<input type="text" class="form-control disable-text" readonly id="curTheme"  title="Theme" aria-lable="theme" aria-describedby="theme-addon">
				    <span class="input-group-btn">
			            <div class="dropdown">
						    <button class="btn btn-primary dropdown-toggle" data-index="8" id="themeSel" data-size="3" type="button" title="Theme List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
						    </button>
						    <ul class="dropdown-menu dropdown-menu-right menu-scroll" id="keyerList">
								<div class='mytheme' id='non'><li><a class='dropdown-item' href='#'>Orange</a></li></div>
								<div class='mytheme' id='rpk'><li><a class='dropdown-item' href='#'>Night</a></li></div>
								<div class='mytheme' id='cat'><li><a class='dropdown-item' href='#'>LCD</a></li></div>
								<div class='mytheme' id='wkr'><li><a class='dropdown-item' href='#'>High Contrast</a></li></div>
						     </ul>
			            </div>
				    </span>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-12 text-spacer text-center">
				<button class="btn btn-outline-success btn-sm my-2 my-sm-0 text-white" data-index="9" id="fillUser"  title="Click to fill fields from Callbook" type="button">
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
					<input type="text" class="form-control" value="<?php echo htmlentities(
       $fName
     ); ?>" onfocus="this.select();" data-index="10" id="fnameValue" aria-lable="name"  placeholder="First name" title="Account owner's first name" aria-describedby="name-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Last</span>
					</div>
					<input type="text" class="form-control" value="<?php echo htmlentities(
       $lName
     ); ?>" onfocus="this.select();" data-index="11" id="lnameValue" aria-lable="last"  placeholder="Last name" title="Account owner's last name" aria-describedby="last-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Street</span>
					</div>
					<input type="text" class="form-control" value="<?php echo htmlentities(
       $qth
     ); ?>" onfocus="this.select();" data-index="12" id="qthValue"  placeholder="Owner's street name and number" title="Account owner's home street address" aria-lable="time" aria-describedby="main-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">City</span>
					</div>
					<input type="text" class="form-control" value="<?php echo htmlentities(
       $city
     ); ?>" onfocus="this.select();" data-index="13" id="cityValue"  placeholder="Owner's city name" title="Account owner's home city" aria-lable="time" aria-describedby="split-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text" >County</span>
					</div>
					<input type="text" class="form-control"  value="<?php echo htmlentities(
       $county
     ); ?>" onfocus="this.select();" data-index="14" id="countyValue"  placeholder="Owner's county name" title="Account owner's home county" aria-lable="band" aria-describedby="call-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text" >State</span>
					</div>
					<input type="text" class="form-control"  value="<?php echo htmlentities(
       $state
     ); ?>" onfocus="this.select();" data-index="15" id="stateValue"  placeholder="Owner's state name" title="Account owner's home state" aria-lable="rsts" aria-describedby="rsts-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text" >Country</span>
					</div>
					<input type="text" class="form-control"  value="<?php echo htmlentities(
       $country
     ); ?>" onfocus="this.select();" data-index="16" id="countryValue"  placeholder="Owner's country name" title="Account owner's home country" aria-lable="rstr" aria-describedby="rstr-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text" >ZIP</span>
					</div>
					<input type="text" class="form-control"  value="<?php echo htmlentities(
       $zip
     ); ?>" onfocus="this.select();" data-index="17" id="zipValue"  placeholder="Owner's ZIP code" title="Account owner's home ZIP code" aria-lable="rstr" aria-describedby="rstr-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Continent</span>
					</div>
					<input type="text" class="form-control disable-text" readonly id="continentVal" value="<?php echo htmlentities(
       $continent
     ); ?>"  title="Account holder Continent" aria-lable="cont" aria-describedby="cont-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" data-index="8" id="contSel" data-size="3" type="button" title="Continent List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right menu-scroll" id="contList">
								<div class='mycont' id='na'><li><a class='dropdown-item' href='#'>NA</a></li></div>
								<div class='mycont' id='eu'><li><a class='dropdown-item' href='#'>EU</a></li></div>
								<div class='mycont' id='sa'><li><a class='dropdown-item' href='#'>SA</a></li></div>
								<div class='mycont' id='af'><li><a class='dropdown-item' href='#'>AF</a></li></div>
								<div class='mycont' id='as'><li><a class='dropdown-item' href='#'>AS</a></li></div>
								<div class='mycont' id='oc'><li><a class='dropdown-item' href='#'>OC</a></li></div>
								<div class='mycont' id='an'><li><a class='dropdown-item' href='#'>AN</a></li></div>
							 </ul>
						</div>
					</span>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text" >Email</span>
					</div>
					<input type="text" class="form-control"  value="<?php echo htmlentities(
       $email
     ); ?>" onfocus="this.select();" data-index="19" id="emailValue"  placeholder="Owner's email address" title="Account owner's home email address" aria-lable="rstr" aria-describedby="rstr-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Phone</span>
					</div>
					<input type="text" class="form-control" value="<?php echo htmlentities(
       $phone
     ); ?>" onfocus="this.select();" data-index="20" id="phoneValue"  placeholder="Owner's phone number" title="Account owner's home telephone number" aria-lable="name" aria-describedby="name-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Grid Sq</span>
					</div>
					<input type="text" class="form-control"  value="<?php echo htmlentities(
       $grid
     ); ?>" onfocus="this.select();" data-index="21" id="gridValue"  placeholder="Owner's Maidenhead grid" title="Account owner's home Maidenhead gridsquare" aria-lable="grid" aria-describedby="grid-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text" >Latitude</span>
					</div>
					<input type="text" class="form-control"  value="<?php echo htmlentities(
       $lat
     ); ?>" onfocus="this.select();" data-index="22" id="latValue"  placeholder="Owner's latitude" title="Account owner's home latitude" aria-lable="qsls" aria-describedby="qsls-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Longitude</span>
					</div>
					<input type="text" class="form-control" value="<?php echo htmlentities(
       $lon
     ); ?>" onfocus="this.select();" data-index="23" id="lonValue"  placeholder="Owner's longitude" title="Account owner's home longitude" aria-lable="qslr" aria-describedby="qslr-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text" >M Lat</span>
					</div>
					<input type="text" class="form-control"  value="<?php echo htmlentities(
       $mlat
     ); ?>" onfocus="this.select();" data-index="24" id="mlatValue"  placeholder="Owner's mobile latitude" title="Account owner's mobile latitude" aria-lable="ituz" aria-describedby="ituz-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">M Lon</span>
					</div>
					<input type="text" class="form-control" value="<?php echo htmlentities(
       $mlon
     ); ?>" onfocus="this.select();" data-index="25" id="mlonValue"  placeholder="Owner's mobile longitude" title="Account owner's home longitude" aria-lable="wpx" aria-describedby="wpx-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">M Grid</span>
					</div>
					<input type="text" class="form-control" value="<?php echo htmlentities(
       $mgrid
     ); ?>" onfocus="this.select();" data-index="26" id="mgridValue"  placeholder="Owner's mobile Maidenhead grid" title="Account owner's mobile Maidenhead gridsquare" aria-lable="dxcc" aria-describedby="dxcc-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
			</div>
		</div>
		<div class="row">
			 <div class="col-md-4 text-center text-spacer">
			 	<div class="form-check form-check-inline">
				 	<label class="form-check-label text-white  text-center">
				 	<input type="checkbox" onfocus="this.select();" data-index="27" id="logWSJTX" title="The log used by this account syncs with WSJT-X" value=" class="form-check-input">
				 		Sync WSJTX Log
				 	</input>
				 	</label>
			 	</div>
			 </div>
			 <div class="col-md-4 text-center text-spacer">
			 	<div class="form-check form-check-inline">
				 	<label class="form-check-label  text-white text-center">
				 	<input type="checkbox" onfocus="this.select();" data-index="28" id="logFldigi" title="The log used by this account syncs with Fldigi" value="" class="form-check-input">
				 		Sync Fldigi Log
				 	</input>
				 	</label>
			 	</div>
			 </div>
			<div class="col-md-4 text-spacer">
			</div>
		</div>
		<div class="row">
			<div class="col-12 text-center">
				<span class="label label-success text-white" style="margin-top:20px;">Callbook</span>
				<hr>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">QRZ User</span>
					</div>
					<input type="text" class="form-control" value="<?php echo htmlentities(
       $qrzUser
     ); ?>" data-index="65" id="qrzUserValue"  placeholder="QRZ username for XML access" title="QRZ username for XML access" value="aria-lable="time" aria-describedby="time-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">QRZ PWD</span>
					</div>
					<input type="password" class="form-control" value="<?php echo htmlentities(
       $qrzPWD
     ); ?>" data-index="65" id="qrzPWDValue"  placeholder="QRZ password for XML access" title="QRZ password for XML access" aria-lable="time" aria-describedby="time-addon">
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
    <?php require $dRoot . "/includes/modal.txt"; ?>
    <?php require $dRoot . "/includes/footer.php"; ?>
    <?php require $dRoot . "/includes/modalAlert.txt"; ?>

<script src="./Bootstrap/popper.min.js"</script>
<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
<script src="./Bootstrap/jquery-ui.js"></script>
<script src="./Bootstrap/bootstrap.min.js"></script>
</body>
</html>
