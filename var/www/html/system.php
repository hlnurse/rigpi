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
<html lang="en">
<head>
	<meta charset="utf-8">

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo $tCall; ?> RigPi Radio Settings</title>
	<meta name="RigPi Settings" content="">
	<meta name="author" content="Howard Nurse, W6HN">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">
	<script defer src="./awe/js/all.js" ></script>
	<link href="./awe/css/all.css" rel="stylesheet">
	<link href="./awe/css/fontawesome.css" rel="stylesheet">
	<link href="./awe/css/solid.css" rel="stylesheet">	
    <script src="/Bootstrap/jquery.min.js" ></script>
	<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="apple-touch-icon" href="/apple-touch-icon.png">
	<?php require $dRoot . "/includes/styles.php"; ?>
	<script type="text/javascript">
		var tMyRadio;
        var tUserName="<?php echo $tUserName; ?>";
        var tCall="<?php echo $tCall; ?>";
        var tNoInternet=0;
        var tTest=0;
  		$(document).ready(function(){
            	$.post('/programs/testInternet.php',function(response){
            		if (response !=0){
	            		tNoInternet=1;
            		}
				if (tNoInternet==1){
					$("#modalA-body").html("<br>RSS cannot reach the Internet so can't check for IP addresses. (Try refreshing the System page.)<br><br>");			  				
					$("#modalA-title").html("No Internet");
				  	$("#myModalAlert").modal({show:true});
					$("#lan1").val("N/A");
					$("#lan2").val("N/A");
					$("#wanIP").val("N/A");
				}else{
					$.post("/programs/GetInfo.php", {what: 'IPAdr'}, function(response){
						var aData=response.split('+');
						$("#lan1").val(aData[0]);
						$("#lan2").val(aData[1]);
						$("#wanIP").val(aData[2]);
					});
				};
            	});
            	
			$('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
				if (!$(this).next().hasClass('show')) {
					$(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
					}
					var $subMenu = $(this).next(".dropdown-menu");
					$subMenu.toggleClass('show');
					
					
					$(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
					$('.dropdown-submenu .show').removeClass("show");
				});
				return false;
			});

	  		$.post('/programs/version.php', function(response){
		  		$("#version").val(response);
	  		});

			$.post('/programs/GetSelectedRadio.php', {un:tUserName}, function(response) {
				tMyRadio=response;
				var pR=parseInt(tMyRadio)*2+4530;
				$("#rclPort").val(pR);
				var pR=parseInt(tMyRadio)-1+30001;
				$("#ccMPort").val(pR);
		        $.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
		        {
					$('#searchText').val(response);
			    });

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

			$(document).keydown(function(e){
			var t=e.key;
			e.multiple
			var w=e.which;
			if (w==191)
			{
				if (e.shiftKey){
					<?php require $dRoot . "/includes/shortcutsOther.php"; ?>
					$("#modalCO-body").html(tSh);			  				
					$("#modalCO-title").html("Shortcut Keys");
					  $("#myModalCancelOnly").modal({show:true});
					  return false;
				}else{
					var tS1=document.activeElement.tagName;
					if (tS1=='INPUT'){
						return true;
					}else{
						$("#searchText").focus();
						return false;
					}
				}
			};
			
			if (w == 27) { 
				document.getElementById('closeModal').click();
			}
				if (e.altKey){
					switch(w){
					case 65: // a
						showCalendar();
						e.preventDefault();
						break;
					case 69: // e
						showSettings();
						e.preventDefault();
						break;
					case 72: // h
						showHelp();
						e.preventDefault();
						break;
					case 75: //k
						showKeyer();
						e.preventDefault();
						break;
					case 76: //l
						showLog();
						e.preventDefault();
						break;
			 			
					case 83: // s
						showSpots();
						e.preventDefault();
						break;
					case 84: // t
						showTuner();
						e.preventDefault();
						break;
					case 87: // w
						showWeb();
						e.preventDefault();
						break;
					};
				};
			});

			$.getScript("/js/modalLoad.js");

			$(document).on('click', '#logoutButton', function() {
				openWindowWithPost("/login.php", {
				    status: "loggedout",
				    username: tUserName});
			});
			
			$(document).on('click', '#checkStatsButton', function() {
				$.post("/programs/GetInfo.php", {what: 'Stats'}, function(response){
					$("#modalA-body").html(response);			  				
					$("#modalA-title").html("RSS Stats");
				  	$("#myModalAlert").modal({show:true});
				});

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

			$(document).on('click', '#checkVersionButton', function() {
				if (tNoInternet==1){
					$("#modalA-body").html("<br>RSS cannot reach the Internet so can't check for a new version.<br><br>");			  				
					$("#modalA-title").html("No Internet");
				  	$("#myModalAlert").modal({show:true});
				}else{
					if (tTest==0){
	  					$.post('/programs/GetUpdateVersion.php', function(response){
							var vers=response;
							var tV=$("#version").val();
							if (vers>0){
					            if (vers>tV){
									$("#modalC-body").html("<br>RSS version is now "+tV+". A new RSS version, "+vers+", is available.<p><p>Do you wish to update now?<br>");
									$("#modalC-title").html("New RSS Version");
								  	$("#myModalCAlert").modal({show:true});
					            }else{
									$("#modalA-body").html("<br>You are using the latest version of RSS.<br><br>");			  				
									$("#modalA-title").html("RSS Version");
								  	$("#myModalAlert").modal({show:true});
								};
							};
						});
					}else{
						$("#modalC-body").html("<br>The latest RigPi Beta version will be downloaded and installed.<br>");
						$("#modalC-title").html("New RSS Version");
					  	$("#myModalCAlert").modal({show:true});
					}
				}
			});

	            $(document).on('click', '#modalAlertOK', function() {
				  	$("#myModalCAlert").modal('hide');
					$("#modalU-title").html("RSS Update");
					$("#modalU-body").html("");
				  	$("#myModalUpdate").modal({show:true});
				  	var status='';
					$.post("/my/getUpdate.php", {portion: "1", test: tTest}, function(response){
						status=response;
						$("#modalU-body").html(status);
						$.post("/my/getUpdate.php", {portion: "2", test: tTest}, function(response){
							status=status+'<p><p>'+response;
							$("#modalU-body").html(status);
							$.post("/my/getUpdate.php", {portion: "3", test: tTest}, function(response){
								status=status+'<p><p>'+response;
								$("#modalU-body").html(status);
								$.post("./my/getUpdate.php", {portion: "4", test: tTest}, function(response){
									status=status+'<p><p>'+response;
									$("#modalU-body").html(status);
								});
							});
						});
					});
				});
				
				$(document).on('click', '#closeUpdate', function() {
					$.get('/programs/GetMyRadio.php', 'f=PTTMode&r='+tMyRadio, function(response) {
			  			var tMyPTT=response;
		  				$.post('/programs/disconnectRadio.php', {radio: tMyRadio, user: tUserName, rotor: tMyRadio}, function(response) {
			                $.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: "OFF", table: "RadioInterface"});
			  				if (tMyPTT==2){
			  					$.post('/programs/doGPIOPTT.php', {PTTControl: "off"}); 			  				
			  				}
							openWindowWithPost("/login.php", {
							    status: "reboot",
							    username: tUserName});
						});
					});
				});
			$(document).on('click', '#shutDownButton', function() {
				$("#modalS-body").html("<br>OK to shut down RigPi?<br><br>Wait for the green activity light to stop blinking before removing power.");
				$("#modalS-title").html("Shut Down RigPi");
			  	$("#myShutDownAlert").modal({show:true});
			});
			
			$(document).on('click', '#rebootButton', function(){
				$("#modalR-body").html("<br>OK to reboot now?");
				$("#modalR-title").html("Reboot");
				$("#myRebootAlert").modal({show:true});
			});

			$(document).on('click', '#modalRebootOK', function() {
				$.get('/programs/GetMyRadio.php', 'f=PTTMode&r='+tMyRadio, function(response) {
		  			var tMyPTT=response;
	  				$.post('/programs/disconnectRadio.php', {radio: tMyRadio, user: tUserName, rotor: tMyRadio}, function(response) {
		                $.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: "OFF", table: "RadioInterface"});
		  				if (tMyPTT==2){
		  					$.post('/programs/doGPIOPTT.php', {PTTControl: "off"}); 			  				
		  				}
					openWindowWithPost("/login.php", {
					    status: "reboot",
					    username: tUserName});
						});
				});
			});

			$(document).on('click', '#modalShutDownOK', function() {
				openWindowWithPost("/login.php", {
				    status: "shutdown",
				    username: tUserName});
			});

			$.post("/programs/GetInfo.php", {what: 'Temp'}, function(response){
				$("#temp").val(response);
			});

		});

	  		$.getScript("/js/addPeriods.js");

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

		$(document).on('click', '#fccButton', function() {
			if (tNoInternet==1){
				$("#modalA-body").html("<br>RSS cannot reach the Internet so can't update the FCC database.<br><br>");			  				
				$("#modalA-title").html("No Internet");
			  	$("#myModalAlert").modal({show:true});
			}else{
				$("#modalF-body").html("<br>OK to download and install the weekly FCC Database?<br><br>This process will take over 20 minutes.<br><br>");
				$("#modalF-title").html("FCC Database");
			  	$("#myFCCAlert").modal({show:true});
			};
		});

		$(document).on('click', '#modalFCCOK', function() {
			$("#myFCCAlert").modal('hide');
			$el=$('#spinner');
			$el.addClass('fa-spin');
			$.post('/FCC/doFCC.php', function(response){
				$("#modalA-body").html(response);			  				
				$("#modalA-title").html("FCC Database");
			  	$("#myModalAlert").modal({show:true});
				$el.removeClass('fa-spin');
			})
		});

	</script>
