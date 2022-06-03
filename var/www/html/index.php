<!--
		This program is free software: you can redistribute it and/or modify
		it under the terms of the GNU General Public License as published by
		the Free Software Foundation, either version 3 of the License, or
		(at your option) any later version.
	
		This program is distributed in the hope that it will be useful,
		but WITHOUT ANY WARRANTY; without even the implied warranty of
		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
		GNU General Public License for more details.
	
		You should have received a copy of the GNU General Public License
		along with this program.  If not, see <https://www.gnu.org/licenses/>.
	-->
<?php
session_start();
if (!isset($GLOBALS["htmlPath"])) {
  $GLOBALS["htmlPath"] = $_SERVER["DOCUMENT_ROOT"];
}
if (!empty($_SESSION["firstuse"])) {
  $firstUse = $_SESSION["firstUse"];
} else {
  $firstUse = 1;
}
//$firstUse = 1;
$_SESSION["firstUse"] = 0;
if ($firstUse == 1) {
  $tColsExec = "sudo /usr/share/rigpi/col.sh";
  $cols = exec($tColsExec);
}
$dRoot = $GLOBALS["htmlPath"];
$wanIP = "http://rigpi.dyndns.org:8081";
require $dRoot . "/programs/GetMyRadioFunc.php";
if (!empty($_GET["c"]) && !empty($_GET["x"])) {
  $tCall = strtoupper($_GET["c"]);
  $tUserName = $_GET["x"];
} else {
  $tCall = "";
  $tUserName = "";
}
require_once $dRoot . "/classes/Membership.php";
$membership = new Membership();
if (!$membership->confirm_Member($tUserName)) {
  exit();
}
require_once $dRoot . "/programs/GetUserFieldFunc.php";
$theme = getUserField($tUserName, "Theme");
if ($theme == 0) {
  ////$tThemePanel = "panelNUMOrange.json";
  $tThemePanel = "panelNUMOrange1000.json";
  $tThemeMeter = "smeterOrange.json";
} elseif ($theme == 1) {
  $tThemePanel = "panelNUMOrange1000.json";
  $tThemeMeter = "smeterNight.json";
} elseif ($theme == 2) {
  $tThemePanel = "panelNUMOrange1000.json";
  $tThemeMeter = "smeterLCD.json";
} elseif ($theme == 3) {
  $tThemePanel = "panelNUMOrange1000.json";
  $tThemeMeter = "smeterHigh.json";
} elseif ($theme == 4) {
  $tThemePanel = "panelNUMOrange1000.json";
  $tThemeMeter = "smeterLCD.json";
}
require_once $dRoot . "/classes/MysqliDb.php";
require $dRoot . "/programs/sqldata.php";
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->where("Username", $tUserName);
$row = $db->getOne("Users");
$level = "10";
if ($row) {
  $level = $row["Access_Level"];
}
$db->where("IsAlive", "1");
$rows = $db->get("RadioInterface");
$online = $db->count;
?>
	
	<!--This is the Tuner window -->
	
	<!DOCTYPE html>
	<html lang="en">
		<head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
			<title><?php echo $tCall; ?> RigPi Tuner</title>
			<meta name="description" content="RigPi Tuner">
			<meta name="author" content="Howard Nurse, W6HN">
			<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	
			<!-- Bootstrap CSS -->
			<link rel="stylesheet" href="/Bootstrap/bootstrap.min.css">
			 <script src="/Bootstrap/jquery.min.js" ></script>
	<!--
		<script src="/js/jogDial.min.js"></script>
		<script src="/js/gauge.min.js"></script>
	-->
			<link rel="shortcut icon" href="./favicon.ico">
			<link rel="apple-touch-icon" href="./favicon.ico">
			<?php require $dRoot . "/includes/styles.php"; ?>
			<link href="./awe/css/all.css" rel="stylesheet">
			<link href="./awe/css/fontawesome.css" rel="stylesheet">
			<link href="./awe/css/solid.css" rel="stylesheet">	
			<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
	
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<script type="text/javascript">
				var resetTimer, spaceUp, oldState;
				var pknob;
				var ppanel;
				var pmeter;
				var lowerText;
				var pled;
				var swiper;
				var knobOld;
				var plus;
				var ptt;
				var keypad;
				var tBusy;
				var spacePTT=0;
				var tPTT, tPTTIsOn=0;
				var tKnobPTT=0;
				var tSwipeOld=0;
				var xmit=false;
				var trXmit=false;
				var tuningIncrement=100;
				var windowLoaded=false;
				var tUpdate;
				var mp1;
				var mp100;
				var waitRefresh=0;
				var tMyRadio='1';
				var tMyRadioReal='1';
				var tCWPort="/dev/ttyS0";
				var tMyRotorPort="";
				var tUserName="<?php echo $tUserName; ?>";
				var tAccessLevel="<?php echo $level; ?>";
				var tFirstUse="<?php echo $firstUse; ?>";
				var tMyCall="<?php echo $tCall; ?>";
				var tOnline="<?php echo $online; ?>";
				var tUser='';
				var tSplitOn=0;
				var tTuneFromTap=0;
				var classTimer;
				var trOn=0; //T/R macro
				var tLine1="";
				var tLine2="";
				var stLine1="";
				var stLine2="";
				var curDigit=0;
				var curSubDigit=0;
				var tMyKeyer='';
				var tMain=0;
				var tSub=0;
				var tMode=0;
				var tNoRadio=true;
				var tOverPanel=false;
				var tMouseDelta=0;
				var tIgnoreRepeating=false;
				var tMyPTT=1;
				var tAliveCount=0;
				var tMeterCal=1;
				var ld='ld2';
				var lu=2;
				var slider1='';
				var band1='';
				var band2='';
				var alreadyDone=0;
				var tRadioMem="";
				var tInternet=1;
				var tNoReboot=0;
				var speedPot=0;
				var tSplitDisabled=0;
				var notGotPerfectWidgets=0;
				var tMyKeyerFunction="0";
				var tMyKeyerPort="0";
				var tMyKeyerIP="0";
				var ptt;
				var ptt1;
				var lock;
				var lockOn, tTuneOn=0;
				var crx;
				var tTrx=0;
				var lastSpaceTimer=0;
				var tRadioName="";
				var tRadioModel="";
				var tRadioPort=4532;
				var si;
				var tDisconnected=0;
				var tMyRadioCW;
				var tMyCWPort;
				var tButtonWait=0;
				var tMyRotorRadio; //ve9gj
				var led1,led2,led3,led4,led5,led6,led7,led8;
				var mBank=1;
				var showVideo=2;
				var jsonPanel='';
				var jsonSMeter='';
				var wanIP;
				var tBand=20;
				var tBandMHz="14MHz";
				var tKnobLock=0;
				var mtrLabel="S-Meter";
				var outputAF, sliderAF, outputPwrOut, sliderPwrOut, sliderMic, outputMic, outputRF, sliderRF, tVal;
				var sliderAFGainOride, sliderRFGainOride, sliderPwrOutOride, sliderMicLvlOride;
				var sliderAFRef, sliderRFRef, sliderPwrOutRef, sliderMicRef, sliderHandle;
				var latchBtn1=[],latchBtn2=[],latchBtn3=[],latchBtn4=[],latchBtn=[], bntLatchColor;
				var bEnable=[],bModeEnable=[],aMacros=[], bk, tMainSelect, tSpeedOriginal;
				$(document).ready(function()
				{
					btnLatchColor="btn-warning";
					updateFreqDisp()
					outputAF = document.getElementById("myAFVal");
					sliderAF = document.getElementById("sliderAF");
					outputRF = document.getElementById("myRFVal");
					sliderRF = document.getElementById("sliderRF");
					outputPwrOut = document.getElementById("myOutputPwrVal");
					sliderPwrOut = document.getElementById("sliderPwrOut");
					sliderHandle="";
					outputMic = document.getElementById("myMicVal");
					sliderMic = document.getElementById("sliderMic");
					 
					if (tAccessLevel==1 && tFirstUse>0){
							$.post('/programs/testInternet.php',function(response){
								if (response !=0){
									tInternet=0;
									tNoReboot=1;
									$("#modalA-title").html("No Internet");
									$("#modalA-body").html("<br>RSS does not find an Internet connection. <p><p>"+
										"Certain functions such as Spots, QRZ Call Lookup, Version updating, and "+
										"Maps will not function without a connection. (Try refreshing the Tuner page.)<p>");
									  $("#myModalAlert").modal({show:true});
								}else{
									$.post('/programs/version.php', function(response){
									  var tV=response;
									  tNoReboot=0;
									  $.post('/programs/GetUpdateVersionBeta.php', function(response){
										var vers=response;
										if (vers!=0){
											if (vers>tV){
												$("#modalC-body").html("<br>RSS version is now "+tV+". A new RSS version, "+vers+", is available.<p><p>Do you wish to update now?<br>");
												$("#modalC-title").html("New RSS Version");
												  $("#myModalCAlert").modal({show:true});
											};
										};
									});
								  });
							};
						}); 
					}
	
					$(document).on('click', '#modalAlertOK', function() {
						  $("#myModalCAlert").modal('hide');
						$("#modalU-title").html("RSS Update");
						$("#modalU-body").html("");
						  $("#myModalUpdate").modal({show:true});
						  var status='';
						$.post("/my/getUpdate.php", {portion: "1", test: "0"}, function(response){
							status=response;
							$("#modalU-body").html(status);
							$.post("/my/getUpdate.php", {portion: "2", test: "0"}, function(response){
								status=status+'<p><p>'+response;
								$("#modalU-body").html(status);
								$.post("/my/getUpdate.php", {portion: "3", test: "0"}, function(response){
									status=status+'<p><p>'+response;
									$("#modalU-body").html(status);
									$.post("./my/getUpdate.php", {portion: "4", test: "0"}, function(response){
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
							  $.post('/programs/disconnectRadio.php', {radio: tMyRadio, user: tUserName, rotor: tMyRotorRadio}, function(response) {
								$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: "OFF", table: "RadioInterface"});
								  if (tMyPTT==3){
									  $.post('/programs/doGPIOPTT.php', {PTTControl: "off"}); 			  				
								  }
								openWindowWithPost("/login.php", {
									status: "reboot",
									username: tUserName});
							});
						});
					});
	
					function updateBank(which){
						var mB='#myBank'+mBank;
						$(mB).removeClass(btnLatchColor);
						$(mB).addClass('btn-color');
						mBank=which;
						loadMacroBank(mBank);
						var mB='#myBank'+mBank;
						$(mB).removeClass('btn-color');
						$(mB).addClass("btn-info");
						$.post("/programs/SetSettings.php", {field: "MacroBankTuner", radio: tMyRadio, data: mBank, table: "RadioInterface"});
					};	
	
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
					
	
					$.post('./programs/GetSelectedRadio.php', {un:tUserName}, function(response) 
					{
						$.get('/programs/GetMyRadio.php', 'f=Port&r='+response, function(response1) {
							tMyRadio=response;
							tMyRadioReal=response;
							  var tMyRadioPort=response1;
							  if (tMyRadioPort>4530 && tMyRadioPort<5000){
								  tMyRadioReal=1+(tMyRadioPort-4532)/2;
							  }
						$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'LatchBtn1', table: 'RadioInterface'}, function(response){
							if (response==""){
								response="?,".repeat(32);
							}
							latchBtn1=response.split(",");
						});
						$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'LatchBtn2', table: 'RadioInterface'}, function(response){
							if (response==""){
								response="?,".repeat(32);
							}
							latchBtn2=response.split(",");
						});
						$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'LatchBtn3', table: 'RadioInterface'}, function(response){
							if (response==""){
								response="?,".repeat(32);
							}
							latchBtn3=response.split(",");
						});
						$.post('/programs/GetSetting.php',{radio: tMyRadio, field: "LatchBtn4", table: "RadioInterface"}, function(response){
							if (response==""){
								response="?,".repeat(32);
							}
							latchBtn4=response.split(",");
						});
	
						$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'AFGainOride', table: 'RadioInterface'}, function(response){
							sliderAFGainOride=response;
							$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'AFGain', table: 'RadioInterface'}, function(response){
								$( function() {
									$("#sliderAF").slider({
										min: 0,
										max: sliderAFGainOride,
										range: 'min'
									});
									outputAF.innerHTML = response;
									$("#sliderAF").slider('value',response);
							});
						
							$("#sliderAF").on("slidechange",function(event,ui){
								tVal=$("#sliderAF").slider('value');
								if (tVal<0) tVal=0;
								if (tVal!=sliderAFRef){
									waitRefresh=8;
									sliderAFRef=tVal;
									sliderHandle=ui.handle;
									var tV=tVal;
									if (tV>0 && tV<100) tV=tV-1;
									outputAF.innerHTML =tV;
									$.post("/programs/SetSettings.php", {field: "AFGain", radio: tMyRadio, data: tVal, table: "RadioInterface"}, function(response){
									});
								};
							});
							
							$("#sliderAF").on("slide",function(event,ui){
								waitRefresh=8;
								tVal=$("#sliderAF").slider('value');
								var tV=tVal;
								if (tV>0 && tV<100) tV=tV-1;
								outputAF.innerHTML =tV;
								sliderHandle=ui.handle;
								$.post("/programs/SetSettings.php", {field: "AFGain", radio: tMyRadio, data: tVal, table: "RadioInterface"}, function(response){
								});
							});
						});
						
						$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'RFGainOride', table: 'RadioInterface'}, function(response){
							sliderRFGainOride=response;
							$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'RFGain', table: 'RadioInterface'}, function(response){
								$( function() {
									$("#sliderRF").slider({
										min: 0,
										max: sliderRFGainOride,
										range: 'min'
								});
								outputRF.innerHTML = response;
								$("#sliderRF").slider('value',response);
								});
							});
							
							$("#sliderRF").on("slidechange",function(event,ui){
								tVal=$("#sliderRF").slider('value');
							//						if (tVal<100) tVal=tVal-1;
								if (tVal<0) tVal=0;
								if (tVal!=sliderRFRef){
									waitRefresh=5;
									sliderRFRef=tVal;
									sliderHandle=ui.handle;
									var tV=tVal;
									if (tV>0 && tV<100) tV=tV-1;
									outputRF.innerHTML =tV;
									$.post("/programs/SetSettings.php", {field: "RFGain", radio: tMyRadio, data: tVal, table: "RadioInterface"}, function(response){
									});
							//radial.value = tV;
								};
							});
							
							$("#sliderRF").on("slide",function(event,ui){
								waitRefresh=3;
								tVal=$("#sliderRF").slider('value');
								if (tVal<0) tVal=0;
								sliderHandle=ui.handle;
								var tV=tVal;
								if (tV>0 && tV<100) tV=tV-1;
								outputRF.innerHTML =tV;
								$.post("/programs/SetSettings.php", {field: "RFGain", radio: tMyRadio, data: tVal, table: "RadioInterface"}, function(response){
								});
							});
						});
						
						$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'PwrOutOride', table: 'RadioInterface'}, function(response){
							sliderPwrOutOride=response;
						
							$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'PwrOut', table: 'RadioInterface'}, function(response){
								$( function() {
									$("#sliderPwrOut").slider({
										min: 0,
										max: sliderPwrOutOride,
										range: 'min'
									});
									outputPwrOut.innerHTML = response;
									$("#sliderPwrOut").slider('value',response);
								});
							});
								
							$("#sliderPwrOut").on("slidechange",function(event,ui){
								tVal=$("#sliderPwrOut").slider('value');
								if (tVal<0) tVal=0;
								if (tVal!=sliderPwrOutRef){
									waitRefresh=5;
									sliderPwrOutRef=tVal;
									sliderHandle=ui.handle;
									var tV=tVal;
									if (tV>0 && tV<100) tV=tV-1;
									outputPwrOut.innerHTML =tV;
									$.post("/programs/SetSettings.php", {field: "PwrOut", radio: tMyRadio, data: tVal, table: "RadioInterface"}, function(response){
									});
								};
							});
							
							$("#sliderPwrOut").on("slide",function(event,ui){
								waitRefresh=3;
								tVal=$("#sliderPwrOut").slider('value');
								var tV=tVal;
								if (tV>0 && tV<100) tV=tV-1;
								outputPwrOut.innerHTML =tV;
								sliderHandle=ui.handle;
								$.post("/programs/SetSettings.php", {field: "PwrOut", radio: tMyRadio, data: tVal, table: "RadioInterface"}, function(response){
								});
							});
						});
						
						$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MicLvlOride', table: 'RadioInterface'}, function(response){
							sliderMicLvlOride=response;
							$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MicLvl', table: 'RadioInterface'}, function(response){
								$( function() {
									$("#sliderMic").slider({
										min: 0,
										max: sliderMicLvlOride,
										range: 'min'
									});
									outputMic.innerHTML = response;
									$("#sliderMic").slider('value',response);
								});
							});
							
							$("#sliderMic").on("slidechange",function(event,ui){
								tVal=$("#sliderMic").slider('value');
								if (tVal<0) tVal=0;
								if (tVal!=sliderMicRef){
									sliderMicRef=tVal;
									waitRefresh=8;
									sliderHandle=ui.handle;
									var tV=tVal;
									if (tV>0 && tV<100) tV=tV-1;
									if (tV<0) tV=0;
									outputMic.innerHTML =tV;
									$.post("/programs/SetSettings.php", {field: "MicLvl", radio: tMyRadio, data: tVal, table: "RadioInterface"}, function(response){
									});
								};
							});
							
							$("#sliderMic").on("slide",function(event,ui){
								waitRefresh=8;
								tVal=$("#sliderMic").slider('value');
								if (tVal<0) tVal=0;
								sliderHandle=ui.handle;
								var tV=tVal;
								if (tV>0 && tV<100) tV=tV-1;
								outputMic.innerHTML =tV;
								$.post("/programs/SetSettings.php", {field: "MicLvl", radio: tMyRadio, data: tVal, table: "RadioInterface"}, function(response){
								});
							});
						})
					});					
					var mtrCalField="";
						$.post('/programs/GetSetting.php',{radio: tMyRadioReal, field: 'TransmitLevel', table: 'MySettings'}, function(response){
							if(response=="l RFPOWER_METER")
							{
								mtrLabel='Output Power Meter';
								mtrCalField="PowerMeterCal";
							}else if (response=="l SWR")
							{
								mtrLabel='SWR';
								mtrCalField="SWRCal";
							}else if (response=="l RFPOWER")
							{
								mtrLabel='RF Power Default';
								mtrCalField="PowerDefaultCal";
							}else if (response=="l MICGAIN")
							{
								mtrLabel="Mic Gain";
								mtrCalField="MicGainCal";
							}else if (response=="l ALC")
							{
								mtrLabel="ALC";
								mtrCalField="ALCCal";
							}else if (response=="l VD_METER")
							{
								mtrLabel="Voltage";
								mtrCalField="VoltageCal";
							}else if (response=="l ID_METER")
							{
								mtrLabel="Current";
								mtrCalField="CurrentCal";
							}else if (response=="l METER")
							{
								mtrLabel="Meter";
								mtrCalField="MeterCal";
							}
							$.post('./programs/GetSetting.php',{radio: tMyRadio, field: mtrCalField, table: 'RadioInterface'}, function(response)
							{
								tMeterCal=response;
							});
						});
						$.post('./programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKPot'}, function(response) {
							speedPot=response;
							$.post('./programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKSpeed'}, function(response) {
								var tSpeed=response;
	//							tSpeedOriginal=tSpeed
								$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tSpeed, table: "Keyer"});
								$.post("/programs/SetSettings.php", {field: "CWChangeCk", radio: tMyRadio, data: 1, table: "RadioInterface"});
							});
						});
		
							$.post("/programs/GetInfo.php", {what: 'IPAdr'}, function(response){
								  var d = new Date();
								  var n = d.getTime();
		
								var aData=response.split('+');
								wanIP="http://"+aData[2]+":8081"+"?"+n;
								lanIP="http://"+aData[0]+":8081"+"?"+n;
								$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'ShowVideo', table: 'MySettings'}, function(response)
								{
									showVideo=response;
									$.getJSON('https://api.ipify.org?format=json',function(response){
											var wIP=response.ip;
										if (showVideo>1){
											if ($(window).width()<1000){  //phones
												if (wIP==aData[2]){
													$('#i1').attr('src', lanIP);
													$('#i2').attr('src', "");
													$('#i3').attr('src', "");
												}else{
													$('#i1').attr('src',wanIP );
													$('#i2').attr('src', "");
													$('#i3').attr('src', "");
												}
											}else{ //normal screens
												if (wIP==aData[2]){
													if (showVideo==2){
														$('#i3').attr('src', "");
														$('#i2').attr('src', lanIP);
														$('#i1').attr('src', "");
													}else if (showVideo==3) {
														$('#i3').attr('src', "");
														$('#i2').attr('src', "");
														$('#i1').attr('src', lanIP);
													}else{
														$('#i1').attr('src', "");
														$('#i2').attr('src', "");
														$('#i3').attr('src', lanIP);
													}
												}else{
													if (showVideo==2){
														$('#i3').attr('src', "");
														$('#i2').attr('src', wanIP );
														$('#i1').attr('src', "");
													}else if (showVideo==3) {
														$('#i3').attr('src', "");
														$('#i2').attr('src', "");
														$('#i1').attr('src', wanIP);
													}else{
														$('#i1').attr('src', "");
														$('#i2').attr('src', "");
														$('#i3').attr('src', wanIP);
													}
												};
											};
											
										}else{
											$('#i1').attr('src', '');
											$('#i2').attr('src', '');
											$('#i3').attr('src', '');
										};
									});
								});
							});
			
			//Main buttons
							if (notGotPerfectWidgets==0){
	//							bkColor = ppanel.getByName("Background");
	//							bkColor.setVisible(false);
								up1 = ppanel.getByName("up1");
								up1.addOnClickHandler(up1Clicked);
								dn1 = ppanel.getByName("dn1");
								dn1.addOnClickHandler(dn1Clicked);
								up10 = ppanel.getByName("up10");
								up10.addOnClickHandler(up10Clicked);
								dn10 = ppanel.getByName("dn10");
								dn10.addOnClickHandler(dn10Clicked);
								up100 = ppanel.getByName("up100");
								up100.addOnClickHandler(up100Clicked);
								up1000 = ppanel.getByName("up1000");
								up1000.addOnClickHandler(up1000Clicked);
								dn100 = ppanel.getByName("dn100");
								dn100.addOnClickHandler(dn100Clicked);
				
								up01 = ppanel.getByName("up.1");
								up01.addOnClickHandler(up01Clicked);
								dn01 = ppanel.getByName("dn.1");
								dn01.addOnClickHandler(dn01Clicked);
								up001 = ppanel.getByName("up.01");
								up001.addOnClickHandler(up001Clicked);
								dn001 = ppanel.getByName("dn.01");
								dn001.addOnClickHandler(dn001Clicked);
								up0001 = ppanel.getByName("up.001");
								up0001.addOnClickHandler(up0001Clicked);
								dn0001 = ppanel.getByName("dn.001");
								dn0001.addOnClickHandler(dn0001Clicked);
				
								upx1 = ppanel.getByName("upx1");
								upx1.addOnClickHandler(upx1Clicked);
								dnx1 = ppanel.getByName("dnx1");
								dnx1.addOnClickHandler(dnx1Clicked);
								upx01 = ppanel.getByName("upx01");
								upx01.addOnClickHandler(upx01Clicked);
								dnx01 = ppanel.getByName("dnx01");
								dnx01.addOnClickHandler(dnx01Clicked);
								upx001 = ppanel.getByName("upx001");
								upx001.addOnClickHandler(upx001Clicked);
								dnx001 = ppanel.getByName("dnx001");
								dnx001.addOnClickHandler(dnx001Clicked);
				//Sub buttons
								sup1 = ppanel.getByName("sup1");
								sup1.addOnClickHandler(sup1Clicked);
								sdn1 = ppanel.getByName("sdn1");
								sdn1.addOnClickHandler(sdn1Clicked);
								sup10 = ppanel.getByName("sup10");
								sup10.addOnClickHandler(sup10Clicked);
								sdn10 = ppanel.getByName("sdn10");
								sdn10.addOnClickHandler(sdn10Clicked);
								sup100 = ppanel.getByName("sup100");
								sup100.addOnClickHandler(sup100Clicked);
								sdn100 = ppanel.getByName("sdn100");
								sdn100.addOnClickHandler(sdn100Clicked);
								sdn1000 = ppanel.getByName("sdn1000");
	//							sdn1000.addOnClickHandler(sdn1000Clicked);
								sup1000 = ppanel.getByName("sup1000");
	//							sup1000.addOnClickHandler(sup1000Clicked);
				
								sup01 = ppanel.getByName("sup.1");
								sup01.addOnClickHandler(sup01Clicked);
								sdn01 = ppanel.getByName("sdn.1");
								sdn01.addOnClickHandler(sdn01Clicked);
								sup001 = ppanel.getByName("sup.01");
								sup001.addOnClickHandler(sup001Clicked);
								sdn001 = ppanel.getByName("sdn.01");
								sdn001.addOnClickHandler(sdn001Clicked);
								sup0001 = ppanel.getByName("sup.001");
								sup0001.addOnClickHandler(sup0001Clicked);
								sdn0001 = ppanel.getByName("sdn.001");
								sdn0001.addOnClickHandler(sdn0001Clicked);
				
								supx1 = ppanel.getByName("supx1");
								supx1.addOnClickHandler(supx1Clicked);
								sdnx1 = ppanel.getByName("sdnx1");
								sdnx1.addOnClickHandler(sdnx1Clicked);
								supx01 = ppanel.getByName("supx01");
								supx01.addOnClickHandler(supx01Clicked);
								sdnx01 = ppanel.getByName("sdnx01");
								sdnx01.addOnClickHandler(sdnx01Clicked);
								supx001 = ppanel.getByName("supx001");
								supx001.addOnClickHandler(supx001Clicked);
								sdnx001 = ppanel.getByName("sdnx001");
								sdnx001.addOnClickHandler(sdnx001Clicked);
				//Main lines
								ld1 = ppanel.getByName("lhd1");
								lu1 = ppanel.getByName("lhu1");
								ld2 = ppanel.getByName("lhd2");
								lu2 = ppanel.getByName("lhu2");
								ld3 = ppanel.getByName("lhd3");
								lu3 = ppanel.getByName("lhu3");
								ld4 = ppanel.getByName("lkd1");
								lu4 = ppanel.getByName("lku1");
								ld5 = ppanel.getByName("lkd2");
								lu5 = ppanel.getByName("lku2");
								ld6 = ppanel.getByName("lkd3");
								lu6 = ppanel.getByName("lku3");
								ld7 = ppanel.getByName("lmd1");
								lu7 = ppanel.getByName("lmu1");
								ld8 = ppanel.getByName("lmd2");
								lu8 = ppanel.getByName("lmu2");
								ld9 = ppanel.getByName("lmd3");
								lu9 = ppanel.getByName("lmu3");
								lu10 = ppanel.getByName("lmu4");
								ld10 = ppanel.getByName("lmd4");
				//Sub lines
								sld1 = ppanel.getByName("slhd1");
								slu1 = ppanel.getByName("slhu1");
								sld2 = ppanel.getByName("slhd2");
								slu2 = ppanel.getByName("slhu2");
								sld3 = ppanel.getByName("slhd3");
								slu3 = ppanel.getByName("slhu3");
								sld4 = ppanel.getByName("slkd1");
								slu4 = ppanel.getByName("slku1");
								sld5 = ppanel.getByName("slkd2");
								slu5 = ppanel.getByName("slku2");
								sld6 = ppanel.getByName("slkd3");
								slu6 = ppanel.getByName("slku3");
								sld7 = ppanel.getByName("slmd1");
								slu7 = ppanel.getByName("slmu1");
								sld8 = ppanel.getByName("slmd2");
								slu8 = ppanel.getByName("slmu2");
								sld9 = ppanel.getByName("slmd3");
								slu9 = ppanel.getByName("slmu3");
								sld10 = ppanel.getByName("slmd4");
								slu10 = ppanel.getByName("slmu4");
								
								tLine1=ld4;
								tLine2=lu4;
								stLine1=sld4;
								stLine2=slu4;
								$.post('/programs/GetSetting.php',{radio: tMyRadioReal, field: 'MainSelect', table: 'MySettings'}, function(response){
									tMainSelect=response;
									var tSel='0';
									var tSelM='1' + tSel.repeat(tMainSelect);
									doDigit(tSelM, 1);
								});
								doSubDigit(1000, 1);
								plus = pknob.getByName("PlusButton");
								plus.addOnClickHandler(plusClicked);
								lock = pknob.getByName("Lock");
								lockOn = pknob.getByName("LockOn");
								lock.addOnClickHandler(function (sender, e)
								{
									if (tKnobLock==1){
										tKnobLock=0;
										lockOn.setVisible(false);
										lock.setVisible(true);
									}else{
										tKnobLock=1;
										lockOn.setVisible(true);
										lock.setVisible(false);
									};
								});
								minus = pknob.getByName("MinusButton");
								minus.addOnClickHandler(minusClicked);
								ptt = pknob.getByName("PTT");
								ptt1 = pknob.getByName("PTTOn");
								cRX=ppanel.getByName("TR");
								ptt.addOnClickHandler(function (sender, e) 
								{
	//								var which1=ptt.getPressed();
	//ptt.setPressed(false);
	//var which2=ptt.getPressed();
									var rec = document.getElementById('dKnob').getBoundingClientRect();
									var position=rec.top + window.scrollY - window.pageYOffset;
	//if (which1==true && tDisconnected==0 && position>50)  //last && is to prevent PTT whene under hamburger menu on phones
	if (tDisconnected==0 && position>50)  //last && is to prevent PTT whene under hamburger menu on phones
									{
										if (xmit==true){
											setPTT(0,0);
										}else{
											setPTT(1,0);
											tKnobPTT=1;
										};
									};
								});
								
								
			
								$.post("/includes/vfoButtons.php", function(response){
									var bVFO=response;
									$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'ShowVideo', table: 'MySettings'}, function(response)
									{
										showVideo=response;
				
									   // $.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'showVideo', table: 'MySettings'}, function(response)
									   // {
											//showVideo=1;//response;
										var wWidth=$(window).width();
										if ($(window).width()<835){  //if small screen, show bar s-meter and keypad button
											var st=$(".status");
											  st.addClass('d-none');
											$("#bottomVFO").html(bVFO);
											gNum=ppanel.getByName("GroupNUM");
											gNum.setVisible(true);
											gDig=ppanel.getByName("GroupDigits");
											gDig.setVisible(false);
											gDig.setActive(false);
											nPos=ppanel.getByName("NUMrect");
											nPos.setActive(true);
											nPos=ppanel.getByName("Guide1");
											nPos.setActive(false);
											nPos=ppanel.getByName("Label1");
											nPos.setActive(true);
											nPos=ppanel.getByName("Slider1");
											nPos.setActive(false);
											nPos=ppanel.getByName("BarBorder");
											nPos.setActive(false);
											nPos=ppanel.getByName("LinearLevel1");
											nPos.setActive(false);
											if (showVideo==1 || showVideo==3 || showVideo==4){
												cBarVal=pmeter.getByName("Slider1");
											}
											keypad=ppanel.getByName("NUMBut")
											keypad.addOnClickHandler(keypadClicked);
											$("#colPan").removeClass("col-sm-4");
											$("#colPan").addClass("col-sm-12");
											$("#colKnob").removeClass("col-sm-4");
											$("#colKnob").addClass("col-sm-12");
											$("#colVid").removeClass("col-sm-4");
											$("#colVid").addClass("col-sm-12");
											if (showVideo==1){ //no video
												$("#videoPanel").addClass('d-none');
												$("#videoMeter").addClass('d-none');
												$("#videoKnob").addClass('d-none');
												$("#dMeter").addClass('d-none');
												$("#dPanel").removeClass('d-none');
												$("#dKnob").removeClass('d-none');
												$(".mycall").addClass('d-none');
												$("#lowerText").removeClass('d-none');
											}else if (showVideo==2){ //meter
												$("#videoPanel").removeClass('d-none');
												$("#videoMeter").addClass('d-none');
												$("#videoKnob").addClass('d-none');
												$("#dPanel").removeClass('d-none');
												$("#dMeter").addClass('d-none');
												$("#dKnob").removeClass('d-none');
												$("#lowerText").removeClass('d-none');
												$(".mycall").addClass('d-none');
											}else if (showVideo==3){ //panel 
												$("#videoPanel").removeClass('d-none');
												$("#videoMeter").addClass('d-none');
												$("#videoKnob").addClass('d-none');
												$("#dPanel").addClass('d-none');
												$("#dMeter").addClass('d-none');
												$("#dKnob").removeClass('d-none');
												$("#lowerText").removeClass('d-none');
												$(".mycall").addClass('d-none');
											}else if (showVideo==4){ //knob 
												$("#videoPanel").removeClass('d-none');
												$("#videoMeter").addClass('d-none');
												$("#videoKnob").addClass('d-none');
												$("#dPanel").addClass('d-none');
												$("#dMeter").addClass('d-none');
												$("#dKnob").removeClass('d-none');
												$("#lowerText").removeClass('d-none');
												$(".mycall").addClass('d-none');
											};
										}else{ //1-0 digits will show
											var st=$(".status");
											st.removeClass('d-none');
											$("#topVFO").html(bVFO);
											if (showVideo==2){
												gNum=ppanel.getByName("GroupNUM");
												gNum.setVisible(true);
												gDig=ppanel.getByName("GroupDigits");
												gDig.setVisible(false);
												gDig.setActive(false);
												nPos=ppanel.getByName("NUMrect");
												nPos.setActive(true);
												nPos=ppanel.getByName("Guide1");
												nPos.setActive(false);
												nPos=ppanel.getByName("Label1");
												nPos.setActive(true);
												nPos=ppanel.getByName("Slider1");
												nPos.setActive(false);
												nPos=ppanel.getByName("BarBorder");
												nPos.setActive(false);
												nPos=ppanel.getByName("LinearLevel1");
												nPos.setActive(false);
											}else{
												gNum=ppanel.getByName("GroupNUM");
												gNum.setVisible(false);
												gDig=ppanel.getByName("GroupDigits");
												gDig.setVisible(true);
												gDig.setActive(true);
												nPos=ppanel.getByName("NUMrect");
												nPos.setActive(false);
												nPos=ppanel.getByName("Guide1");
												nPos.setActive(false);
												nPos=ppanel.getByName("Label1");
												nPos.setActive(false);
												nPos=ppanel.getByName("Slider1");
												nPos.setActive(false);
												nPos=ppanel.getByName("BarBorder");
												nPos.setActive(false);
												nPos=ppanel.getByName("LinearLevel1");
												nPos.setActive(false);
											}
											$("#colPan").removeClass("col-sm-12");
											$("#colPan").addClass("col-sm-4");
											$("#colKnob").removeClass("col-sm-12");
											$("#colKnob").addClass("col-sm-4");
											$("#colVid").removeClass("col-sm-12");
											$("#colVid").addClass("col-sm-4");
											if (showVideo==1){
												$("#videoPanel").addClass('d-none');
												$("#videoMeter").addClass('d-none');
												$("#knobMeter").addClass('d-none');
												$("#dMeter").removeClass('d-none');
												$("#dPanel").removeClass('d-none');
												$("#dKnob").removeClass('d-none');
												$(".mycall").removeClass('d-none');
												$("#lowerText").removeClass('d-none');
											}else if (showVideo==2){ //meter
												$("#videoPanel").addClass('d-none');
												$("#videoMeter").removeClass('d-none');
												$("#videoKnob").addClass('d-none');
												$("#dPanel").removeClass('d-none');
												$("#dMeter").addClass('d-none');
												$("#dKnob").removeClass('d-none');
												$("#lowerText").removeClass('d-none');
												$(".mycall").addClass('d-none');
											}else if (showVideo==3){ //panel same as meter
												$("#videoPanel").removeClass('d-none');
												$("#videoMeter").addClass('d-none');
												$("#videoKnob").addClass('d-none');
												$("#dPanel").addClass('d-none');
												$("#dMeter").removeClass('d-none');
												$("#dKnob").removeClass('d-none');
												$("#lowerText").removeClass('d-none');
												$(".mycall").removeClass('d-none');
											}else if (showVideo==4){ //knob
												$("#videoPanel").addClass('d-none');
												$("#videoMeter").addClass('d-none');
												$("#videoKnob").removeClass('d-none');
												$("#dPanel").removeClass('d-none');
												$("#dMeter").removeClass('d-none');
												$("#dKnob").addClass('d-none');
												$("#lowerText").removeClass('d-none');
												$(".mycall").removeClass('d-none');
											};
										};
									
									});
								});
	
								$(document).on('click', '#myBank1', function() 
								{
									updateBank(1);
								});
								
								$(document).on('click', '#myBank2', function() 
								{
									updateBank(2);
								});
								
								$(document).on('click', '#myBank3', function() 
								{
									updateBank(3);
								});
								
								$(document).on('click', '#myBank4', function() 
								{
									updateBank(4);
								});
								
								$.post("/programs/GetUserField.php", {un:tUserName, field:'BandEnable'}, function(response)
								{
									if (response==""){
										response="1,1,1,1,1,1,1,1,1,1,1,1,1,1,1";
									}
									bEnable=response.split(",");
									updateButtons();
								});
								
								$.post("/programs/GetUserField.php", {un:tUserName, field:'ModeEnable'}, function(response)
								{
									if (response==""){
										response="1,1,1,1,1,1,1,1,1";
									}
									bModeEnable=response.split(",");
									updateModeButtons();
								});
								
								function updateModeButtons(){
									var bName="";
									for (i=0;i<9;i++){
										switch (i){
											case 0:
												bName="#lsbButton";
												break;
											case 1:
												bName="#usbButton";
												break;
											case 2:
												bName="#usbdButton";
												break;
											case 3:
												bName="#cwButton";
												break;
											case 4:
												bName="#cwrButton";
												break;
											case 5:
												bName="#rttyButton";
												break;
											case 6:
												bName="#fmButton";
												break;
											case 7:
												bName="#amButton";
												break;
											case 8:
												bName="#rttyrButton";
												break;
										};
										if (bModeEnable[i]=="0") {
											$(bName).removeClass("btn-success");
											$(bName).addClass("btn-secondary");
											$(bName).addClass("disabled");
										}else{
											$(bName).removeClass("btn-secondary");
											$(bName).addClass("btn-success");
											$(bName).removeClass("disabled");
										};
									};
								};
								
								function updateButtons(){
									var bName="";
									for (i=0;i<15;i++){
										switch (i){
											case 0:
												bName="#160Button";
												break;
											case 1:
												bName="#80Button";
												break;
											case 2:
												bName="#60Button";
												break;
											case 3:
												bName="#40Button";
												break;
											case 4:
												bName="#30Button";
												break;
											case 5:
												bName="#20Button";
												break;
											case 6:
												bName="#17Button";
												break;
											case 7:
												bName="#15Button";
												break;
											case 8:
												bName="#12Button";
												break;
											case 9:
												bName="#10Button";
												break;
											case 10:
												bName="#6Button";
												break;
											case 11:
												bName="#2Button";
												break;
											case 12:
												bName="#125Button";
												break;
											case 13:
												bName="#70Button";
												break;
											case 14:
												bName="#23Button";
												break;
										};
										if (bEnable[i]=="0") {
											$(bName).removeClass("btn-success");
											$(bName).addClass("btn-secondary");
											$(bName).addClass("disabled");
										}else{
											$(bName).removeClass("btn-secondary");
											$(bName).addClass("btn-success");
											$(bName).removeClass("disabled");
										};
									};
								};
								
								$(document).on('click', '#closeModalInput', function(){ 
									var x = document.getElementById("curFreq"); 
									var f = x.value;
									if (f != null || f !=""){
										$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: f, table: "RadioInterface"});
										$.post("/programs/SetSettings.php", {field: "SubOut", radio: tMyRadio, data: f, table: "RadioInterface"});
									}
									var x = document.getElementById("curMode1"); 
									var m = x.value;
									if (m=="PKTUSB/USB-D" || m=="USB-D"){
										m="PKTUSB";
									}
									if (m != null || m !=""){
										var x = document.getElementById("curPassband1").value;
										var xP=x.split("="); 
										var tC="*M "+m+" "+xP[1];
										tC=tC.replace("Hz","");
										$.post("/programs/SetSettings.php", {field: "CommandOut", radio: tMyRadio, data: tC,table:"RadioInterface"});
									};
								});
	
							}; //end ready
							
							$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'DisableSplitPolling', table: 'MySettings'}, function(response)
							{
								tSplitDisabled=response;
								if (tSplitDisabled==1){
									$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'SplitOut'}, function(response) 
									{
										tSplitOn=response;
										toggleSplitButton(tSplitOn);
									});
								};
							});
							$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'WKFunction', table: 'Keyer'}, function(response)
							{
								tMyKeyerFunction=response;
							});
							$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'pttLatch', table: 'MySettings'}, function(response)
							{
								tPTTLatch=response;
							});
							$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'WKRemotePort', table: 'Keyer'}, function(response)
							{
								tMyKeyerPort=response;
							});
							$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'WKRemoteIP', table: 'Keyer'}, function(response)
							{
								tMyKeyerIP=response;
							});
							$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'RadioName', table: 'MySettings'}, function(response)
							{
								tRadioName=response;
								setMiddleBand(response);
							});
							
							$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'RadioModel', table: 'MySettings'}, function(response)
							{
								tRadioModel=response;
							});
							
							$.get('/programs/GetMyRadio.php', 'f=Port&r='+response, function(response1) {
								  var tMyRadioPort=response1;
								  if (tMyRadioPort>4530 && tMyRadioPort<5000){
									  tMyRadioPort=1+(tMyRadioPort-4532)/2;
		//				  			if (tMyRadioPort>0){
										tMyRadioCW=tMyRadioPort;
		//							}else{
		//								tMyRadio=response;
		//							}
								  }else{
									  tMyRadioCW=tMyRadio;
								  }
								$.post('./programs/GetSetting.php',{radio: tMyRadioCW, field: 'Port', table: 'MySettings'}, function(response)
								{
									tRadioPort=response;
								});
								$.post('./programs/GetSetting.php',{radio: tMyRadioCW, field: 'Keyer', table: 'MySettings'}, function(response)
								{
									$('#myKeyer').text("CW: "+response);
									tMyKeyer=response;
								});
								$.post('./programs/GetSetting.php',{radio: tMyRadioCW, field: 'KeyerPort', table: 'MySettings'}, function(response)
								{
									tCWPort=response;
								});
								$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'SlaveCommand', table: 'MySettings'}, function(response){
									if (response == "Macro Decimal"){
										var ple=$('#dLED');
										  ple.removeClass('d-none');
									}
								});
							  });
							  tUpdate = setInterval(updateTimer,300);
							
							$.post('./programs/GetSetting.php',{radio: tMyRadioReal, field: 'PTTMode', table: 'MySettings'}, function(response)
							{
								tMyPTT=response;
							});
							$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'RotorPort', table: 'MySettings'}, function(response)
							{
								tMyRotorPort=response;
								if (tMyRotorPort > 4532 && tMyRotorPort < 5000){  //VE9GJ
									tMyRotorRadio = (tMyRotorPort - 4531)/2;
								}else{
									tMyRotorRadio = tMyRadio;
								}
							});
							$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'LogName', table: 'MySettings'}, function(response)
							{
								$('#myLog').text("Log: "+response);
							});
							$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MacroBankTuner', table: 'RadioInterface'}, function(response){
									mBank=response
									$('#myBank').text("Macro Bank: "+response);
									loadMacroBank(mBank);
									updateBank(mBank);
							})
							$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
							{
								$('#searchText').val(response.toUpperCase());
							});
						  });
	
					});
	
					var jsonKnob='';
					var jsonLED='';
					if (notGotPerfectWidgets==0){
	
						<?php include $dRoot . "/includes/$tThemePanel"; ?>;
						<?php include $dRoot . "/includes/$tThemeMeter"; ?>;
						<?php include $dRoot . "/includes/bigPTT1.json"; ?>;
						<?php include $dRoot . "/includes/led.json"; ?>;
						
						$("#playOK").addClass('d-none');
						$("#play").removeClass('d-none');
						///////////////////////////////////////
						// The three following PerfectWidgets components are proprietary included under a commercial license and are NOT open source
						pmeter = new PerfectWidgets.Widget("dMeter", jsonSMeter);
						ppanel = new PerfectWidgets.Widget("dPanel", jsonPanel);
						pknob = new PerfectWidgets.Widget("dKnob", jsonKnob);
						pled = new PerfectWidgets.Widget("dLED", jsonLED);
						var theme=<?php echo $theme; ?>;
						switch(theme){
						case 0:
							bk="Orange";
							break;
						case 1:
							bk="LCD";
							break;
						case 2:
							bk="LCD";
							break;
						case 3:
							bk="HighCon";
							break;
						case 4:
							bk="Green";
							break;
						}
						var bkd=ppanel.getByName(bk);	
						bkd.setVisible(true);
						//End of commercially licensed components
						///////////////////////////////////////
						slider1 = pknob.getByName("Slider1");
						band2=ppanel.getByName("Band2");
						band1=ppanel.getByName("Band1");
						freq0=ppanel.getByName("Freq10");
						freq0.addOnClickHandler(freq0Click);
						freq1=ppanel.getByName("Freq1");
						freq1.addOnClickHandler(freq1Click);
						freq2=ppanel.getByName("Freq2");
						freq2.addOnClickHandler(freq2Click);
						freq3=ppanel.getByName("Freq3");
						freq3.addOnClickHandler(freq3Click);
						freq4=ppanel.getByName("Freq4");
						freq4.addOnClickHandler(freq4Click);
						freq5=ppanel.getByName("Freq5");
						freq5.addOnClickHandler(freq5Click);
						freq6=ppanel.getByName("Freq6");
						freq6.addOnClickHandler(freq6Click);
						freq7=ppanel.getByName("Freq7");
						freq7.addOnClickHandler(freq7Click);
						freq8=ppanel.getByName("Freq8");
						freq8.addOnClickHandler(freq8Click);
						freq9=ppanel.getByName("Freq9");
						freq9.addOnClickHandler(freq9Click);
						led1=pled.getByName("R1PB");
						led1.addOnClickHandler(sw1Click);
						led2=pled.getByName("R2PB");
						led2.addOnClickHandler(sw2Click);
						led3=pled.getByName("R3PB");
						led3.addOnClickHandler(sw3Click);
						led4=pled.getByName("R4PB ");
						led4.addOnClickHandler(sw4Click);
						led5=pled.getByName("R5PB");
						led5.addOnClickHandler(sw5Click);
						led6=pled.getByName("R6PB");
						led6.addOnClickHandler(sw6Click);
						led7=pled.getByName("R7PB");
						led7.addOnClickHandler(sw7Click);
						led8=pled.getByName("R8PB");
						led8.addOnClickHandler(sw8Click);
					}
					
					function updateFreq(which)
					{
						var pl=$('#play');
						if (!pl.hasClass('d-none')|| tButtonWait==1){
							return;
						}
						tButtonWait=1;
						var num=which;
						if (tSplitOn==0){
							switch (tLine1){
								case ld1:
									tMain=tMain.substring(0, tMain.length-1)+ num ;
									break;
								case ld2:
									tMain=tMain.substring(0, tMain.length-2)+ num + tMain.substring(tMain.length-1);
									doDigit(1,1);
									break;
								case ld3:
									tMain=tMain.substring(0, tMain.length-3)+ num + tMain.substring(tMain.length-2);
									doDigit(10,1);
									break;
								case ld4:
									tMain=tMain.substring(0, tMain.length-4)+ num + tMain.substring(tMain.length-3);
									doDigit(100,1);
									break;
								case ld5:
									tMain=tMain.substring(0, tMain.length-5)+ num + tMain.substring(tMain.length-4);
									doDigit(1000,1);
									break;
								case ld6:
									tMain=tMain.substring(0, tMain.length-6)+ num + tMain.substring(tMain.length-5);
									doDigit(10000,1);
									break;
								case ld7:
									tMain=tMain.substring(0, tMain.length-7)+ num + tMain.substring(tMain.length-6);
									doDigit(100000,1);
									break;
								case ld8:
									tMain=tMain.substring(0, tMain.length-8)+ num + tMain.substring(tMain.length-7);
									doDigit(1000000,1);
									break;
								case ld9:
									doDigit(10000000,1);
									tMain=tMain.substring(0, tMain.length-9)+ num + tMain.substring(tMain.length-8);
									break;
								case ld10:
									doDigit(100000000,1);
									tMain=tMain.substring(0, tMain.length-10)+ num + tMain.substring(tMain.length-9);
									break;
									
							}
							var cFreq2m=("0000000000" + tMain).slice(-10);
							var tMain1=addPeriods(cFreq2m);
							var tF=tMain;
							var cMain=ppanel.getByName("Main");
							cMain.setText(tMain1);
							cMain.setNeedRepaint(true);
							cMain.refreshElement();
							$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: tMain, table: "RadioInterface"}, function(response){
									tButtonWait=0;
								}
							);
						}else{
							switch (stLine1){
								case sld1:
									tSub=tSub.substring(0, tSub.length-1)+num;
									break;
								case sld2:
									tSub=tSub.substring(0, tSub.length-2) +num +  tSub.substring(tSub.length-1);
									doSubDigit(1,1);
									break;
								case sld3:
									tSub=tSub.substring(0, tSub.length-3) +num +  tSub.substring(tSub.length-2);
									doSubDigit(10,1);
									break;
								case sld4:
									tSub=tSub.substring(0, tSub.length-4) +num +  tSub.substring(tSub.length-3);
									doSubDigit(100,1);
									break;
								case sld5:
									tSub=tSub.substring(0, tSub.length-5) +num +  tSub.substring(tSub.length-4);
									doSubDigit(1000,1);
									break;
								case sld6:
									tSub=tSub.substring(0, tSub.length-6) +num +  tSub.substring(tSub.length-5);
									doSubDigit(10000,1);
									break;
								case sld7:
									tSub=tSub.substring(0, tSub.length-7) +num +  tSub.substring(tSub.length-6);
									doSubDigit(100000,1);
									break;
								case sld8:
									tSub=tSub.substring(0, tSub.length-8) +num +  tSub.substring(tSub.length-7);
									doSubDigit(1000000,1);
									break;
								case sld9:
									doSubDigit(10000000,1);
									tSub=tSub.substring(0, tSub.length-9) +num +  tSub.substring(tSub.length-8);
									break;
								case sld10:
								doSubDigit(100000000,1);
								tSub=tSub.substring(0, tSub.length-9) +num +  tSub.substring(tSub.length-9);
								break;
									
							}
							var cFreq2s=("0000000000" + tSub).slice(-10);
							var tSub1=addPeriods(cFreq2s);
							var cSub=ppanel.getByName("Sub");
							cSub.setText(tSub1);
							cSub.setNeedRepaint(true);
							cSub.refreshElement();
							$.post("/programs/SetSettings.php", {field: "SubOut", radio: tMyRadio, data: tSub, table: "RadioInterface"}, function(response){
									tButtonWait=0;
								}
							);
	
						}
						waitRefresh=10;
						
					}
					
				   function freq0Click()
					{
						updateFreq(0);
					}
					
				   function freq1Click()
					{
						updateFreq(1);
					}
					
				   function freq2Click()
					{
						updateFreq(2);
					}
					
				   function freq3Click()
					{
						updateFreq(3);
					}
					
				   function freq4Click()
					{
						updateFreq(4);
					}
					
				   function freq5Click()
					{
						updateFreq(5);
					}
					
				   function freq6Click()
					{
						updateFreq(6);
					}
					
				   function freq7Click()
					{
						updateFreq(7);
					}
					
				   function freq8Click()
					{
						updateFreq(8);
					}
					
				   function freq9Click()
					{
						updateFreq(9);
					}
	
				   function sw1Click()
					{
						setLed(1);
					}
					
				   function sw2Click()
					{
						setLed(2);
					}
					
				   function sw3Click()
					{
						setLed(3);
					}
					
				   function sw4Click()
					{
						setLed(4);
					}
					
				   function sw5Click()
					{
						setLed(5);
					}
					
				   function sw6Click()
					{
						setLed(6);
					}
					
				   function sw7Click()
					{
						setLed(7);
					}
					
				   function sw8Click()
					{
						setLed(8);
					}
					
					function setLed(which)
					{
						if (tNoRadio==false && notGotPerfectWidgets==0){
							$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'Slave', table: 'RadioInterface'}, function(response){
								var tOld=response;
								switch (which)
								{
									case 1:
										whichval=1;
										break;
									case 2:
										whichval=2;
										break;
									case 3:
										whichval=4;
										break;
									case 4:
										whichval=8;
										break;
									case 5:
										whichval=16;
										break;
									case 6:
										whichval=32;
										break;
									case 7:
										whichval=64;
										break;
									case 8:
										whichval=128;
										break;
								}
								var tOld1 = dec_to_bho(tOld, 'B');
								tOld1=padDigits(tOld1,8);
								var bLed = which;//tOld ^ which;
								var bled0 = tOld ^ whichval;
								bled01=dec_to_bho(bled0,'B');
								bled01=padDigits(bled01,8);
								var bLed1 = dec_to_bho(bLed,'B');
								bLed1=padDigits(bLed1,8);
								var mL='R'+which+'O';
								var tL=pled.getByName(mL);
								var ti=8-which;//8-i;
								if (bled01.substr(ti,1,1)==1 && tOld1.substr(ti,1,1)==0){
									tL.setVisible(false);
									var tCom="!SW"+which+"-1";
								}else{
									tL.setVisible(true);
								}
		
								$.post("/programs/SetSettings.php", {field: 'Slave', radio: tMyRadio, data: bled0, table: "RadioInterface"});
							});
						}else{
							alert('Please connect radio.');
						}			
					}
					
					dec_to_bho  = function(n, base) {
						 if (n < 0) {
							  n = 0xFFFFFFFF + n + 1;
						 } 
						switch (base)  
						{  
							case 'B':  
								return parseInt(n, 10).toString(2);
								break;  
							case 'H':  
								return parseInt(n, 10).toString(16);
								break;  
							case 'O':  
								return parseInt(n, 10).toString(8);
								break;  
							default:  
								return("Wrong input.........");  
						}  
					}
	
					$( ".slider" ).on('touchstart',function() {
						stopSliderScroll();
					});
					
					$( ".slider" ).on('touchend',function(e) {
						return true;
					});
					
					function stopSliderScroll(){
						$('.slider').bind('touchmove', function(e) {
						  e.preventDefault();
						  return true;
						});
					};
	
					$( "#dKnob" ).on('touchstart',function() {
						stopScroll();
					});
					
					$( "#dKnob" ).on('touchend',function(e) {
						return true;
					});
					
					function stopScroll(){
						$('#dKnob').bind('touchmove', function(e) {
						  e.preventDefault();
						  return true;
						});
					}
					stopScroll();
					
					$( "#dKnob" ).mouseover(function() {
						tOverPanel=false;
						$('#dKnob').bind('mousewheel', function(e) {
							  e.preventDefault();
						});
					});
	 
					$( "#dKnob" ).mouseleave(function() {
						tOverPanel=false;
						$('body').on('scroll mousewheel', function(e) {
							  return true;
						});
					});
	 
	 
					$( "#dPanel" ).mouseover(function() {
						tOverPanel=true;
						$('#dPanel').bind('mousewheel', function(e) {
							  e.preventDefault();
						});
					});
	 
					$( "#dPanel" ).mouseleave(function() {
						tOverPanel=false;
						$('body').on('scroll mousewheel', function(e) {
							  return true;
						});
					});
	 
					$(document).on('click', '#logoutButton', function() 
					{
						openWindowWithPost("/login.php", {
							status: "loggedout",
							username: tUserName});
					});	
					
	/*			   $(document).on('click', '#myLock', function() 
					{
						if (tKnobLock==1){
							tKnobLock=0;
							$('.tActive').addClass('btn-small-lock-off');
							$('.tActive').removeClass('btn-small-lock-on');
						}else{
							tKnobLock=1;
							$('.tActive').addClass('btn-small-lock-on');
							$('.tActive').removeClass('btn-small-lock-off');
						}
				   });	
	*/				
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
						form.submit();
					};
					
					var knobOld=0;
					if (notGotPerfectWidgets==0){
						slider1.addValueChangedHandler(
					   function (sender, e) 
						{
							if (tNoRadio==true){
								return;
							}
							var knobVal=parseInt(sender.getValue());
							var knobChange = parseInt(knobVal/10)-knobOld;
							var pl=$('#play');
							if (knobChange!=0 && pl.hasClass('d-none') && tKnobLock==0)
							{
								if (knobChange < -5 || knobChange > 5)
								{
									knobOld=0;
									knobChange=1;
								}
								if (tSplitOn==0){
									var cMain=ppanel.getByName("Main");
									var tMain=cMain.getText();
									cFreq=tMain.replace(".", "");
									cFreq=cFreq.replace(".", "");
									cFreq1=parseInt(cFreq)+knobChange*tuningIncrement;
									cFreq2=("0000000000" + cFreq1).slice(-10);
									cFreq2=addPeriods(cFreq2);
									knobOld=parseInt(knobVal/10);
									cMain.setText(cFreq2);
									cMain.setNeedRepaint(true);
									cMain.refreshElement();
									$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: cFreq1, table: "RadioInterface"});
								}else{
									var cSub=ppanel.getByName("Sub");
									var tSub1=cSub.getText();
									cFreq=tSub1.replace(".", "");
									cFreq=cFreq.replace(".", "");
									cFreq1=parseInt(cFreq)+knobChange*tuningIncrement;
									cFreq2=("0000000000" + cFreq1).slice(-10);
									cFreq2=addPeriods(cFreq2);
									knobOld=parseInt(knobVal/10);
									cSub.setText(cFreq2);
									cSub.setNeedRepaint(true);
									cSub.refreshElement();
									$.post("/programs/SetSettings.php", {field: "SubOut", radio: tMyRadio, data: cFreq1, table: "RadioInterface"});
								}
								waitRefresh=0;
							}
							if (showVideo==1 || showVideo==3  || showVideo==4){
								var cMeterVal=pmeter.getByName("Slider1");
								cMeterVal.configureAnimation({"enabled":true,"ease":"easeOutBack","duration":1,"direction":"normal"});
							}
						   })
					   }
	
					function resetClass(){
						var sec = 0;
						clearInterval(classTimer);
						classTimer = setInterval(function() { 
							sec=sec-1;
							if (sec == -1) {
								clearInterval(classTimer);
							 }
						}, 1000);
					}
	
	
					function PBmp1Clicked(updateVal)
					{
						tuneMain(1000);
					}
	
					function up1Clicked()
					{
						   tTuneFromTap=1;
						doDigit(1000000, 0);
					}
	
					function sup1Clicked()
					{
						   tTuneFromTap=1;
						doSubDigit(1000000, 0);
					}
	
					function dn1Clicked()
					{
						tTuneFromTap=1;
						doDigit(-1000000, 0);
					}
	
					function sdn1Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(-1000000, 0);
					}
	
					function up10Clicked()
					{
						tTuneFromTap=1;
						doDigit(10000000, 0);
					}
	
					function sup10Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(-10000000, 0);
					}
	
					function dn10Clicked()
					{
						tTuneFromTap=1;
						doDigit(-10000000, 0);
					}
	
					function sdn10Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(-10000000, 0);
					}
	
					function up100Clicked()
					{
						tTuneFromTap=1;
						doDigit(100000000, 0);
					}
					
					function up1000Clicked()
					{
						tTuneFromTap=1;
						doDigit(1000000000, 0);
					}
	
					function sup100Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(100000000, 0);
					}
	
					function sup1000Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(1000000000, 0);
					}
	
					function dn100Clicked()
					{
						tTuneFromTap=1;
						doDigit(-100000000, 0);
					}
	
					function sdn100Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(-100000000, 0);
					}
	
					function sdn1000Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(-1000000000, 0);
					}
	
					function up01Clicked()
					{
						tTuneFromTap=1;
						doDigit(100000, 0);
					}
	
					function sup01Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(100000, 0);
					}
	
					function dn01Clicked()
					{
						tTuneFromTap=1;
						doDigit(-100000, 0);
					}
	
					function sdn01Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(-100000, 0);
					}
	
					function up001Clicked()
					{
						tTuneFromTap=1;
						doDigit(10000, 0);
					}
	
					function sup001Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(10000, 0);
					}
	
					function dn001Clicked()
					{
						tTuneFromTap=1;
						doDigit(-10000, 0);
					}
	
					function sdn001Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(-10000, 0);
					}
	
					function up0001Clicked()
					{
						tTuneFromTap=1;
						doDigit(1000, 0);
					}
	
					function sup0001Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(1000, 0);
					}
	
					function dn0001Clicked()
					{
						tTuneFromTap=1;
						doDigit(-1000, 0);
					}
					
					function sdn0001Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(-1000, 0);
					}
					
	
					function upx1Clicked()
					{
						tTuneFromTap=1;
						doDigit(100,0);
					}
	
					function supx1Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(100,0);
					}
	
					function dnx1Clicked()
					{
						tTuneFromTap=1;
						doDigit(-100,0);
					}
	
					function sdnx1Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(-100,0);
					}
	
					function upx01Clicked()
					{
						tTuneFromTap=1;
						doDigit(10,0);
					}
	
					function supx01Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(10,0);
					}
	
					function dnx01Clicked()
					{
						tTuneFromTap=1;
						doDigit(-10,0);
					}
	
					function sdnx01Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(-10,0);
					}
	
					function upx001Clicked()
					{
						tTuneFromTap=1;
						doDigit(1,0);
					}
	
					function supx001Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(1,0);
					}
	
					function dnx001Clicked()
					{
						tTuneFromTap=1;
						doDigit(-1,0);
					}
					
					function sdnx001Clicked()
					{
						tTuneFromTap=1;
						doSubDigit(-1,0);
					}
					
					function keypadClicked(updateVal)
					{
						if (tNoRadio==false){
							$('#dt').blur().focus();
						}else{
							$("#modalA-body").html("<br>A radio is not connected.<p><p>");			  				
							$("#modalA-title").html("Radio Connection");
							  $("#myModalAlert").modal({show:true});
						}
					}
		
				   function plusClicked(updateVal)
					{
	//	                console.log("+clicked");
						var pl=$('#play');
						if (pl.hasClass('d-none')){
							clearInterval(si);
							if (tSplitOn==0){
								tuneMain(tuningIncrement);
								si=setInterval(plusPressed, 500, "m");
							}else{
								tuneSplit(tuningIncrement);
								si=setInterval(plusPressed, 500, "s");
							}
						};
					}
					
					function plusPressed(which){
	//	                console.log("+pressed");
						if (plus.getPressed()){
							if (which=="m"){
								tuneMain(tuningIncrement);
							}else{
								tuneSplit(tuningIncrement);
							}
						}else{
							clearInterval(si);
						}
						
					}
		
					 function minusClicked()
					{
	//	                console.log("-clicked");
						var pl=$('#play');
						if (pl.hasClass('d-none')){
							clearInterval(si);
							if (tSplitOn==0){
								tuneMain(-1*tuningIncrement);
								si=setInterval(minusPressed, 500, "m");
							}else{
								tuneSplit(-1*tuningIncrement);
								si=setInterval(minusPressed, 500, "s");
							}
						};
					}
					
				  function minusPressed(which){
	//	                console.log("-pressed");
						if (minus.getPressed()){
							if (which=="m"){
								tuneMain(-1*tuningIncrement);
							}else{
								tuneSplit(-1*tuningIncrement);
							}
						}else{
							clearInterval(si);
						}
						
					}
		
					$(window).on('wheel', function(event){
						var pl=$('#play');
						if (pl.hasClass('d-none')){
							if (tOverPanel==true){
								if (tSplitOn==0){
									if (event.originalEvent.deltaY<0){
										tuneMain(tuningIncrement);
									}else{
										tuneMain(-tuningIncrement);
									}
								}else{
									if (event.originalEvent.deltaY<0){
										tuneSplit(tuningIncrement);
									}else{
										tuneSplit(-tuningIncrement);
									}
								}
							}
						}
					})
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
					
					function showSettings(){
						var set=document.getElementById('Preview');
						set.click();
					}
					
					function changeMacroColor(which){
						for (i=1;i<5;i++){
							var mB='#myBank'+i;
							$(mB).removeClass('btn-info');
							$(mB).addClass('btn-color');
						}
						var mB='#myBank'+which;
						$(mB).removeClass('btn-color');
						$(mB).addClass("btn-info");
						$.post("/programs/SetSettings.php", {field: "MacroBankTuner", radio: tMyRadio, data: which, table: "RadioInterface"});
						return false;				
					}
					