</head>

<body class="body-black">
	<?php require $dRoot . "/includes/header.php"; ?>
	<div class="container-fluid">
		<div class="row" style="margin-bottom:10px;">
			<div class="col-12 col-sm-12 text-center">
				<span class="label label-success text-white" style="cursor: default; margin-top:10px;"><?php echo "System Settings"; ?></span>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-4 text-center text-spacer">
				<button class="btn btn-outline-warning btn-sm my-2 my-sm-0 text-white" onfocus="this.select();" data-id="3" id="rebootButton"  title="Click to Reboot RigPi" type="button">
					<i class="fas fa-redo"></i>
					Reboot RigPi
				</button>
			</div>
			<div class="col-md-4 text-center text-spacer">
				<button class="btn btn-outline-danger btn-sm my-2 my-sm-0 text-white" onfocus="this.select();" data-id="4" id="shutDownButton"  title="Click to Shut Down RigPi" type="button">
					<i class="fas fa-power-off"></i>
					Shut Down RigPi
				</button>
			</div>
			<div class="col-md-4 text-center text-spacer">
				<button class="btn btn-outline-success btn-sm my-2 my-sm-0 text-white" onfocus="this.select();" data-id="5" id="fccButton"  title="Click to update FCC database" type="button">
					Update FCC Database
					<div class="inline" id="spinner"><i class="fas fa-sync" style="color:white; font-size:24px"></i></div>
				</button>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text  readonly system-group-addon">LAN IP 1</span>
					</div>
					<input type="text" class="form-control disable-text"  readonly title="LAN IP" id="lan1" aria-lable="lan1" aria-describedby="lanIP-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text  readonly system-group-addon">LAN IP 2</span>
					</div>
					<input type="text" class="form-control disable-text"  readonly title="Wi-Fi IP" id="lan2" aria-lable="lan2" aria-describedby="lan2-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text  readonly system-group-addon">WAN IP</span>
					</div>
					<input type="text" class="form-control disable-text" readonly title="Internet IP" id="wanIP" aria-lable="wan1" aria-describedby="wan1-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text noselect readonly system-group-addon">RPi Temp</span>
					</div>
					<input type="text" class="form-control disable-text"  readonly title="Temp" id="temp" aria-lable="temp" aria-describedby="temp-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text  readonly system-group-addon">Rigctl Port</span>
					</div>
					<input type="text" class="form-control disable-text"  readonly title="Temp" onfocus="this.select();" data-id="6" id="rclPort" aria-lable="temp" aria-describedby="temp-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text  readonly system-group-addon">CCM Port</span>
					</div>
					<input type="text" class="form-control disable-text"  readonly title="Temp" id="ccMPort" aria-lable="temp" aria-describedby="temp-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text  readonly system-group-addon">RSS Vers</span>
					</div>
					<input type="text" class="form-control disable-text"  readonly title="Version" id="version" aria-lable="version" aria-describedby="version-addon">
				</div>
			</div>
			<div class="col-md-4 text-center text-spacer">
				<button class="btn btn-outline-warning btn-sm my-2 my-sm-0 text-white" onfocus="this.select();" data-id="7" id="checkVersionButton"  title="Check current version" type="button">
					<i class="fas fa-check"></i>
					Update RSS Files
				</button>
			</div>
			<div class="col-md-4 text-spacer text-center">
				<button class="btn btn-outline-success btn-sm my-2 my-sm-0 text-white" onfocus="this.select();" data-id="8" id="checkStatsButton"  title="Get RSS Stats" type="button">
					<i class="fas fa-list fa-fw"></i>
					Get RSS Stats
				</button>
			</div>
		</div>
	</div>
    <?php require $dRoot . "/includes/footer.php"; ?>
    <?php require $dRoot . "/includes/modal.txt"; ?>
    <?php require $dRoot . "/includes/modalAlert.txt"; ?>
    <?php require $dRoot . "/includes/modalCancelAlert.txt"; ?>
   <?php require $dRoot . "/includes/modalCancelShutdown.txt"; ?>
   <?php require $dRoot . "/includes/modalCancelFCC.txt"; ?>
	<?php require $dRoot . "/includes/modalUpdate.txt"; ?>
 	<?php require $dRoot . "/includes/modalCancelReboot.txt"; ?>
<?php require $dRoot . "/includes/modalCancelOnly.txt"; ?>
 	<script src="./Bootstrap/popper.min.js"</script>
	<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
	<script src="./Bootstrap/jquery-ui.js"></script>
	<script src="./Bootstrap/bootstrap.min.js"></script>
	<script src="js/nav-active.js"></script>
</body>
</html>