/*					function showHelp(){
						var set=document.getElementById('help');
						set.click();
					}
*/					
					function connectRadio(){
						if (tDisconnected==0){
							disconnectRadio();
						};
						tDisconnected=0;
						if (tMyKeyer=='RigPi Keyer'){
							tMyKeyer='rpk';
							tCWPort='/dev/ttyS0';
							$.post('./programs/GetKeyerOut.php',{radio: tMyRadioReal, field: 'WKSpeed'}, function(response) {
								var tSpeed=response;
	//							tSpeedOriginal=tSpeed
								$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadioReal, data: tSpeed, table: "Keyer"});
								$.post("/programs/SetSettings.php", {field: "CWChangeCk", radio: tMyRadioReal, data: 1, table: "Keyer"});
								$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: "", table: "RadioInterface"});
							});
						}else if (tMyKeyer=='via CAT'){
							tMyKeyer='cat';
						}else if (tMyKeyer=="WinKeyer"){
							tMyKeyer="wkr";
						}
					  	var pl=$('#play');
					  	pl.addClass('d-none');
					  	var pl=$('#playOK');
					  	pl.addClass('d-none');
						var el=$('#spinner');
						el.addClass('fa-spin');
					  	el.removeClass('d-none');
					  	$.post('./programs/hamlibDo.php', {test: 0, keyer: tMyKeyer, radio: tMyRadio, user: tUserName, port: tCWPort, tcpPort: "30001", rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction}, function(response) {
						  	if (tDisconnected==1){
							  	return;
						  	}
						  	if (response.length>30){
								$("#modalA-body").html(response);			  				
								$("#modalA-title").html("Radio Connection");
							  	$("#myModalAlert").modal({show:true});
							  	if (tMyPTT==3){
								  	$.post('./programs/doGPIOPTT.php', {PTTControl: "on"}); 			  				
							  	}
							  	var pl=$('#play');
							  	pl.addClass('d-none');
							  	var pl=$('#playOK');
							  	pl.removeClass('d-none');
							  	var el=$('#spinner');
							  	el.addClass('d-none');
							  	setTimeout(function(){ 
								  	$("#myModalAlert").modal('hide');
							 	},
							  	4000);
						  	}else{
							  	var text="<br>The radio did not connect due to an unknown problem.  Please check settings in SETTINGS>Radio>Advanced.<p><p>"
							  	$("#modalA-body").html(text);			  				
								$("#modalA-title").html("Radio Problem");
							  	$("#myModalAlert").modal({show:true});
							  	var el=$('#spinner');
							  	el.addClass('d-none');
							  	var pl=$('#play');
							  	pl.removeClass('d-none');
							  	var pl=$('#playOK');
							  	pl.addClass('d-none');
							  	setTimeout(function(){ 
								  	$("#myModalAlert").modal('hide');
							 	},
							  	3000);
						  	}
						});
					}
	
					//vfo
					$(document).on('click', '#connect', function() 
					{
						connectRadio();
					});	
					
					function setSplit(which){
						if (tSplitOn==0){
							$.post("/programs/SetSettings.php", {field: "SplitOut", radio: tMyRadio, data: 1, table: "RadioInterface"});
						}else{
							$.post("/programs/SetSettings.php", {field: "SplitOut", radio: tMyRadio, data: 0, table: "RadioInterface"});
						}
					}

					function disconnectRadio(){
						tSplitOn=0;
							toggleSplitButton(tSplitOn);
							var el=$('#spinner');
		  					el.addClass('d-none');
		  					tDisconnected=1;
	   					$.post("/programs/SetSettings.php", {field: "SplitOut", radio: tMyRadio, data: tSplitOn, table: "RadioInterface"});
		  					$.post('./programs/disconnectRadio.php', {radio: tMyRadio, user: tUserName, rotor: tMyRotorRadio}, function(response) {
								$("#modalA-body").html(response);			  				
								$("#modalA-title").html("Radio Connection");
			  					$("#myModalAlert").modal({show:true});
			  					setTimeout(function(){ 
				  					$("#myModalAlert").modal('hide');
			 					},
			  					2000);
			   					$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: "OFF", table: "RadioInterface"});
			  					if (tMyPTT==3){
				  					$.post('./programs/doGPIOPTT.php', {PTTControl: "off"}); 			  				
			  					}
							});
							waitRefresh=4;
					}
	
					$(document).on('click', '#disconnect', function() 
					{
						disconnectRadio();
					});	
	
	
					$(document).on('click', '#A2BButton', function() 
					{
					   $.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'MainIn'}, function(response) 
						{
							var tA=response;
							$.post('/programs/SetSettings.php', {field: 'B', radio: tMyRadio, data: tA, table: 'RadioInterface'}, function(response)
							{
								$.post('/programs/SetSettings.php', {field: 'SubOut', radio: tMyRadio, data: tA, table: 'RadioInterface'}, function(response)
								{
								});
							});
						});
					});	
	
					$(document).on('click', '#A2MButton', function() 
					{
						$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'MainIn'}, function(response) 
						{
							var tA=response;
							$.post('/programs/SetSettings.php', {field: 'M', radio: tMyRadio, data: tA, table: 'RadioInterface'});
						})
					});	
	 
					$(document).on('click', '#M2AButton', function() 
					{
						$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'M'}, function(response) 
						{
							var tA=response;
							$.post('/programs/SetSettings.php', {field: 'A', radio: tMyRadio, data: tA, table: 'RadioInterface'}, function(response)
							{
								$.post('/programs/SetSettings.php', {field: 'MainOut', radio: tMyRadio, data: tA, table: 'RadioInterface'});
							});
						})
					});	
	
					$(document).on('click', '#ABButton', function() {
						$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'MainIn'}, function(response) {
							var tA=response;
							$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'SubIn'}, function(response) {
								var tB=response;
								$.post('/programs/SetSettings.php', {field: 'B', radio: tMyRadio, data: tA, table: 'RadioInterface'}, function(response)
								{
									$.post('/programs/SetSettings.php', {field: 'A', radio: tMyRadio, data: tB, table: 'RadioInterface'}, function(response)
									{
										$.post('/programs/SetSettings.php', {field: 'SubOut', radio: tMyRadio, data: tA, table: 'RadioInterface'}, function(response)
										{
											$.post('/programs/SetSettings.php', {field: 'MainOut', radio: tMyRadio, data: tB, table: 'RadioInterface'});
										});
									});
								});
							});
						});
					});	
					<?php require $dRoot . "/includes/buildMacros.php"; ?>
					
					
					$(document).on('click', '#SplitaButton', function() 
					{
						if (tNoRadio==true){
							$("#modalA-body").html("<br>The radio is not connected.<p><p>");			  				
							$("#modalA-title").html("Radio Connection");
							  $("#myModalAlert").modal({show:true});
							  setTimeout(function(){ 
								  $("#myModalAlert").modal('hide');
							 },
							  2000);
							return;
						}
						var btn =document.getElementById('SplitaButton');
						if (tSplitDisabled==0){
							$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'SplitIn'}, function(response) 
							{
								var tSplit=response;
								if (tSplit==1)
								{
									tSplit=0;
								}else{
									tSplit=1;
								}
								tSplitOn=tSplit;
							   if (tSplitOn==1){
									doDigit(0, 1);
									doSubDigit(tuningIncrement, 1);
								}else{
									doSubDigit(0, 1);
									doDigit(tuningIncrement, 1)
								}
								toggleSplitButton(tSplit);
								waitRefresh=0;
								$.post("/programs/SetSettings.php", {field: "SplitOut", radio: tMyRadio, data: tSplitOn, table: "RadioInterface"});
							 });
						}else{
							if (tSplitOn==0){
								tSplitOn=1;
								doDigit(0, 1);
								doSubDigit(tuningIncrement, 1);
							}else{
								tSplitOn=0;
								doSubDigit(0, 1);
								doDigit(tuningIncrement, 1)
							}
							toggleSplitButton(tSplitOn);
							$.post("/programs/SetSettings.php", {field: "SplitOut", radio: tMyRadio, data: tSplitOn, table: "RadioInterface"});
						}
					});	
	
					$('#dt').keyup(function(e) {
						var t=e.key;
						if (e.which>47 && e.which< 58 && tButtonWait==0){
							if (tNoRadio==true || t===""){
								$('#dt').blur().focus();
								$('#dt').val('');
								return;
							}
							alreadyDone=0;
							$('#dt').blur().focus();
							$('#dt').val(t);
							numTune(t);
						}else{
							return;
						}
					});
					
				   //bands
					$(document).on('click', '#160Button', function() 
					{
						if (bEnable[0]==1){
							getBandMemory('160');
						}
					});	
	
					$(document).on('click', '#80Button', function() 
					{
						if (bEnable[1]==1){
							getBandMemory('80');
						}
					});	
	
					$(document).on('click', '#60Button', function() 
					{
						if (bEnable[2]==1){
							getBandMemory('60');
						}
					});	
	
					$(document).on('click', '#40Button', function() 
					{
						if (bEnable[3]==1){
							getBandMemory('40');
						}
					});	
	
					$(document).on('click', '#30Button', function() 
					{
						if (bEnable[4]==1){
							getBandMemory('30');
						}
					});	
	
					$(document).on('click', '#20Button', function() 
					{
						if (bEnable[5]==1){
							getBandMemory('20');
						}
					});	
	
					$(document).on('click', '#17Button', function() 
					{
						if (bEnable[6]==1){
							getBandMemory('17');
						}
					});	
	
					$(document).on('click', '#15Button', function() 
					{
						if (bEnable[7]==1){
							getBandMemory('15');
						}
					});	
	
					$(document).on('click', '#12Button', function() 
					{
						if (bEnable[8]==1){
							getBandMemory('12');
						}
					});	
	
					$(document).on('click', '#10Button', function() 
					{
						if (bEnable[9]==1){
							getBandMemory('10');
						}
					});	
	
					$(document).on('click', '#6Button', function() 
					{
						if (bEnable[10]==1){
							getBandMemory('6');
						}
					});	
	
					$(document).on('click', '#2Button', function() 
					{
						if (bEnable[11]==1){
							getBandMemory('2');
						}
					});	
	
					$(document).on('click', '#125Button', function() 
					{
						if (bEnable[12]==1){
							getBandMemory('1.25');
						}
					});	
					
					$(document).on('click', '#70Button', function() 
					{
						if (bEnable[13]==1){
							getBandMemory('70');
						}
					});	
					
					$(document).on('click', '#23Button', function() 
					{
						if (bEnable[14]==1){
							getBandMemory('23');
						}
					});	
	
					$(document).on('click', '#cwButton', function() 
					{
						if (bModeEnable[3]==1){
							$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'CW', table: "RadioInterface"});
							waitRefresh=6;
						}
					});	
	
					$(document).on('click', '#fmButton', function() 
					{
						if (bModeEnable[6]==1){
							$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'FM', table: "RadioInterface"});
							waitRefresh=6;
						};
					});	
	
					$(document).on('click', '#lsbButton', function() 
					{
						if (bModeEnable[0]==1){
							$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'LSB', table: "RadioInterface"});
							waitRefresh=6;
						};
					});	
	
					$(document).on('click', '#usbButton', function() 
					{
						if (bModeEnable[1]==1){
							$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'USB', table: "RadioInterface"});
							waitRefresh=6;
						};
					});	
	
					$(document).on('click', '#cwrButton', function() 
					{
						if (bModeEnable[4]==1){
							$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'CWR', table: "RadioInterface"});
							waitRefresh=6;
						};
					});	
	
					$(document).on('click', '#amButton', function() 
					{
						if (bModeEnable[7]==1){
							$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'AM', table: "RadioInterface"});
							waitRefresh=6;
						};
					});	
					
					$(document).on('click', '#rttyButton', function() 
					{
						if (bModeEnable[5]==1){
							$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'RTTY', table: "RadioInterface"});
							waitRefresh=6;
						};
					});	
					
					$(document).on('click', '#rttyrButton', function() 
					{
						if (bModeEnable[8]==1){
							$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'RTTYR', table: "RadioInterface"});
							waitRefresh=6;
						};
					});	
					
					$(document).on('click', '#usbdButton', function() 
					{
						if (bModeEnable[2]==1){
							$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'PKTUSB', table: "RadioInterface"});
							waitRefresh=6;
						};
					});	
					
					$(document).on('click', '#searchButton', function() 
					{
						var dx=$('#searchText').val().toUpperCase();
						if (dx.length==0 || ~dx.indexOf('*')){
							return;
						}
							$.post('/programs/GetUserField.php',{un: tUserName, field: 'uID'}, function(response) {
								tUser=response;
								$.post("./programs/GetCallbook.php", {call: dx, what: 'QRZData', user: tUser, un: tUserName},function(response){
									$(".modal-body").html(response);
								  $.post("./programs/GetCallbook.php", {call: dx, what: 'QRZpix', user: tUser, un: tUserName},function(response){
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
							$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: dx, table: "MySettings"});
						})
					
					});
		
				function setTopBand(tText){
					if (notGotPerfectWidgets==1){
						return;
					}
					band2.setText(tText);
					band2.setNeedRepaint(true);
					band2.refreshElement();
				}
	
				function setMiddleBand(tText){
					if (notGotPerfectWidgets==1){
						return;
					}
					band1.setText(tText);
					band1.setNeedRepaint(true);
					band1.refreshElement();
				}
	
				function getBandMemory(nBand)
				{
					var pl=$('#play');
					if (pl.hasClass('d-none')){
						if (tNoRadio==true){
							$("#modalA-body").html("<br>The radio is not connected.<p><p>");			  				
							$("#modalA-title").html("Radio Connection");
							  $("#myModalAlert").modal({show:true});
							  setTimeout(function(){ 
								  $("#myModalAlert").modal('hide');
							 },
							  2000);
							return;
						}
						if (nBand==23 || nBand==70 ){
							setTopBand(nBand+"cm");
						}else{
							setTopBand(nBand+"m");
						}
						var qBand=nBand+'L';
						var tF='0';
						
						 $.post("/programs/SetFrequencyMem.php", {radio: tMyRadio, main: tMain, sub: tSub, mode: tMode}, function(response){
							$.post('/programs/GetFrequencyMem.php',{radio: tMyRadio, band: qBand}, function(response) 
							{
								var obj = JSON.parse(response);
								var tF=obj[0];
								var tM=obj[1];
								waitRefresh=0;
								$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: tF, table: "RadioInterface"});
								$.post("/programs/SetSettings.php", {field: "SubOut", radio: tMyRadio, data: tF, table: "RadioInterface"});
								$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: tM, table: "RadioInterface"});
							});				
						 });
					 }
				}
				
				var tUpdate = setInterval(bearingTimer,1000)
				function bearingTimer()
				{
					$.post("./programs/GetRotorIn.php", {rotor: tMyRotorRadio},function(response){
						var tAData=response.split('`');
						tAData=response;
						if (tAData=="+"){
							tAData="--";
						}
						var tAz=Math.round(tAData)+"&#176;";
						$(".angle").html(tAz);
					});
					tBand=GetBandFromFrequency(tMain);
					if (tBand.indexOf('NK')>0){
						setTopBand(tBand);	
					}else{
						if (tBand==70 || tBand==23){
							setTopBand(tBand+'cm');	
						}else{
							setTopBand(tBand+'m');	
						}
					}
					$.post('./programs/GetKeyerOut.php',{radio: tMyRadioReal, field: 'WKSpeed'}, function(response) {
						var tSpeed=response;
	//					tSpeedOriginal=tSpeed
						$.post('./programs/GetKeyerOut.php',{radio: tMyRadioReal, field: 'WKPot'}, function(response) {
							if (speedPot!=response){
	//							console.log("sp2: "+speedPot+ " "+response);
								speedPot=response;
								tSpeed=speedPot;
								$.post('/programs/GetKeyerOut.php',{radio: tMyRadioReal, field: 'WKMinWPM'}, function(response) {
									var tMin=response;
									tSpeed=parseInt(tSpeed)+parseInt(tMin);
									$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadioReal, data: tSpeed, table: "Keyer"});
									$.post("/programs/SetSettings.php", {field: "CWChangeCk", radio: tMyRadioReal, data: 1, table: "Keyer"});
								});
							};
	
						});				
					});				
				}
				
				   $(document).keyup(function(e) 
				{
					var t=e.key;
					var w=e.which;
					if (e.key==="Escape") 
					{ // escape key maps to keycode `27`
						holdText='';
						var tNum=String.fromCharCode(10);
						if (tNum==""){
							return;
						}
						$.post("./programs/SetSettings.php", {field: "CWIn", radio: tMyRadioCW, data: tNum, table: "RadioInterface"});
						trOn=0;   //false?
						spacePTT=0;
						setPTT(0,0);
						return false;
					} else if (e.key===" "){
						if(tIgnoreRepeating==false){
							if (tPTTLatch==2){
								spaceUp=true;
								spacePTT=0;
								$.post("./programs/SetSettings.php", {field: "PTTOut", radio: tMyRadioCW, data: 0, table: "RadioInterface"});
								setPTT(0, 0);
							}
						}	
					}
				});			
	
				windowLoaded=true;
	//			tUpdate = setInterval(updateTimer,300);
	
				$.post('/programs/GetRadioCaps.php', {r: tMyRadioReal, q:'radio'}, function(response) {
					  var caps=response;
					  var tBandsAvail=0;
					  if (caps.indexOf('144000000')>0){
						  tBandsAvail=12;
					  }else if (caps.indexOf('54000000')>0){
						  tBandsAvail=11;
					  };
					  if (!caps.indexOf('3500000')>0){
						  tBandsAvail=2;
					  };
	
					  if (tBandsAvail==2){
						  function doBandDown(){
							  switch ("tBand"){
							case "6":
								getBandMemory("2");
								break;
							case "2":
								getBandMemory("6");
								break;
							}
						}
						  function doBandUp(){
							  switch ("tBand"){
							case "6":
								getBandMemory("2");
								break;
							case "2":
								getBandMemory("6");
								break;
							}
						}
						  
					  }else{
						function doBandDown(){
							switch (tBand){
							case "160":
								if (tBandsAvail==11){
									getBandMemory("6");  //does radio do 2?
								}else{
									getBandMemory("2");  //does radio do 2?
								};
								break;
							case "80":
								getBandMemory("160");
								break;
							case "60":
								getBandMemory("80");
								break;
							case "40":
								getBandMemory("60");
								break;
							case "30":
								getBandMemory("40");
								break;
							case "20":
								getBandMemory("30");
								break;
							case "17":
								getBandMemory("20");
								break;
							case "15":
								getBandMemory("17");
								break;
							case "12":
								getBandMemory("15");
								break;
							case "10":
								getBandMemory("12");
								break;
							case "6":
								getBandMemory("10");
								break;
							case "2":
								getBandMemory("6");
								break;
							}
						}
		
						function doBandUp(){
							switch (tBand){
							case "160":
								getBandMemory("80");
								break;
							case "80":
								getBandMemory("60");
								break;
							case "60":
								getBandMemory("40");
								break;
							case "40":
								getBandMemory("30");
								break;
							case "30":
								getBandMemory("20");
								break;
							case "20":
								getBandMemory("17");
								break;
							case "17":
								getBandMemory("15");
								break;
							case "15":
								getBandMemory("12");
								break;
							case "12":
								getBandMemory("10");
								break;
							case "10":
								getBandMemory("6");
								break;
							case "6":
								if (tBandsAvail==11){
									getBandMemory("160");  //does radio do 2?
								}else{
									getBandMemory("2");  //does radio do 2?
								};
								break;
							case "2":
								getBandMemory("160");
								break;
							}
						}
					  }
						function doFCheck(which){
							for (i = 0; i < 32; i++) {
								x=aMacros[i];
								if (x.indexOf(which)>0){
									y=x.split("|");
									y=y[1].replace(which,"").trim();
									processCommand(y,i);
								};
							};
						};
					
					$(document).keydown(function(e){
						var t=e.key;
						e.multiple
						var w=e.which;
						if (e.ctrlKey){
							switch(w){
								case 49: //1
									document.getElementById("160Button").click();
									break;
								case 50: //2
									document.getElementById("80Button").click();
									break;
								case 51: //3
									document.getElementById("60Button").click();
									break;
								case 52: //4
									document.getElementById("40Button").click();
									break;
								case 53: //5
									document.getElementById("30Button").click();
									break;
								case 54: //6
									document.getElementById("20Button").click();
									break;
								case 55: //7
									document.getElementById("17Button").click();
									break;
								case 56: //8
									document.getElementById("15Button").click();
									break;
								case 57: //9
									document.getElementById("12Button").click();
									break;
								case 65: //a
									document.getElementById("10Button").click();
									break;
								case 66: //b
									document.getElementById("6Button").click();
									break;
								case 67: //c
									document.getElementById("2Button").click();
									break;
								case 68: //d
									document.getElementById("125Button").click();
									break;
								case 69: //e
									document.getElementById("70Button").click();
									break;
								case 70: //f
									document.getElementById("23Button").click();
									break;
								case 83: //usbd
									document.getElementById("usbdButton").click();
									break;
								case 76: //lsb
									document.getElementById("lsbButton").click();
									break;
								case 77: //am
									document.getElementById("amButton").click();
									break;
								case 82: //cwr
									document.getElementById("cwrButton").click();
									break;
								case 84: //rtty
									document.getElementById("rttyButton").click();
									break;
								case 85: //usb
									document.getElementById("usbButton").click();
									break;
								case 87: //cw
									document.getElementById("cwButton").click();
									break;
								case 89: //rttyr
									document.getElementById("rttyrButton").click();
									break;
							}
						}
						if (e.altKey){
							switch(w){
								case 49: //1
									mBank=1;
									changeMacroColor(mBank);
									loadMacroBank(mBank);
									break;
								case 50: //2
									mBank=2;
									changeMacroColor(mBank);
									loadMacroBank(mBank);
									break;
								case 51: //3
									mBank=3;
									changeMacroColor(mBank);
									loadMacroBank(mBank);
									break;
								case 52: //4
									mBank=4;
									changeMacroColor(mBank);
									loadMacroBank(mBank);
									break;
								case 65: //c
									showCalendar();
									e.preventDefault();
									break;
								case 67: //c
									connectRadio();
									break;
								case 68: //d
									disconnectRadio();
									break;
								case 69: // e
									showSettings();
									e.preventDefault();
									break;
								case 72: // h
									showHelp();
									e.preventDefault();
									break;
								case 73: // i
									setSplit();
									e.preventDefault();
									break;
								case 75: //k
									var win="/keyer.php?x="+tUserName+"&c="+tMyCall;
									window.open(win, "_self");
									break;
								case 76: //l
									var win="/log.php?x="+tUserName+"&c="+tMyCall;
									window.open(win, "_self");
									break; 
								case 83: // s
									var win="/spots.php?x="+tUserName+"&c="+tMyCall;
									window.open(win, "_self");
									break;
								case 84: // t
									var win="/index.php?x="+tUserName+"&c="+tMyCall;
									window.open(win, "_self");
									break;
								case 87: // w
									var win="/web.php?x="+tUserName+"&c="+tMyCall;
									window.open(win, "_self");
									break;
								case 88: //x
									openWindowWithPost("/login.php", {
									status: "loggedout",
									username: tUserName});
							}
								
							return false
						}
						if (w==191)
						{
							if (e.shiftKey){
								<?php require $dRoot . "/includes/shortcuts.php"; ?>
								$("#modalCO-body").html(tSh);			  				
								$("#modalCO-title").html("Shortcut Keys");
								  $("#myModalCancelOnly").modal({show:true});
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
						if (w>111 && w<125){
							x=w-111;
							x="F"+x+":";
							doFCheck(x);
							return false;
						};
						if (w==32 && notGotPerfectWidgets==0){
	//						console.log("rpt: " + tIgnoreRepeating);
							if (tIgnoreRepeating==false || tPTTLatch==1){
								tIgnoreRepeating=true;
	//							trOn=1;
								if (spacePTT==0){
										setPTT(1,0);
	//									waitRefresh=10;
									spacePTT=1;
								}else{
										setPTT(0,0);
	//									waitRefresh=10;
									spacePTT=0;
									tIgnoreRepeating=true;
								}
							}
							e.preventDefault();
							clearTimeout(resetTimer);
							resetTimer=setTimeout(resetIgnoreRepeating,1000);
							return false;
						}else if (t ==="+"){
							if (tSplitOn==0){
								tuneMain(tuningIncrement);
							}else{
								tuneSplit(tuningIncrement);
							}
							return false;
						}else if (e.which>47 && e.which< 58){
							if (tOverPanel==false){
								return true;
							}
							alreadyDone=0;
							var num = t;
							numTune(num);
							waitRefresh=3;
							return false;
						}else if (document.activeElement===sliderHandle ){
							return false;
						}else if (w ==37){
							if (tSplitOn==0){
								switch (tLine1){
									case ld1:
										doDigit(10,1);
										break;
									case ld2:
										doDigit(100,1);
										break;
									case ld3:
										doDigit(1000,1);
										break;
									case ld4:
										doDigit(10000,1);
										break;
									case ld5:
										doDigit(100000,1);
										break;
									case ld6:
										doDigit(1000000,1);
										break;
									case ld7:
										doDigit(10000000,1);
										break;
									case ld8:
										doDigit(100000000,1);
										break;
									case ld9:
										doDigit(1000000000,1);
										break;
									case ld10:
										doDigit(1,1);
									break;
								}
							}else{
								switch (stLine1){
									case sld1:
										doSubDigit(10,1);
										break;
									case sld2:
										doSubDigit(100,1);
										break;
									case sld3:
										doSubDigit(1000,1);
										break;
									case sld4:
										doSubDigit(10000,1);
										break;
									case sld5:
										doSubDigit(100000,1);
										break;
									case sld6:
										doSubDigit(1000000,1);
										break;
									case sld7:
										doSubDigit(10000000,1);
										break;
									case sld8:
										doSubDigit(100000000,1);
										break;
									case sld9:
										doSubDigit(1000000000,1);
										break;
									case sld10:
										doSubDigit(1,1);
										break;
								}
							}
							return false;
						}else if (w==39){
							if (tSplitOn==0){
								switch (tLine1){
									case ld1:
										doDigit(1000000000,1);
										break;
									case ld2:
										doDigit(1,1);
										break;
									case ld3:
										doDigit(10,1);
										break;
									case ld4:
										doDigit(100,1);
										break;
									case ld5:
										doDigit(1000,1);
										break;
									case ld6:
										doDigit(10000,1);
										break;
									case ld7:
										doDigit(100000,1);
										break;
									case ld8:
										doDigit(1000000,1);
										break;
									case ld9:
										doDigit(10000000,1);
										break;
									case ld10:
										doDigit(100000000,1);
										break;
								}
							}else{
								switch (stLine1){
									case sld1:
										doSubDigit(1000000000,1);
										break;
									case sld2:
										doSubDigit(1,1);
										break;
									case sld3:
										doSubDigit(10,1);
										break;
									case sld4:
										doSubDigit(100,1);
										break;
									case sld5:
										doSubDigit(1000,1);
										break;
									case sld6:
										doSubDigit(10000,1);
										break;
									case sld7:
										doSubDigit(100000,1);
										break;
									case sld8:
										doSubDigit(1000000,1);
										break;
									case sld9:
										doSubDigit(10000000,1);
										break;
									case sld10:
										doSubDigit(100000000,1);
										break;
								}
							}
							return false;
						}else if (w==38){
							plusClicked();
							return false;
						}else if (w==40){
							minusClicked();
							return false;
						}else if (t==="-"){
							if (!e.ctrlKey){
								if (tSplitOn==0){
									tuneMain(-1*tuningIncrement);
								}else{
									tuneSplit(-1*tuningIncrement);
								}
							}else{
								return true;
							}
							return false;
						}else if (t==="["){
							doBandDown();
						}else if (t==="]"){
							doBandUp();
						}
					});
				});
			});
			
			function resetIgnoreRepeating(){
				if (tPTTLatch==2){
					setPTT(0, 0);
					spacePTT=0;
				}
				tIgnoreRepeating=false;
			}
			
			function padDigits(number, digits) {
				return Array(Math.max(digits - String(number).length + 1, 0)).join(0) + number;
			};
			
			$.getScript("/js/modalLoad.js");
	///check here
			function setPTT(state, pttBypass){
				if (notGotPerfectWidgets==0 && tAccessLevel < 4){
					var ttPTT=tMyRadioReal;
					if (tRadioModel=="NET rigctl"){
						if (tRadioPort==4532){
							ttPTT=1;
						}else if (tRadioPort==4534){
							ttPTT=2;
						}else if (tRadioPort==4536){
							ttPTT=3;
						}else if (tRadioPort==4538){
							ttPTT=4;
						}else if (tRadioPort==4540){
							ttPTT=5;
						}else if (tRadioPort==4542){
							ttPTT=6;
						}else if (tRadioPort==4544){
							ttPTT=7;
						}else if (tRadioPort==4548){
							ttPTT=8;
						}else{
							ttPTT=tMyRadioReal;
						}
					}
					if (state==1  && tPTTIsOn==0){
	//					tKnobPTT=1;
						xmit=true;
						trOn=1;
	//	                    spacePTT=0;
						trXmit=true;
						if (pttBypass==0){
							$.post("/programs/SetSettings.php", {field: "PTTOut", radio: ttPTT, data: "1", table: "RadioInterface"});			
						}
	
						if (tMyPTT==1){
							$.post('/programs/doGPIOPTT.php', {PTTControl: "on"});
						}
						var cRX=ppanel.getByName("TR");
						cRX.setText("XMIT");
						cRX.setNeedRepaint(true);
						cRX.refreshElement();
						ptt1.setVisible(true);
						ptt.setVisible(false);
						var cMeterLabel=pmeter.getByName("MtrFn");
						cMeterLabel.setText(mtrLabel);
						cMeterLabel.setNeedRepaint(true);
						cMeterLabel.refreshElement();
						waitRefresh=4;
					}else{
						tKnobPTT=0;
						xmit=false;
						trXmit=false;
						trOn=0;
						if (pttBypass==0){
							$.post("/programs/SetSettings.php", {field: "PTTOut", radio: ttPTT, data: "0", table: "RadioInterface"});
						}
						if (tMyPTT==1)
						{
							$.post('/programs/doGPIOPTT.php', {PTTControl: "off"});
						}
						var cRX=ppanel.getByName("TR");
						cRX.setText("RCV");
						cRX.setNeedRepaint(true);
						cRX.refreshElement();
						ptt1.setVisible(false);
						ptt.setVisible(true);
						var cMeterLabel=pmeter.getByName("MtrFn");
						cMeterLabel.setText("S-Meter");
						cMeterLabel.setNeedRepaint(true);
						cMeterLabel.refreshElement();
						waitRefresh=4;
					}
				}
			}
			
			function toggleSplitButton(split)
			{
				if (split==1)
				{
					$('.btn-toggle').removeClass('btn-color');
					$('.btn-toggle').removeClass('btn-primary');
					$('.btn-toggle').addClass('btn-danger');
				}
				else 
				{
					$('.btn-toggle').removeClass('btn-danger');
					$('.btn-toggle').addClass('btn-color');
					$('.btn-toggle').addClass('btn-primary');
				}
			}            
		
			function doFKey(which,btn){
				var thisOne=which.substring(0, 1);
				var tCommand=which.substr(which.indexOf(":")+1);
				processCommand(tCommand, btn);
			}
	
			function loadMacroBank(which)
			{
				which=which.toString();
				switch (which){
					case "1":
						latchBtn=latchBtn1;
						break;
					case "2":
						latchBtn=latchBtn2;
						break;
					case "3":
						latchBtn=latchBtn3;
						break;
					case "4":
						latchBtn=latchBtn4;
						break;
				};
				$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'Macros'+which}, function(response)
				{
					var tMacros=decodeURIComponent((response+'').replace(/\+/g,'%20'));
					aMacros=tMacros.split('~');
					var mBtn;
					aMCommands=[];
					for (i = 0; i < 32; i++) {
						var mID='m'+i+'Button';
						var tLabel = aMacros[i];
						tLabel=tLabel.split('|');
						if (tLabel[1].indexOf("+")>0){
							btnLatchColor=tLabel[1].substr(tLabel[1].indexOf("+")+1);
						}else{
							btnLatchColor="btn-info";
						}
						var btn =document.getElementById(mID);
						btn.innerHTML=tLabel[0];
						var arlbtn=latchBtn[i];
						if (arlbtn==null || arlbtn==""){
							arlbtn="?";
						}
						if (arlbtn=="?"){
							$(btn).removeClass(btnLatchColor);
							$(btn).addClass("btn-color");
						}else{
							$(btn).removeClass("btn-color");
							$(btn).addClass(btnLatchColor);
						}
						if (tLabel[0]=="BANK"){
							mBtn=btn;
							mBtn.innerHTML="BANK "+mBank;
						}
						aMCommands.push(tLabel[1]);
					}
					if (tAccessLevel<4){
						$.post('/programs/isSupported.php',{radio: tMyRadioReal, getSet: 'set', text:'AF('}, function(response){
							if (response==1 && sliderAFGainOride>0){
								$("#AF").removeClass('d-none');
							}
						});
						$.post('/programs/isSupported.php',{radio: tMyRadioReal, getSet: 'set', text:'RF('}, function(response){
							if (response==1 && sliderRFGainOride>0){
								$("#RF").removeClass('d-none');
							}
						});
						$.post('/programs/isSupported.php',{radio: tMyRadioReal, getSet: 'set', text:'RFPOWER('}, function(response){
							if (response==1 && sliderPwrOutOride>0){
								$("#Pwr").removeClass('d-none');
							}
						});
						$.post('/programs/isSupported.php',{radio: tMyRadioReal, getSet: 'set', text:'MICGAIN('}, function(response){
							if (response==1 && sliderMicLvlOride > 0){
								$("#Mic").removeClass('d-none');
							}
						});
					}				
				})
			}
	
			var aMCommands=[];
			//processCommand ties into /includes/buildMacros.php
			
			function processCommand(which,btn)
			{
				var tMe=tMyCall;
				var tWhat = which.replace(/'\*/g, tMe);
				which = tWhat.replace(/'X/g,$('#searchText').val());
				var tPre=which.substring(0, 1);
				var tPost=which.substring(1);
				if (which.indexOf("+")>0){
					btnLatchColor=which.substr(which.indexOf("+")+1);
					which=which.substr(0,which.indexOf("+"));
				}else{
					btnLatchColor="btn-info";
				}
				if (tPre=="F"){
					doFKey(tPost,btn);
					return false;
				}
				if (tPre=="{"){
					var lbtn=btn.substring(2,btn.indexOf("B"));
					var arlbtn=latchBtn[lbtn];
					if (arlbtn==null || arlbtn==""){
						arlbtn="?";
					}
					var lt1;
					if (arlbtn !== "?"){
						tPost=latchBtn[lbtn];
						latchBtn[lbtn]="?";
						tPre=tPost.substr(0,1);
						tPost=tPost.substring(1);
						if (tPost.indexOf("+")>0){
							tPost=tPost.substring(0,tPost.indexOf("+"));
						}
						$(btn).removeClass(btnLatchColor);
						$(btn).addClass("btn-color");
						lt1=latchBtn.join(",");
					}else{
						tPre=tPost.substring(0,1);
						var tPost1=tPost.substring(tPost.indexOf("}")+1);
						tPost=tPost.substring(1,tPost.indexOf("}"));
						if (tPost1.indexOf("+")>0){
							tPost1=tPost1.substring(0,tPost1.indexOf("+"));
						}
						latchBtn[lbtn]=tPost1;
						$(btn).removeClass("btn-color");
						$(btn).addClass(btnLatchColor);
						lt1 = latchBtn.join(",");
					}
					which=tPre+tPost;
					var tLat="latchBtn"+mBank;
					$.post("/programs/SetSettings.php", {field: tLat, radio: tMyRadio, data: lt1, table: "RadioInterface"}, function(response){
						});
				}
				if (tPre=="/"){
					var tDX=$('#searchText').val().toUpperCase();
					tPost=tPost.replace('<dxcall>',tDX);
					if (tPost.indexOf('<mode>')>0){
						var tM='';
						if (tMode=="USB" || tMode=="LSB"|| tMode=="AM" || tMode=="FM"){
							tM="PHONE";
						}else if (tMode=="PKTUSB" || tMode=="USB-D" || tMode=="RTTY" || tMode=="RTTYR"){
							tM="DIGI";
						}else{
							tM="CW"
						}
						tPost=tPost.replace('<mode>',tM);	
					}
					if (tPost.indexOf("<band>")>0){
						var tB=parseInt(tMain);
						tB=addPeriods(tB);
						tB=tB.slice(0,tB.length-6);
						if (tB>1000){
							tB='1.2GHz';
						}else if(tB>400){
							tB='430MHz';
						}else if(tB>220){
							tB='220MHz';
						}else if(tB>143){
							tB='144MHz';
						}else if(tB>49){
							tB='50MHz';
						}else if(tB>27){
							tB='28MHz';
						}else if(tB>24){
							tB='24MHz';
						}else if(tB>21){
							tB='21MHz';
						}else if(tB>17){
							tB='18MHz';
						}else if(tB>13){
							tB='14MHz';
						}else if(tB>10){
							tB='10MHz';
						}else if(tB>6){
							tB='7MHz';
						}else if(tB>5){
							tB='5MHz';
						}else if(tB>3){
							tB='3.5MHz';
						}else if(tB>1.7){
							tB='1.8MHz';
						}else{
							tB=tB+'MHz';
						}
						tPost=tPost.replace('<band>',tB);
					}
					window.open(tPost, '_blank');
				}
				if (tPre=='$'){
					if (tPost.indexOf('<02>')==0){
						var tSpeed=tPost.split(">");
						if (tSpeed[1]==0){
							$.post('./programs/GetSetting.php',{radio: tMyRotorRadio, field: 'WKSpeedOriginal', table: 'Keyer'}, function(response)
							{
								tSpeedOriginal=response;
								tPost="<02><"+parseInt(tSpeedOriginal).toString(16)+">";
								$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tSpeedOriginal, table: "Keyer"});
							});
						}else{
							tPost="<02><"+parseInt(tSpeed[1]).toString(16)+">";
							$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tSpeed[1], table: "Keyer"});
							$.post("/programs/SetSettings.php", {field: "WKSpeedOriginal", radio: tMyRadio, data: tSpeedOriginal, table: "Keyer"});
						}
					}
					$.post("/programs/ConcatSettings.php", {field: "CWIn", radio: tMyRadioCW, data: tPost, table: "RadioInterface"});
					return false;
				}
				if (which=="*PS1;")
				{
					if (tRadioModel !="NET rigctl"){
						if (tNoRadio==false){
							$("#modalCO-body").html("Radio is already on.");			  				
							$("#modalCO-title").html("Radio Power");
							  $("#myModalCancelOnly").modal({show:true});
							setTimeout(function(){ 
								  $("#myModalCancelOnly").modal('hide');
							 },
							  2000);
							return true;
						}
						$("#modalCO-body").html("Radio is powering up, please wait.");			  				
						$("#modalCO-title").html("Radio Power");
						  $("#myModalCancelOnly").modal({show:true});
						$.post('/programs/powerOn.php', {radio: tMyRadio, user: tUserName}, function(response){
							$("#modalCO-body").html(response);			  				
							  setTimeout(function(){ 
								  $("#myModalCancelOnly").modal('hide');
							 },
							  2000);
						});
					}else{
						$("#modalCO-body").html("Shared radio access can't control power on/off.");			  				
						$("#modalCO-title").html("Radio Power");
						$("#myModalCancelOnly").modal({show:true});
					}
				}else if (which=="*PS0;"){
					if (tRadioModel !="NET rigctl"){
						tSplitOn=0;
						toggleSplitButton(tSplitOn);
						$.post("/programs/SetSettings.php", {field: "SplitOut", radio: tMyRadio, data: tSplitOn, table: "RadioInterface"});
						$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: "OFF", table: "RadioInterface"}, function(response){
								if (tMyPTT==3){
									$.post('./programs/doGPIOPTT.php', {PTTControl: "off"}); 			  				
								}
							$.post('./programs/disconnectRadio.php', {radio: tMyRadio, user: tUserName, rotor: tMyRotorRadio}, function(response){
								$("#modalCO-body").html("Radio is powering down, please wait.");			  				
								$("#modalCO-title").html("Radio Power");
								$("#myModalCancelOnly").modal({show:true});
								$.post('/programs/powerOff.php', {radio: tMyRadio, user: tUserName}, function(response){
									$("#modalCO-body").html(response);			  				
									  $.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: 'OFF', table: "RadioInterface"});
									  setTimeout(function(){ 
										  $("#myModalCancelOnly").modal('hide');
									 },
									  2000);
								});
							});
						});
					}else{
						$("#modalCO-body").html("Shared radio access can't control power on/off.");			  				
						$("#modalCO-title").html("Radio Power");
						$("#myModalCancelOnly").modal({show:true});
					}
						
				}else if (which=="!ROTATE"){
					$.post('./programs/GetSetting.php',{radio: tMyRotorRadio, field: 'RotorAzIn', table: 'RadioInterface'}, function(response)
					{
							var caption="Rotor bearing is now " + parseInt(response) + " deg.\n\nEnter new value and click OK.";
							var curBeam=parseInt(response);
							var text = prompt(caption, parseInt(response));
							if (parseInt(text) > -1 && parseInt(text) < 361 && parseInt(text) != curBeam){
								$.post("./programs/SetMyRotorBearing.php", {w: "turn", i: tMyRotorRadio, a: text}); //VE9GJ
							}
					});
				}else if (which=="!RTR STOP"){
					$.post("./programs/SetMyRotorBearing.php", {w: "stop", i: tMyRotorRadio, a: ''});  //VE9GJ
				}else if (which=="!BANK"){
					mBank=parseInt(mBank)+1;
					if (mBank==5){mBank=1};
					loadMacroBank(mBank);
					for (i=1;i<5;i++){
						var mB='#myBank'+i;
						$(mB).removeClass('btn-info');
						$(mB).addClass('btn-color');
					}
					var mB='#myBank'+mBank;
					$(mB).removeClass('btn-color');
					$(mB).addClass("btn-info");
					$.post("/programs/SetSettings.php", {field: "MacroBankTuner", radio: tMyRadio, data: mBank, table: "RadioInterface"});
					return false;				
				}else if (which=="!PTTON"){
					  $.post('/programs/doGPIOPTT.php', {PTTControl: "on"}); 			  				
				}else if (which=="!PTTOFF"){
					  $.post('/programs/doGPIOPTT.php', {PTTControl: "off"}); 			  				
				}else if (which.substring(0,3)=="!SW"){
					$.post("/programs/SetSettings.php", {field: "CommandOut", radio: tMyRadio, data: which, table: "RadioInterface"});
				}else{
					switch(tPre)
					{
					case '*':	//direct radio command using hamlib format
						var pl=$('#play');
						if (!pl.hasClass('d-none')){
							return;
						}
						$.post("/programs/SetSettings.php", {field: "CommandOut", radio: tMyRadio, data: "*"+tPost, table: "RadioInterface"});
						break;
					case '#':	//direct system command
						$.post("/programs/systemExec.php", {command: tPost});
						break;
					case '!':	//special command
						if (tPost=='ESC'){
							setPTT(0, 0);
							var tNum=String.fromCharCode(10);
							$.post("./programs/SetSettings.php", {field: "CWIn", radio: tMyRadioCW, data: tNum, table: "RadioInterface"});
						}else if (tPost=='TUNE'){
							if (tTuneOn==1){
								tTuneOn=0;
								var tNum=String.fromCharCode(11)+String.fromCharCode(0);
								$.post("./programs/SetSettings.php", {field: "CWIn", radio: tMyRadioCW, data: tNum, table: "RadioInterface"});
							}else{
								tTuneOn=1;
								var tNum=String.fromCharCode(11)+String.fromCharCode(1);
								$.post("./programs/SetSettings.php", {field: "CWIn", radio: tMyRadioCW, data: tNum, table: "RadioInterface"});
							}
						}else if (tPost=='TUNETO'){
							if (tDisconnected==1){
								alert("Please connect radio.");
								return false;
							}
							if (tMode==0){
								alert("Please try again, radio not ready.");
								return false;
							}
							var text=tMode;
							$("#curMode1").val(text);
							$("#modal-title").html("Tune to");
							$("#modalI-body").html("Enter any combination and click OK.");
							$("#myModalInput").modal({show:true});
							var x = document.getElementById("curFreq"); 
							x.value=parseInt(tMain);
							$.post("/programs/getRigBandwidths.php", {myRadio:tMyRadio, mode: ""}, function(response){
								var x = document.getElementById("curPassband1"); 
								x.value=response;//("Select Passband");
							});
			
							$.post("./programs/getRigCaps.php", {myRadio: tMyRadio, cap: "Mode list:"}, function(response){
								var tL="";
								var tList1=response;
								var tList=tList1.split(" ");
								for (i=0;i<tList.length-1;i++)
								{
									if (tList[i]=="PKTUSB"){
										tList[i]="PKTUSB/USB-D"
									}
									if (tList[i]=="USB-D"){
										tList[i]="PKTUSB/USB-D"
									}
									tL=tL+"<div class='mymode' id=i<li><a class='dropdown-item' href='#'>"+tList[i]+"</a></li></div>";
								}
								var caps=response;
								x = document.getElementById("modeList"); 
								x.innerHTML=tL;
							});
						
							$.post("./programs/getRigBandwidths.php", {myRadio: tMyRadio, mode: tMode}, function(response){
								var tB="";
								if (tMode=="USB-D"){
									tMode="PKTUSB";
								}
								var tList=response.split("\t");
								for (i=2;i<tList.length;i++){
									var tB1=tList[i];
									var tB2=tB1;
									if (tB1.indexOf("kHz")>0){
										tB2=tB1.split("=");
										tB2[1]=tB2[1].replace(" kHz","");
										tB2[1]=tB2[1]*1000;
										tB2=tB2[0]+"=" + tB2[1]+" "+"Hz"
									}
									tB2=tB2.replace(".0","");
									tB=tB+"<div class='mypassband' id=i<li><a class='dropdown-item' href='#'>"+tB2+"</a></li></div>";
								};
								x = document.getElementById("passbandList"); 
								x.innerHTML=tB;
							});
						};
							
						if (tPost=="T/R" && tDisconnected==0 && tAccessLevel<4){
							if (trXmit==true){
			//        			trXmit=false;
			//        			xmit=false;
			//        			trOn=0;
								setPTT(0, 0);
							}else{
			//        			trXmit=true;
			//					xmit=true;
			//        			trOn=1;
								setPTT(1, 0);
							}
						}
						break;
					}
				}
			}
			
			$(document).on('click', '.mymode', function() {
				var text = $(this).text();
				$("#curMode1").val(text);
				$.post("/programs/getRigBandwidths.php", {myRadio:tMyRadio, mode: ""}, function(response){
					var x = document.getElementById("curPassband1"); 
					x.value=response;//("Select Passband");
				});
				$.post("./programs/getRigBandwidths.php", {myRadio: tMyRadio, mode: text}, function(response){
					var tB="";
					var tList=response.split("\t");
					for (i=2;i<tList.length;i++){
						var tB1=tList[i];
						if (tB1=="PKTUSB/USB-D"){
							tB1="PKTUSB";
						}
						var tB2=tB1;
						if (tB1.indexOf("kHz")>0){
							tB2=tB1.split("=");
							tB2[1]=tB2[1].replace(" kHz","");
							tB2[1]=tB2[1]*1000;
							tB2=tB2[0]+"=" + tB2[1]+" "+"Hz"
						}
						tB2=tB2.replace(".0","");
						tB=tB+"<div class='mypassband' id=i<li><a class='dropdown-item' href='#'>"+tB2+"</a></li></div>";
					};
					x = document.getElementById("passbandList"); 
					x.innerHTML=tB;
				});
			});
			
			$(document).on('click', '.mypassband', function() {
				var text = $(this).text();
				var x = document.getElementById("curPassband1"); 
				x.value=text;
	//			  $("#curPassband1").val(text);
			});
	
			function tuneMain(increment)
			{
				if (notGotPerfectWidgets==1){
					return;
				}
				if (tTuneFromTap=0){
					toggleSplitButton(0);
				}else{
					tTuneFromTap=0;
				}
			 
				var cMain=ppanel.getByName("Main");
				var tMain=cMain.getText();
				cFreq=tMain.replace(".", "");
				cFreq=cFreq.replace(".", "");
				cFreq1=parseInt(cFreq)+increment;
				if (cFreq1<0){
					return;
				}
				cFreq2=("0000000000" + cFreq1).slice(-10);
				cFreq2=addPeriods(cFreq2);
				cMain.setText(cFreq2);
				cMain.setNeedRepaint(true);
				cMain.refreshElement();
				$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: cFreq1, table: "RadioInterface"});
				waitRefresh=3;
			}
	
			function tuneSplit(increment)
			{
				if (notGotPerfectWidgets==1){
					return;
				}
				toggleSplitButton(1);
				var cSub=ppanel.getByName("Sub");
				var cSubIn=ppanel.getByName("SubInactive");
				cSubIn.setVisible(false);
				var tSub1=cSub.getText();
				cFreq=tSub1.replace(".", "");
				cFreq=cFreq.replace(".", "");
				cFreq1=parseInt(cFreq)+increment;
				if (cFreq1<0){
					return;
				}
				cFreq2=("0000000000" + cFreq1).slice(-10);
				cFreq2=addPeriods(cFreq2);
				cSub.setText(cFreq2);
				cSub.setVisible(true);
				cSub.setNeedRepaint(true);
				cSub.refreshElement();
				$.post("/programs/SetSettings.php", {field: "SubOut", radio: tMyRadio, data: cFreq1, table: "RadioInterface"});
	//            console.log(cFreq1);
				waitRefresh=3;
			}
	
			function setBandMemory(){
					$.post("/programs/SetFrequencyMem.php", {radio: tMyRadio, main: tMain, 
					 sub: tSub, mode: tMode}, function(response){});
				
			}
	
			function GetBandFromFrequency(nFreq)
			{
				if (nFreq > 1800000 && nFreq < 2000000){
					return "160";
				}else if (nFreq > 3500000 && nFreq < 4000000){
					return "80";
				}else if (nFreq > 5330000 && nFreq < 5405010){
					return "60";
				}else if (nFreq > 7000000 && nFreq < 7300000){
					return "40";
				}else if (nFreq > 10100000 && nFreq < 10150000){
					return "30";
				}else if (nFreq > 14000000 && nFreq < 14500000){
					return "20";
				}else if (nFreq > 18060000 && nFreq < 18168000){
					return "17";
				}else if (nFreq > 21000000 && nFreq < 21450000){
					return "15";
				}else if (nFreq > 24890000 && nFreq < 24990000){
					return "12";
				}else if (nFreq > 28000000 && nFreq < 29700000){
					return "10";
				}else if (nFreq > 50000000 && nFreq < 54000000){
					return "6";
				}else if (nFreq > 144000000 && nFreq < 148000000){
					return "2";
				}else if (nFreq > 220000000 && nFreq < 225000000){
					return "1.25";
				}else if (nFreq > 420000000 && nFreq < 450000000){
					return "70";
				}else if (nFreq >= 1240000000 && nFreq < 1300000000){
					return "23";
				}else {
					return "UNK";
				}
			} 
	
			function updateFreqDisp()
			{
				$.post('./programs/GetSelectedRadio.php', {un:tUserName}, function(response) 
				{
					tMyRadio=response;
					$.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:tMyCall }, function(response) 
					{
						var tAData=response.split('`');
						var cMain=ppanel.getByName("Main");
						if (tAData[0]=="OFF"){
							var cFs="0000.000.000";
						}else{
							var cF=("0000000000" + tAData[0]).slice(-10);
							 cFs=addPeriods(cF);
						}
						var tF=cFs;
						tMain=tAData[0];
				//		cFs=cFs.trim();
						cMain.setText(cFs);
						cMain.setNeedRepaint(true);
						cMain.refreshElement();
						var tSplit=tAData[1];
						if (tSplitDisabled==0){
							tSplitOn=tSplit;
						}else{
							$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'SplitOut'}, function(response) 
							{
								if (tSplitOn!=response){
									tSplitOn=response;
									toggleSplitButton(tSplitOn);
								};
							});
						}
						var tBand=GetBandFromFrequency(tAData[0]);
						if (tBand.indexOf('NK')>0){
							var band2=ppanel.getByName("Band2");
							band2.setText(tBand);
							band2.setNeedRepaint(true);
							band2.refreshElement();
						}else{
							var band2=ppanel.getByName("Band2");
							if (tBand==70 || tBand==23){
								band2.setText(tBand+'cm');
							}else{
								band2.setText(tBand+'m');
							}
							band2.setNeedRepaint(true);
							band2.refreshElement();
						}
					   var cSub=ppanel.getByName("Sub");
						var cSubIn=ppanel.getByName("SubInactive");
						if (tAData[0]=="OFF"){
							var cFreq2s="0000.000.000"
						}else{
							var cFreq2s=("0000000000" + tAData[2]).slice(-10);
							var tSub1=cFreq2s;
							cFreq2s=addPeriods(cFreq2s);
						}
				//		cFreq2s1=cFreq2s.trim();
						if (tSplitOn==1){
							cSub.setVisible(true);
							cSubIn.setVisible(false);
							cSub.setText(cFreq2s);
						}else{
							cSub.setVisible(false);
							cSubIn.setVisible(true);
							cSubIn.setText(cFreq2s);
						}
	//					toggleSplitButton(tSplitOn);
						cSub.setNeedRepaint(true);
						cSub.refreshElement();
						var cMode=ppanel.getByName("Mode");
						if (tAData[3]=="PKTUSB"){
							tAData[3]="USB-D";
						}
						cMode.setText(tAData[3]);
						cMode.setNeedRepaint(true);
						cMode.refreshElement();
						//var sl=$('#spinner');
						//if (sl.hasClass('d-none')){
							  //not connecting
						//	  var pl=$('#play');
						//	  pl.addClass('d-none');
						//	  var pl=$('#playOK');
						//	  pl.removeClass('d-none');
						//  }else{
							  //is connecting
						//$("#playOK").addClass('d-none');
						//$("#play").removeClass('d-none');
						/*	  var pl=$('#play');
							  pl.addClass('d-none');
							  var pl=$('#playOK');
							  pl.removeClass('d-none');
						*///  }
						
						tF=tF.trim();
						if (notGotPerfectWidgets==1){
							return;
						}
						if (tF.length==8){
							tF=tF.substr(1);
							if (tF.substr(0,1)=="0"){
								tF=tF.substr(1);
							}
							if (cFreq2s1.substr(0,1)=="."){
								cFreq2s=cFreq2s1.substr(1);
							}
							$("#fPanel1").text("Main: "+tF+" kHz");
							if (tSplitOn==1){
								$("#fPanel2").text("Sub: "+cFreq2s+" kHz");
							}else{
								$("#fPanel2").text("");
							}
							
						}else{
							$("#fPanel1").text("Main: "+tF+" MHz");
							if (tSplitOn==1){
								$("#fPanel2").text("Sub: "+cFreq2s+" MHz");
							}else{
								$("#fPanel2").text("");
							}
						}
						$("#fPanel3").text("Mode: "+tAData[3]);
						$('#fPanel1').attr('style', 'background-color:black');
									})
						if (notGotPerfectWidgets==1){
							return;
						}
						var now = new Date();
						var now_hours=now.getUTCHours();
						now_hours=("00" + now_hours).slice(-2);
						var now_minutes=now.getUTCMinutes();
						now_minutes=("00" + now_minutes).slice(-2);
						//var timeUTC = ppanel.getByName("LocalTime");
						//timeUTC.setText(now_hours+":"+now_minutes);
						//timeUTC.setNeedRepaint(true);
						//timeUTC.refreshElement();
						$("#fPanel5").text(now_hours+":"+now_minutes+'z');
						$("#fPanel4").text("User: "+tMyCall+" (" +tUserName+")");
						
		
				}
			)};
	
			function updateTimer()
			{
				if (waitRefresh>0)
				{
					waitRefresh=waitRefresh-1;
				}
					if(tTrx==0)
					{	   
						$.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:tMyCall }, function(response) 
						{
						var tAData=response.split('`');
	//tAData[0]="1240222000";
	//					console.log( "af: "+tAData[12]);
						tTrx=tAData[9];
						tPTT=tAData[7];
						if (tTrx==1){
							return;
						}
						if (waitRefresh==0){
							ptt1.setVisible(false);
							ptt.setVisible(true);
						}
						var cRX=ppanel.getByName("TR");
	//					cRX.setText("RCV");
						cRX.setNeedRepaint(true);
						cRX.refreshElement();
						if (tDisconnected==0){
							if (tAData[11]!=sliderRFRef && sliderRFGainOride!=0){
								var tP=tAData[11];
								if (tP<100 && tP>0) tP=tP-1;
								outputRF.innerHTML = tP;
								$('#sliderRF').slider('value',tAData[11]);
							};
							if (tAData[12]!=sliderAFRef && sliderAFGainOride!=0){
								tP=parseInt(tAData[12]);
								if (tP<100 && tP>0) tP=tP-1;
								outputAF.innerHTML = tP;
								$('#sliderAF').slider('value',tAData[12]);
							};
							if (tAData[13]!=sliderPwrOutRef && sliderPwrOutOride!=0){
								tP=parseInt(tAData[13]);
								if (tP<100 && tP>0) tP=tP-1;
								outputPwrOut.innerHTML = tP;
								$('#sliderPwrOut').slider('value',tAData[13]);
							};
							if (tAData[14]!=sliderMicRef && sliderMicLvlOride!=0){
								tP=parseInt(tAData[14]);
								if (tP<100 && tP>0) tP=tP-1;
								outputMic.innerHTML = tP;
								$('#sliderMic').slider('value',tAData[14]);
							};
						};
	//					tAData[0]="1228925888";
						var tAlive=tAData[8];  //to watch for no connection to radio
						tTrx=tAData[9];
						var tSlave=tAData[10];
						tSlave=dec_to_bho(tSlave, 'B');
						tSlave=padDigits(tSlave, 8);
						
						if (notGotPerfectWidgets==0){
							for (i=1;i<9;i++){
								var tL='R'+i+'O';
								var tLed=pled.getByName(tL);
								if (tSlave.substr(8-i, 1)==0){
									tLed.setVisible(true);
								}else{
									tLed.setVisible(false);
								}    
							}
						}
						var tRadioUpdate=tAData[8];
						if (tRadioUpdate.length!=0){
	/*						$("#modalA-body").html(tRadioUpdate);			  				
							$("#modalA-title").html("RigPi Report");
							  $("#myModalAlert").modal({show:true});
	*/					$.post("/programs/SetSettings.php", {field: "RadioData", radio: tMyRadio, data: "", table: "RadioInterface"});
						}
						var tNowDead=0;
						if (tAlive=='0'){
							tAliveCount=tAliveCount+1;
							if (tAliveCount>5){
								tNowDead=1;
								tAliveCount=6;
							}
						}else{
							tAliveCount=0;
						}
						var wWidth=$(window).width();
						var wHeight=$(window).height();
						var st=$(".status");
						if (!$.isNumeric(tAData[0] ) || parseInt(tAData[0])==0){
							if (wWidth<1000 && wHeight<600){
							   st.removeClass('d-none');
							}
							tNoRadio=true;
							tAData[0]="00000000";
							tAData[3]='';
							tAData[2]="00000000";
							tAData[4]=-54;
						}else{
							if (wWidth<1000 && wHeight<600){
							   st.addClass('d-none');
							}else{
							   st.removeClass('d-none');
							}
						}
						$.post("/programs/SetSettings.php", {field: "IsAlive", radio: tMyRadio, data: "0", table: "RadioInterface"});
						tMain=tAData[0];
						tSub=tAData[2];
	//					tMode=tAData[3];
						tPTT=tAData[7];
	//llllllllllllllllllllllll
						tBusy=tPTT;
						tMain=("0000000000" + tMain).slice(-10);
						var tF=addPeriods(tMain);
						if (notGotPerfectWidgets==1){
							return;
						}
						if (waitRefresh==0){
							var cMain=ppanel.getByName("Main");
							cMain.setText(tF);
							cMain.setNeedRepaint(true);
							cMain.refreshElement();
							var tSplit=tAData[1];
							if (tSplitDisabled==0){
								tSplitOn=tSplit;
							}else{
								$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'SplitOut'}, function(response) 
								{
									if (tSplitOn!=response){
										tSplitOn=response;
										toggleSplitButton(tSplitOn);
									};
								});
							}
						   var cSub=ppanel.getByName("Sub");
							var cSubIn=ppanel.getByName("SubInactive");
							var cFreq2s=("0000000000" + tSub).slice(-10);
							var tSub1=cFreq2s;
							cFreq2s=addPeriods(cFreq2s);
							cFreq2s1=cFreq2s.trim();
							if (tSplitOn==1){
								cSub.setVisible(true);
								cSubIn.setVisible(false);
								cSub.setText(cFreq2s);
							}else{
								cSub.setVisible(false);
								cSubIn.setVisible(true);
								cSubIn.setText(cFreq2s);
							}
							toggleSplitButton(tSplitOn);
							cSub.setNeedRepaint(true);						cSub.refreshElement();
						}
						var cMode=ppanel.getByName("Mode");
						if (tAData[3]=="PKTUSB"){
							tAData[3]="USB-D";
						}
							cMode.setText(tAData[3]);
							cMode.setNeedRepaint(true);
							cMode.refreshElement();
							tMode=tAData[3];
	//						waitRefresh=10;
						
						$("#fPanel4").text("User: "+tMyCall+" (" +tUserName+")");
						if (tAData[0]=="00000000"){
							tDisconnected=1;
							$("#fPanel1").html("&nbsp;No Radio");
							$("#fPanel2").text("");
							$("#fPanel3").text("");
							$('#fPanel1').attr('style', 'background-color:red');
							var sl=$('#spinner');
							  if (sl.hasClass('d-none')){
								  //not connecting
								  var pl=$('#play');
								  pl.removeClass('d-none');
								  var pl=$('#playOK');
								  pl.addClass('d-none');
							  }else{
								  //is connecting, no radio yet
								  var pl=$('#play');
								  pl.addClass('d-none');
								  var pl=$('#playOK');
								  pl.addClass('d-none');
							  }
						}else{
							tDisconnected=0;
							var sl=$('#spinner');
							  if (sl.hasClass('d-none')){
								  //not connecting
								  var pl=$('#play');
								  pl.addClass('d-none');
								  var pl=$('#playOK');
								  pl.removeClass('d-none');
							  }else{
								  //is connecting
								  var pl=$('#play');
								  pl.addClass('d-none');
								  var pl=$('#playOK');
								  pl.addClass('d-none');
							  }
							tF=tF.trim();
							if (notGotPerfectWidgets==1){
								return;
							}
							if (tF.length==8){
								tF=tF.substr(1);
								if (tF.substr(0,1)=="0"){
									tF=tF.substr(1);
								}
								if (cFreq2s1.substr(0,1)=="."){
									cFreq2s=cFreq2s1.substr(1);
								}
								$("#fPanel1").text("Main: "+tF+" kHz");
								if (tSplitOn==1){
									$("#fPanel2").text("Sub: "+cFreq2s+" kHz");
								}else{
									$("#fPanel2").text("");
								}
								
							}else{
								$("#fPanel1").text("Main: "+tF+" MHz");
								if (tSplitOn==1){
									$("#fPanel2").text("Sub: "+cFreq2s+" MHz");
								}else{
									$("#fPanel2").text("");
								}
							}
							$("#fPanel3").text("Mode: "+tAData[3]);
							$('#fPanel1').attr('style', 'background-color:black');
							tNoRadio=false;
						}
						
						if (trOn==1 && 	waitRefresh==0){
							tPTT=tAData[9];
							if (tPTT==1 && spacePTT==0){
								setPTT(1,0);
							}
						}
	
						//Start PTT = momentary ***					
						if (tPTTLatch==2 && waitRefresh==0){
							if (ptt.getPressed()==false){
								if (tBusy==1){
									var cRX=ppanel.getByName("TR");
									cRX.setText("XMIT");
									cRX.setNeedRepaint(true);
									cRX.refreshElement();
									ptt1.setVisible(true);
									ptt.setVisible(false);
									return;
								}else{
									var cRX=ppanel.getByName("TR");
									cRX.setText("RCV");
									cRX.setNeedRepaint(true);
									cRX.refreshElement();
									ptt1.setVisible(false);
									ptt.setVisible(true);
								}
							}else{
	//var xo=spacePTT==1;
								var xo=ptt.getPressed() || spacePTT==1;
								if (xo==false){
									setPTT(0, 0);
									tKnobPTT=0;
									tBusy=0;
								}else{
									setPTT(1, 0);
									tBusy=1;
									return;
								}
								
							}
						}
						//End PTT = momentary
						//Start PTT = latch
						if (tPTTLatch==1 && waitRefresh==0){
							if (tTrx==0){
								tPTT=tAData[9];  //mmmmmmmmmmmmmmmmmmm
								if (tPTT==1 || spacePTT==1){
	//								setPTT(0, 0);
								}
							}else{
	//							setPTT(1,1);
							}
						}
						//End PTT = latch
						if (tPTT==0 ){
							var cMeterVal=pmeter.getByName("Slider1");
							var mtr1=Number(tAData[4])+54;
							mtr=255/100 * mtr1;
							cMeterVal.setValue(mtr, true);
							var cBarVal=ppanel.getByName("LinearLevel1");
							mtr=Math.floor(mtr1/7);
							mtr=mtr*7;
							cBarVal.setValue(mtr, true);
	//						var cMeterLabel=pmeter.getByName("MtrFn");
	///						cMeterLabel.setText("S-Meter");
	//						cMeterLabel.setNeedRepaint(true);
	//						cMeterLabel.refreshElement();
						}
					});
				}else{
					$.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:tMyCall }, function(response) 
					{
						var tAData=response.split('`');
						tTrx=tAData[9];
						tPTT=tAData[7];
	console.log("ptt: "+tTrx);
						if (tTrx==0){
							tPTT=0;
							setPTT(0,0);
							return;
						}
						if (waitRefresh==0){
							ptt1.setVisible(true);
							ptt.setVisible(false);
						}
						if (showVideo==1 || showVideo==3 || showVideo==4){
							var cMeterVal=pmeter.getByName("Slider1");
							var mtr1=parseInt(100*tAData[4],10)/tMeterCal;
							mtr=mtr1;//using mtr direct provides more accurate calibration, limited testing
							cMeterVal.setValue(mtr, true);
							var cBarVal=ppanel.getByName("LinearLevel1");
							mtr=Math.floor(mtr1/7);
							mtr=mtr*7;
							cBarVal.setValue(mtr, true);
	//						var cMeterLabel=pmeter.getByName("MtrFn");
	//						cMeterLabel.setText(mtrLabel);
	//						cMeterLabel.setNeedRepaint(true);
	//						cMeterLabel.refreshElement();
						}
				//Start PTT=momentary in transmit5
				if (tPTTLatch==2 && waitRefresh==0){
					tBusy=tTrx;
	//				console.log("new " + tPTT);
					var tPr=ptt.getPressed();
	//				if (tPr==false){
	//					if (tBusy==1){
					if (tKnobPTT==0){
						if (tBusy==1){
							if (tKnobPTT==0 && trOn==0 && spacePTT==0){
	//							setPTT(1, 1);
							}else{
								setPTT(1,0);
							}
						}else{
							setPTT(0, 0);
						}
					}else{
						if (tTrx==1){
							var xo=ptt.getPressed() || spacePTT==1;
							if (xo==false){
								setPTT(0, 0);
				//							tKnobPTT=0;
								tBusy=0;
								$.post("/programs/SetSettings.php", {field: "PTTOut", radio: tMyRadio, data: "0", table: "RadioInterface"});
							}else{
								setPTT(1, 0);
								tBusy=1;
								$.post("/programs/SetSettings.php", {field: "PTTOut", radio: tMyRadio, data: "1", table: "RadioInterface"});
							}
						}
					}
				}
				//End PTT Momentary					
				//Start PTT Latch					
			if (tPTTLatch==1){
					if (tTrx==0 ){
						setPTT(0, 0);
					}else{
						setPTT(1, 1);
					}
					tBusy=tPTT;
				}
				//End PTT Latch
			});
		};
		if (notGotPerfectWidgets==1){
			return;
		}
		var now = new Date();
		var now_hours=now.getUTCHours();
		now_hours=("00" + now_hours).slice(-2);
		var now_minutes=now.getUTCMinutes();
		now_minutes=("00" + now_minutes).slice(-2);
		if (notGotPerfectWidgets==0){
			var timeUTC = ppanel.getByName("LocalTime");
			timeUTC.setText(now_hours+":"+now_minutes);
			timeUTC.setNeedRepaint(true);
			timeUTC.refreshElement();
		}
		$("#fPanel5").text(now_hours+":"+now_minutes+'z');
	};
	
	//};
			$.getScript("/js/addPeriods.js");
	
			function doDigit(whichIncrement, skipShift){
				if (notGotPerfectWidgets==1){
					return;
				}
				if (tNoRadio==true){
	//                    return;
				}
				if (whichIncrement==0){
					tLine1.setVisible(false);
					tLine2.setVisible(false);
					return;
				}
				if (whichIncrement!=curDigit){
					skipShift=1;
				}
				tTuneFromTap=1;
				if (skipShift==0){
					tuneMain(whichIncrement);
				}
				curDigit=whichIncrement;
				skipShift=0;
				tuningIncrement=Math.abs(whichIncrement);
				tLine1.setVisible(false);
				tLine2.setVisible(false);
				ld=ld4;
				lu=lu4;
				switch(tuningIncrement){
					case 1:
						ld=ld1;
						lu=lu1;
						$.post("/programs/SetSettings.php", {field: "MainSelect", radio: tMyRadio, data: 0, table: "MySettings"})
						break;
					case 10:
						ld=ld2;
						lu=lu2;
						$.post("/programs/SetSettings.php", {field: "MainSelect", radio: tMyRadio, data: 1, table: "MySettings"})
						break;
					case 100:
						ld=ld3;
						lu=lu3;
						$.post("/programs/SetSettings.php", {field: "MainSelect", radio: tMyRadio, data: 2, table: "MySettings"})
						break;
					case 1000:
						ld=ld4;
						lu=lu4;
						$.post("/programs/SetSettings.php", {field: "MainSelect", radio: tMyRadio, data: 3, table: "MySettings"})
						break;
					case 10000:
						ld=ld5;
						lu=lu5;
						$.post("/programs/SetSettings.php", {field: "MainSelect", radio: tMyRadio, data: 4, table: "MySettings"})
						break;
					case 100000:
						ld=ld6;
						lu=lu6;
						$.post("/programs/SetSettings.php", {field: "MainSelect", radio: tMyRadio, data: 5, table: "MySettings"})
						break;
					case 1000000:
						ld=ld7;
						lu=lu7;
						$.post("/programs/SetSettings.php", {field: "MainSelect", radio: tMyRadio, data: 6, table: "MySettings"})
						break;
					case 10000000:
						ld=ld8;
						lu=lu8;
						$.post("/programs/SetSettings.php", {field: "MainSelect", radio: tMyRadio, data: 7, table: "MySettings"})
						break;
					case 100000000:
						ld=ld9;
						lu=lu9;
						$.post("/programs/SetSettings.php", {field: "MainSelect", radio: tMyRadio, data: 8, table: "MySettings"})
						break;
					case 1000000000:
						ld=ld10;
						lu=lu10;
						$.post("/programs/SetSettings.php", {field: "MainSelect", radio: tMyRadio, data: 9, table: "MySettings"})
						break;
				}
				lu.setVisible(true);
				ld.setVisible(true);
				tLine1=ld;
				tLine2=lu;
			}
			
			function doSubDigit(whichIncrement, skipShift){
				if (notGotPerfectWidgets==1){
					return;
				}
				var pl=$('#play');
				if (!pl.hasClass('d-none')){
					return;
				}
				if (whichIncrement==0){
					stLine1.setVisible(false);
					stLine2.setVisible(false);
					return;
				}
				if (tSplitOn==0){
					return;
				}
				if (whichIncrement!=curSubDigit){
					skipShift=1;
				}
				tTuneFromTap=1;
				if (skipShift==0){
					tuneSplit(whichIncrement);
				}
				curSubDigit=whichIncrement;
				skipShift=0;
				tuningIncrement=Math.abs(whichIncrement);
				stLine1.setVisible(false);
				stLine2.setVisible(false);
				var sld;
				var slu;
				sld=sld5;
				slu=slu5;
				switch(tuningIncrement){
					case 1:
						sld=sld1;
						slu=slu1;
						break;
					case 10:
						sld=sld2;
						slu=slu2;
						break;
					case 100:
						sld=sld3;
						slu=slu3;
						break;
					case 1000:
						sld=sld4;
						slu=slu4;
						break;
					case 10000:
						sld=sld5;
						slu=slu5;
						break;
					case 100000:
						sld=sld6;
						slu=slu6;
						break;
					case 1000000:
						sld=sld7;
						slu=slu7;
						break;
					case 10000000:
						sld=sld8;
						slu=slu8;
						break;
					case 100000000:
						sld=sld9;
						slu=slu9;
						break;
					case 1000000000:
						sld=sld10;
						slu=slu10;
						break;
				}
				slu.setVisible(true);
				sld.setVisible(true);
				stLine1=sld;
				stLine2=slu;
			}
			
			function numTune(num)
			{
				if (notGotPerfectWidgets==1){
					return;
				}
				if (alreadyDone==0){
					tButtonWait=1;
					alreadyDone=1;
					if (tSplitOn==0){
						switch (tLine1){
						case ld1:
							tMain=tMain.substring(0, tMain.length-1)+ num;
							break;
						case ld2:
							tMain=tMain.substring(0, tMain.length-2)+ num + tMain.substring(tMain.length-1);
							doDigit(1,1);
							break;
						case ld3:
							tMain=tMain.substring(0, tMain.length-3)+ num + tMain.substring(tMain.length-2);
							doDigit(10,1);
							break;
						case ld4:
							tMain=tMain.substring(0, tMain.length-4)+ num + tMain.substring(tMain.length-3);
							doDigit(100,1);
							break;
						case ld5:
							tMain=tMain.substring(0, tMain.length-5)+ num + tMain.substring(tMain.length-4);
							doDigit(1000,1);
							break;
						case ld6:
							tMain=tMain.substring(0, tMain.length-6)+ num + tMain.substring(tMain.length-5);
							doDigit(10000,1);
							break;
						case ld7:
							tMain=tMain.substring(0, tMain.length-7)+ num + tMain.substring(tMain.length-6);
							doDigit(100000,1);
							break;
						case ld8:
							tMain=tMain.substring(0, tMain.length-8)+ num + tMain.substring(tMain.length-7);
							doDigit(1000000,1);
							break;
						case ld9:
							doDigit(10000000,1);
							tMain=tMain.substring(0, tMain.length-9)+ num + tMain.substring(tMain.length-8);
							break;
						case ld10:
							doDigit(100000000,1);
							tMain=tMain.substring(0, tMain.length-10)+ num + tMain.substring(tMain.length-9);
							break;
							
						}
						var cFreq2m=("0000000000" + tMain).slice(-10);
						var tMain1=addPeriods(cFreq2m);
						var cMain=ppanel.getByName("Main");
						cMain.setText(tMain1);
						cMain.setNeedRepaint(true);
						cMain.refreshElement();
						$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: tMain, table: "RadioInterface"}, function(response){
								tButtonWait=0;
							}
						 );
					}else{
						switch (stLine1){
							case sld1:
								tSub=tSub.substring(0, tSub.length-1)+ num;
								break;
							case sld2:
								tSub=tSub.substring(0, tSub.length-2)+ num + tSub.substring(tSub.length-1);
								doSubDigit(1,1);
								break;
							case sld3:
								tSub=tSub.substring(0, tSub.length-3)+ num + tSub.substring(tSub.length-2);
								doSubDigit(10,1);
								break;
							case sld4:
								tSub=tSub.substring(0, tSub.length-4)+ num + tSub.substring(tSub.length-3);
								doSubDigit(100,1);
								break;
							case sld5:
								tSub=tSub.substring(0, tSub.length-5)+ num + tSub.substring(tSub.length-4);
								doSubDigit(1000,1);
								break;
							case sld6:
								tSub=tSub.substring(0, tSub.length-6)+ num + tSub.substring(tSub.length-5);
								doSubDigit(10000,1);
								break;
							case sld7:
								tSub=tSub.substring(0, tSub.length-7)+ num + tSub.substring(tSub.length-6);
								doSubDigit(100000,1);
								break;
							case sld8:
								tSub=tSub.substring(0, tSub.length-8)+ num + tSub.substring(tSub.length-7);
								doSubDigit(1000000,1);
								break;
							case sld9:
								doSubDigit(10000000,1);
								tSub=tSub.substring(0, tSub.length-9)+ num + tSub.substring(tSub.length-8);
								break;
							case sld10:
								doSubDigit(100000000,1);
								tSub=tSub.substring(0, tSub.length-9)+ num + tSub.substring(tSub.length-9);
								break;
						}
						var cFreq2s=("0000000000" + tSub).slice(-10);
						var tSub1=addPeriods(cFreq2s);
						var cSub=ppanel.getByName("Sub");
						cSub.setText(tSub1);
						cSub.setNeedRepaint(true);
						cSub.refreshElement();
						$.post("/programs/SetSettings.php", {field: "SubOut", radio: tMyRadio, data: tSub, table: "RadioInterface"}, function(response){
								tButtonWait=0;
							}
						);
					}
				}
			};
				
		</script>
	
		</head>
		<body class="body-black" id="tuner">
			<?php require $dRoot . "/includes/header.php"; ?>
			 <div class="container-fluid">
				<p>
				<input class="textarea dummytext" type="tel" rows="4" id="dt">
				<div class="row">
				   <div class="col-sm-4 mx-auto" id="colPan">
						<div class="row fixed mx-auto noselect d-none"  style="margin-bottom:10px;" id="dPanel">
						</div>
						 <div class="row  fixed noselect mx-auto">
							<div class="col d-none embed-responsive-item" id="videoPanel">
								<img class="rigvid" src="" alt="RigVideo" style="margin-left: -17px; width:320px;height:240px;" id="i1">
							</div>
						</div>
						<div class="row" style="margin-top:10px">
							<div class="col-12" id="topVFO">
							</div>
						</div>
				   </div> 
				   <div class="col-sm-4 mx-auto fixed noselect " id="colVid">
						<div class="row">
							<div class="col mx-auto" >
								<div class="row center fixed noselect d-none" id="dMeter">
								</div>
								<div class="row  fixed mx-auto">
									<div class="col d-none embed-responsive-item" id="videoMeter">
										<img class="rigvid" src="" alt="RigVideo" style="margin-left:-10px;width:320px;height:240px;" id="i2">
									</div>
								</div>	
							</div>
						</div>
						<div class="row">
							<div class="col">
							   <div class="mycall text-white-medium d-none" style="margin-top:-60px">
									<?php echo $tCall; ?>
									<hr>
								 </div>
								<div class="row" style="margin-left:20px;margin-right:20px">
									<div class='col-6 btn-padding'>
										<button class="btn btn-color btn-sm btn-block" title="Change Bank" id="myBank1" type="button">
											MACRO BANK <u>1</u>
										</button>
									</div>
										<div class='col-6 btn-padding'>
										<button class="btn btn-color btn-sm btn-block" title="Change Bank" id="myBank2" type="button">
											MACRO BANK <u>2</u>
										</button>
									</div>
								</div>
								<div class="row"style="margin-left:20px;margin-right:20px">
									<div class='col-6 btn-padding'>
										<button class="btn btn-color btn-sm btn-block" title="Change Bank" id="myBank3" type="button">
											MACRO BANK <u>3</u>
										</button>
									</div>
									<div class='col-6 btn-padding'>
										<button class="btn btn-color btn-sm btn-block" title="Change Bank" id="myBank4" type="button">
											MACRO BANK <u>4</u>
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>   	
					<div class="col-sm-4 mx-auto" id="colKnob">
						<div class="row fixed mx-auto noselect d-none" style="margin-left:-10px;" id="dKnob"></div>
						<div class="row  fixed mx-auto">
							<div class="col d-none embed-responsive-item" id="videoKnob">
								<img class="rigvid" src="" alt="RigVideo" style="margin-left:-150px;width:320px;height:240px;" id="i3">
							</div>
						</div>	
						<div class="row fixed mx-auto noselect d-none" id="dLED"></div>
					</div>
				</div>
				<div class="row" style="margin-top:10px">
					<div class="col-12" id="bottomVFO">
					</div>
				</div>
				<hr>
			   <div id="macroDiv">
					<?php require $dRoot . "/includes/macroButtons.php"; ?>
			   </div>
				 <div class="status">
					<?php require $dRoot . "/includes/footer.php"; ?>
				</div>
			</div>
		</body>
		<?php require $dRoot . "/includes/modalxxx.txt"; ?>
	   <?php require $dRoot . "/includes/modal.txt"; ?>
		<?php require $dRoot . "/includes/modalAlert.txt"; ?>
		<?php require $dRoot . "/includes/modalCancelAlert.txt"; ?>
		<?php require $dRoot . "/includes/modalCancelReboot.txt"; ?>
		<?php require $dRoot . "/includes/modalUpdate.txt"; ?>
		<?php require $dRoot . "/includes/modalCancelOnly.txt"; ?>
			<script src="/js/mscorlib.js" type="text/javascript"></script> 
			<script src="/js/PerfectWidgets.js" type="text/javascript"></script>
		<script src="/Bootstrap/jquery-ui.js"></script>
	 <script src="/js/jquery.ui.touch-punch.min.js"></script>   
	<script src="./Bootstrap/popper.min.js"</script>
		<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
		<script src="./Bootstrap/jquery-ui.js"></script>
		<script src="./Bootstrap/bootstrap.min.js"></script>
			<script src="/js/nav-active.js"></script>
	</html>
	