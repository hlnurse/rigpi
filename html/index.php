<?php
/*
 * RigPi Tuner
 * Copyright (c) 2025 Howard Nurse, W6HN
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
 */
session_start();
$dRoot = "/var/www/html";
require $dRoot . "/programs/GetMyRadioFunc.php";
require_once $dRoot . "/programs/GetUserFieldFunc.php";
require_once $dRoot . "/classes/Membership.php";
$membership = new Membership();
if (isset($_SESSION["myUsername"])) {
    $tUserName = $_SESSION["myUsername"];
    $tCall = $_SESSION["myCall"];
    $membership->confirm_Member($tUserName);
} else {
    header("Location: /login.php");
    session_reset();
    exit();
}
$tMyPort = $_SESSION["myPort"];
$tMyRadio = $_SESSION["myRadio"];
if (!empty($_SESSION["firstuse"])) {
    $firstUse = $_SESSION["firstUse"];
} else {
    $firstUse = 1;
}
$theme = getUserField($tUserName, "Theme");
$_SESSION["firstUse"] = 0;
if ($theme == 0) {
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
$level = "11";
if ($row) {
    $level = $row["Access_Level"];
    $tUDPPort = $row["WSJTXPort"];
}
$db->where("IsAlive", "1");
$rows = $db->get("RadioInterface");
$online = $db->count;
?>

	<!--This is the Tuner HTML window -->

	<!DOCTYPE html>
	<html lang="en">
		<head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
			<title><?php echo $tCall; ?> RigPi Tuner <?php echo " for " .
     $tUserName; ?></title>
			<meta name="description" content="RigPi Tuner">
			<meta name="author" content="Howard Nurse, W6HN">
			<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
			<script src="/Bootstrap/jquery.min.js" ></script>
			<link rel="stylesheet" href="/Bootstrap/bootstrap.min.css">
			<link rel="shortcut icon" href="/favicon.ico">
			<link rel="apple-touch-icon" href="/favicon.ico">
			<?php require $dRoot . "/includes/styles.php"; ?>
			<link href="/awe/css/all.css" rel="stylesheet">
			<link href="/awe/css/fontawesome.css" rel="stylesheet">
			<link href="/awe/css/solid.css" rel="stylesheet">
			<link rel="stylesheet" href="/Bootstrap/jquery-ui.css">
			<script src="/Bootstrap/popper.min.js"</script>

			<script src="/Bootstrap/bootstrap.min.js"></script>


		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<script type="text/javascript">
			var resetTimer, spaceUp, oldState;
			var tRotateButton;
			var tRotateButtonCommand;
			var tRotateButtonLabel;
			var tRotateButtonAngle;
			var pknob, dec_to_bho;
			var ppanel;
			var pmeter;
			var lowerText;
			var pled;
			var swiper;
			var knobOld, tBandsAvail;
			var plus;
			var ptt;
			var keypad;
			var tBusy;
			var spacePTT=0;
			var tPTT=0, tPTTIsOn=0;
			var tKnobPTT=0;
			var tSwipeOld=0;
			var xmit=false;
			var trXmit=true;
			var tuningIncrement=100;
			var windowLoaded=false;
			var tUpdate;
			var mp1, tdx="";
			var mp100;
			var waitRefresh=0;
			var tMyRadio='1';
			var tMyRadio='1';
			var tCWPort="/dev/ttyS0";
			var tMyRotorPort="";
			var tCurBeam=0;
			let tUserName="<?php echo $tUserName; ?>";
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
			var tMode='FM';
			var tModeOld='xyz';
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
			var speedPot=0, speedPotEnable=0;
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
			var tRadioPort='0.0.0.0:4532';
			var si;
			var tDisconnected=1;
			var tMyCWPort;
			var tButtonWait=0;
			var tMyRotorRadio; //ve9gj
			var led1,led2,led3,led4,led5,led6,led7,led8;
			var mBank=1;
			var showVideo=2;
			var jsonPanel='';
			var jsonSMeter='';
			var wanIP, tPC, tID;
			var tAz=0;
			var tBand=20;
			var tBandOld='22';
			var tBandMHz="14MHz";
			var tBandWidth=3000;
			var tKnobLock=0, tUDPPort=2333;
			var doUSBUpdate=1; //used to update label on first pass
			var mtrLabel="S-Meter", AFGainOld=0, USBAFGain, USBAFGain1, USBAFGainOld=0;
			var checkPTT=0;  //used to prevent repetitive PTT commands
			var outputAF, sliderAF, outputPwrOut, sliderPwrOut, sliderMic, outputMic, outputRF, sliderRF, tVal;
			var sliderAFGainOride, sliderRFGainOride, sliderPwrOutOride, sliderMicLvlOride;
			var sliderAFRef, sliderRFRef, sliderPwrOutRef, sliderMicRef, sliderHandle;
			var latchBtn1=[],latchBtn2=[],latchBtn3=[],latchBtn4=[],latchBtn=[], bntLatchColor;
			var bEnable=[],bModeEnable=[],aMacros=[], bk, tMainSelect, tSpeed, tSpeedOriginal, tAData, speedLock=0;
			var tNum=0,tPre,tPost, lt1, transRadioID, transRadioName;
			var supportsUSB_AF=0, tMyTCPPort=0;
			var startConnection=0, startCounter=0, tMyIP="", tM1;
			let modeList;
			$(document).ready(function()
			{
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
		}else{
			$(bName).removeClass("btn-secondary");
			$(bName).removeClass("btn-mode-sel");
		};
	};

};
				function updateModeSelection(){
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
						var tB2=bName.substring(1);
//						console.log("bName: " + bName + " " + tB2);
						var tB=document.getElementById(tB2);
						if (tB.classList.contains("btn-mode-sel")){
							$(bName).removeClass("btn-mode-sel");
						}
					};
					waitRefresh=4;
				};
	function setTopBand(tText){
	if (notGotPerfectWidgets==1){
		return;
	}
	band2.setText(tText);
	band2.setNeedRepaint(true);
	band2.refreshElement();
//	waitRefresh=4;
}

function setMiddleBand(tText){
	if (notGotPerfectWidgets==1){
		return;
	}
	band1.setText(tText);
	band1.setNeedRepaint(true);
	band1.refreshElement();
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
				function getBandMemory(nBand)
{
	waitRefresh=6;
	var pl=$('#play');
	if (pl.hasClass('d-none')){
		if (tDisconnected==1){
			$(".modal-body").html("<br>&nbsp;&nbsp;The radio is not connected.<p><p>");
			$(".modalA-title").html("Radio Connection");
			  $("#myModalAlert").modal('show');
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
		tM1=tModeOld.toLowerCase();
		if (tMain.indexOf('UNK')==-1  &&
			tMain.indexOf('0000000000')==-1  &&
			tSub.indexOf('UNK')==-1 &&
			tM1.indexOf('UNK')==-1)
			{
			 $.post("/programs/SetFrequencyMem.php", {radio: tMyRadio, main: tMain, sub: tSub, mode: tM1, bw: -1}, function(response){

				$.post('/programs/GetFrequencyMem.php',{radio: tMyRadio, band: qBand}, function(response)
				{
					var obj = JSON.parse(response);
					var tF=obj[0];
					var tM=obj[1];
					var tB=obj[2];
					var tM2=tM.toLowerCase();
					tBa=GetBandFromFrequency(tMain);
					tBa1=GetBandFromFrequency(tF);
					updateModeSelection();
					updateButtons();
					$('#' + tBa1+ 'Button').addClass('btn-mode-sel');
					$('#' + tM2 + 'Button').addClass('btn-mode-sel');

					$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: tF, table: "RadioInterface"}, function(response){
						var t =response;
						$.post("/programs/SetSettings.php", {field: "SubOut", radio: tMyRadio, data: tF, table: "RadioInterface"}, function(response){
							t = response;
							$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: tM2.toUpperCase(), table: "RadioInterface"}, function(response){
								t = response;
								$.post("/programs/SetSettings.php", {field: "BWOut", radio: tMyRadio, data: tB, table: "RadioInterface"}, function(response){
									t = response;
								});
							});
						});
					});
				});
			 });
		 };
	 }
}
				btnLatchColor="btn-warning";
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
								$(".modalA-title").html("No Internet");
								$("#modalA-body").html("<br>RSS does not find an Internet connection. <p><p>"+
									"Certain functions such as Spots, QRZ Call Lookup, Version updating, and "+
									"Maps will not function without a connection. (Try refreshing the Tuner page.)<p>");
								  $("#myModalAlert").modal('show');
							}else{
						};
					});
				}

					$(document).on('click', '#searchButton', function()
					{
						tUserName="<?php echo $tUserName; ?>";
						tdx=$('#searchText').val().toUpperCase();
						if (tdx.length==0 || tdx.indexOf('*')>-1){
							return;
						}
						$.post('/programs/GetUserField.php',{un: tUserName, field: 'uID'}, function(response) {
							tUser=response;
							$.post("/programs/GetCallbook.php", {call: tdx, what: 'QRZData', user: tUser, un: tUserName},function(response){
								$(".modal-body").html(response);
							$.post("/programs/GetCallbook.php", {call: tdx, what: 'QRZpix', user: tUser, un: tUserName},function(response){
							$.post("/programs/SetSettings.php", {field: "waitReset", radio: tMyRadio, data: 1, table: "RadioInterface"}, function (response1){
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
								  $(".modal-pix").attr("src",'');
								}
								$('.modal-title').html(tdx);
								$('#myModal').modal('show');
								$.post("/programs/SetSettings.php", {field: "waitReset", radio: tMyRadio, data: 1, table: "RadioInterface"});
								});
							});
							$.post("/programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tdx, table: "MySettings"});
						})
					});
				});

				function updateBank(which){
					if ($(window).width()<435){
						$('#myBank1').text("BANK 1");
						$('#myBank2').text("BANK 2");
						$('#myBank3').text("BANK 3");
						$('#myBank4').text("BANK 4");
					}
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


			$.post('/programs/GetSelectedRadio.php', {un:tUserName}, function(response)
			{
				$.get('/programs/GetMyRadio.php', 'f=Port&r='+response, function(response1) {
					tMyRadio=response;
					tMyTCPPort=30000 + parseInt(tMyRadio);
					$.post("/programs/SetSettings.php", {field: "waitReset", radio: tMyRadio, data: 0, table: "RadioInterface"});

					  tRadioPort=response1;
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MacroBankTuner', table: 'RadioInterface'}, function(response){
							mBank=response
							$('#myBank').text("Macro Bank: "+response);
							loadMacroBank(mBank);
							updateBank(mBank);
					})

				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'USBAFGain', table: 'RadioInterface'}, function(response){
					USBAFGain=response;
					if (response>0){
						USBAFGainOld=response;
						var tM = USBAFGainOld;
						$.post("/programs/SetSettings.php", {field: "USBAFGainOld", radio: tMyRadio, data: tM, table: "RadioInterface"}, function(response){	});
						doUSBUpdate=1;
					};
				});
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKSpeed', table: 'Keyer'}, function(response){
					  tSpeed=response;
				  });
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKSpeed', table: 'Keyer'}, function(response){
						tSpeedOriginal=response;
						updateButtonLabels('/OLD', tSpeedOriginal);
					});

				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'LatchBtn1', table: 'RadioInterface'}, function(response){
					if (response==""){
						response="?,".repeat(32);
						$.post("/programs/SetSettings.php", {field: 'LatchBtn1', radio: tMyRadio, data: response, table: "RadioInterface"}, function(response){
						});
					}
					latchBtn1=response.split(",");
				});
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'LatchBtn2', table: 'RadioInterface'}, function(response){
					if (response==""){
						response="?,".repeat(32);
						$.post("/programs/SetSettings.php", {field: 'LatchBtn2', radio: tMyRadio, data: response, table: "RadioInterface"}, function(response){
						});
					}
					latchBtn2=response.split(",");
				});
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'LatchBtn3', table: 'RadioInterface'}, function(response){
					if (response==""){
						response="?,".repeat(32);
						$.post("/programs/SetSettings.php", {field: 'LatchBtn3', radio: tMyRadio, data: response, table: "RadioInterface"}, function(response){
						});

					}
					latchBtn3=response.split(",");
				});
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: "LatchBtn4", table: "RadioInterface"}, function(response){
					if (response==""){
						response="?,".repeat(32);
						$.post("/programs/SetSettings.php", {field: 'LatchBtn4', radio: tMyRadio, data: response, table: "RadioInterface"}, function(response){
						});

					}
					latchBtn4=response.split(",");
				});


				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'AFGainOride', table: 'RadioInterface'}, function(response){
					sliderAFGainOride=response;
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'AFGain', table: 'RadioInterface'}, function(response){
						$( function() {
							$("#sliderAF").slider({
								min: 1,
								max: sliderAFGainOride,
								range: 'min'
							});
							outputAF.innerHTML = response;
							updateButtonLabels('RADIO AF MUTE', response-1);
							$("#sliderAF").slider('value',response);
					});

					$("#sliderAF").on("slidechange",function(event){
						tOverPanel=false;
						var a = $("#sliderAF").slider('value');
						tVal=a;
						if (tVal!=sliderAFRef){
							sliderAFRef=tVal;
							waitRefresh=8;
							var tV=tVal;
							if (tV>0 && tV<100) tV=tV-1;
							outputAF.innerHTML =tV;
							updateButtonLabels('RADIO AF MUTE', tV);
							$.post("/programs/SetSettings.php", {field: "AFGain", radio: tMyRadio, data: tVal, table: "RadioInterface"}, function(response){
							});
						};
						});
				});


				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'RFGainOride', table: 'RadioInterface'}, function(response){
					sliderRFGainOride=response;
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'RFGain', table: 'RadioInterface'}, function(response){
						$( function() {
							$("#sliderRF").slider({
								min: 1,
								max: sliderRFGainOride,
								range: 'min'
						});
						outputRF.innerHTML = response;
						$("#sliderRF").slider('value',response);
						});
					});

					$("#sliderRF").on("slidechange",function(event){
						tVal=$("#sliderRF").slider('value');
						if (tVal<0) tVal=0;
						if (tVal!=sliderRFRef){
							waitRefresh=8;
							sliderRFRef=tVal;
							var tV=tVal;
							if (tV>0 && tV<100) tV=tV-1;
							outputRF.innerHTML =tV;
							$.post("/programs/SetSettings.php", {field: "RFGain", radio: tMyRadio, data: tVal, table: "RadioInterface"}, function(response){
							});
						};
					});
				});

				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'PwrOutOride', table: 'RadioInterface'}, function(response){
					sliderPwrOutOride=response;

					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'PwrOut', table: 'RadioInterface'}, function(response){
						$( function() {
							$("#sliderPwrOut").slider({
								min: 1,
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
							waitRefresh=8;
							sliderPwrOutRef=tVal;
							sliderHandle=ui.handle;
							var tV=tVal;
							if (tV>0 && tV<100) tV=tV-1;
							outputPwrOut.innerHTML =tV;
							$.post("/programs/SetSettings.php", {field: "PwrOut", radio: tMyRadio, data: tVal, table: "RadioInterface"}, function(response){
							});
						};
					});
				});

				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MicLvlOride', table: 'RadioInterface'}, function(response){
					sliderMicLvlOride=response;
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MicLvl', table: 'RadioInterface'}, function(response){
						$( function() {
							$("#sliderMic").slider({
								min: 1,
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
				});
			});
			$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'PowerControl', table: 'MySettings'}, function(response){
				tPC=response;
			});
			var mtrCalField="";
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'TransmitLevel', table: 'MySettings'}, function(response){
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
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: mtrCalField, table: 'RadioInterface'}, function(response)
					{
						tMeterCal=response;
					});
				});
				$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKPot'}, function(response) {
					speedPot=response;
					$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKSpeed'}, function(response) {
						tSpeed=response;
						updateButtonLabels("/OLD", tSpeedOriginal);
						$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tSpeed, table: "Keyer"});
						$.post("/programs/SetSettings.php", {field: "CWChangeCk", radio: tMyRadio, data: 1, table: "RadioInterface"});
						waitRefresh=2;
					});
				});

				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'RadioName', table: 'MySettings'}, function(response)
				{
					tRadioName=response;
					transRadioName=tRadioName;
					$.post("/programs/RadioID.php", {tRadio: tRadioName}, function(response1) {
						if (response1.length>0){
							transRadioID=response1;
						}
					setMiddleBand(response);
					$.post('/programs/GetRadioCaps.php', {a: transRadioName, r: transRadioID, q:'radio'}, function(response) {
					  var caps=response;
					  tBandsAvail=0;
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



					  }
				  });
				});
		function changeVideoSource(sourceUrl) {
		  // Pause the video if it's currently playing
		  videoPlayer.pause();
		  // Clear the existing source
		  videoPlayer.innerHTML = '';
		  // Create a n source element
		  const sourceElement = document.createElement('source');
		  sourceElement.setAttribute('src', sourceUrl);
		  // Add the source element to the video player
		  videoPlayer.appendChild(sourceElement);
		  // Load and play the new video source
		  videoPlayer.load();
		  videoPlayer.play();
		}


		$.post("/programs/GetInfo.php", {what: 'IPAdr'}, function(response){
			var d = new Date();
			var n = d.getTime();
			var aData=response.split('+');
			tMyIP=aData[0];
			wanIP="http://"+aData[2]+":8081?"+n;
			lanIP="http://"+aData[0]+":8081?"+n;
			$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'ShowVideo', table: 'MySettings'}, function(response)
			{
				showVideo=response;
				var wIP=aData[3];
				if (showVideo>1){
					if ($(window).width()<835){  //phones
						if (wIP==aData[2]){
							if (showVideo==2){
								$('#i3').attr('src', "");
								$('#i2').attr('src', lanIP);
								$('#i1').attr('src', "");
							}else if (showVideo==3) {
								$('#i1').attr('src', lanIP);
								$('#i2').attr('src', "");
								$('#i3').attr('src', "");
							}else{
								$('#i1').attr('src',"");
								$('#i2').attr('src', "");
								$('#i3').attr('src', lanIP);
							}
						}else{
							if (showVideo==2){
								$('#i3').attr('src', "");
								$('#i2').attr('src', wanIP);
								$('#i1').attr('src', "");
							}else if (showVideo==3) {
								$('#i1').attr('src', wanIP);
								$('#i2').attr('src', "");
								$('#i3').attr('src', "");
							}else{
								$('#i1').attr('src',"");
								$('#i2').attr('src', "");
								$('#i3').attr('src', wanIP);
							}
						}
					}else{ //normal screens
						if (wIP==aData[2]){
							if (showVideo==2){
								$('#i3').attr('src', "");
								$('#i2').attr('src', lanIP);
								$('#i1').attr('src', "");
							}else if (showVideo==3) {
								$('#i1').attr('src', lanIP);
								$('#i2').attr('src', "");
								$('#i3').attr('src', "");
							}else{
								$('#i1').attr('src',"");
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

//Main buttons
			if (notGotPerfectWidgets==0){
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
				sup1000 = ppanel.getByName("sup1000");

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
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MainSelect', table: 'MySettings'}, function(response){
					tMainSelect=response;
					var tSel='0';
					tSelM='1' + tSel.repeat(tMainSelect);
					//var tSelM=tMainSelect;
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
				var rec = document.getElementById('dKnob').getBoundingClientRect();
				var position=rec.top + window.scrollY - window.pageYOffset;
				if (tDisconnected==0 && position>50)  //last && is to prevent PTT whene under hamburger menu on phones
				{
					if (xmit==true){
						setPTT(0,0);
						tKnobPTT=0;

//						console.log("PTT=0");
					}else{
						setPTT(1,0);
						tKnobPTT=1;
//						console.log("PTT=1");
					};
				};
			});

			$.post("/includes/vfoButtons.php", function(response){
				var bVFO=response;
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'ShowVideo', table: 'MySettings'}, function(response)
				{
					showVideo=response;
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
							$(".videoPanel").addClass('d-none');
							$(".videoMeter").addClass('d-none');
							$(".videoKnob").addClass('d-none');
							$("#dMeter").addClass('d-none');
							$("#dPanel").removeClass('d-none');
							$("#dKnob").removeClass('d-none');
							$(".mycall").addClass('d-none');
							$("#lowerText").removeClass('d-none');
						}else if (showVideo==2){ //meter
							$(".videoPanel").addClass('d-none');
							$(".videoMeter").removeClass('d-none');
							$(".videoKnob").addClass('d-none');
							$("#dPanel").removeClass('d-none');
							$("#dMeter").addClass('d-none');
							$("#dKnob").removeClass('d-none');
							$("#lowerText").removeClass('d-none');
							$(".mycall").addClass('d-none');
						}else if (showVideo==3){ //panel
							$(".videoPanel").removeClass('d-none');
							$(".videoMeter").addClass('d-none');
							$(".videoKnob").addClass('d-none');
							$("#dPanel").addClass('d-none');
							$("#dMeter").addClass('d-none');
							$("#dKnob").removeClass('d-none');
							$("#lowerText").removeClass('d-none');
							$(".mycall").addClass('d-none');
						}else if (showVideo==4){ //knob
							$(".videoPanel").addClass('d-none');
							$(".videoMeter").addClass('d-none');
							$(".videoKnob").removeClass('d-none');
							$("#dPanel").removeClass('d-none');
							$("#dMeter").addClass('d-none');
							$("#dKnob").addClass('d-none');
							$("#lowerText").removeClass('d-none');
							$(".mycall").addClass('d-none');
						};
					}else{ //1-0 digits will show, large format
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
							$(".videoPanel").addClass('d-none');
							$(".videoMeter").addClass('d-none');
							$(".videoKnob").addClass('d-none');
							$("#dMeter").removeClass('d-none');
							$("#dPanel").removeClass('d-none');
							$("#dKnob").removeClass('d-none');
							$(".mycall").removeClass('d-none');
							$("#lowerText").removeClass('d-none');
						}else if (showVideo==2){ //meter
							$(".videoPanel").addClass('d-none');
							$(".videoMeter").removeClass('d-none');
							$(".videoKnob").addClass('d-none');
							$("#dPanel").removeClass('d-none');
							$("#dMeter").addClass('d-none');
							$("#dKnob").removeClass('d-none');
							$("#lowerText").removeClass('d-none');
							$(".mycall").addClass('d-none');
						}else if (showVideo==3){ //panel same as meter
							$(".videoPanel").removeClass('d-none');
							$(".videoMeter").addClass('d-none');
							$(".videoKnob").addClass('d-none');
							$("#dPanel").addClass('d-none');
							$("#dMeter").removeClass('d-none');
							$("#dKnob").removeClass('d-none');
							$("#lowerText").removeClass('d-none');
							$(".mycall").removeClass('d-none');
						}else if (showVideo==4){ //knob
							$(".videoPanel").addClass('d-none');
							$(".videoMeter").addClass('d-none');
							$(".videoKnob").removeClass('d-none');
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

	function updateModeButtons(){
			$.post('/programs/RadioID.php', {tRadio: transRadioName}, function(response) {
			if (response.length>0){
				transRadioID=response;
			}
			$.post("/programs/getRigCaps.php", {myRadioName: transRadioName, myRadio: transRadioID, cap: "Mode list:"}, function(response){
				modeList=response;
			var bName="";
			for (i=0;i<9;i++){
				switch (i){
					case 0:
						bName="#lsbButton";
						if (modeList.indexOf('LSB')<0){
							bModeEnable[i]="0";
						}
						break;
					case 1:
						bName="#usbButton";
						if (modeList.indexOf('USB')<0){
							bModeEnable[i]="0";
						}
						break;
					case 2:
						bName="#usbdButton";
						if (modeList.indexOf('USB-D')<0){
							bModeEnable[i]="0";
						}
						break;
					case 3:
						bName="#cwButton";
						if (modeList.indexOf('CW')<0){
							bModeEnable[i]="0";
						}
						break;
					case 4:
						bName="#cwrButton";
						if (modeList.indexOf('CWR')<0){
							bModeEnable[i]="0";
						}
						break;
					case 5:
						bName="#rttyButton";
						if (modeList.indexOf('RTTY')<0){
							bModeEnable[i]="0";
						}
						break;
					case 6:
						bName="#fmButton";
						if (modeList.indexOf('FM')<0){
							bModeEnable[i]="0";
						}
						break;
					case 7:
						bName="#amButton";
						if (modeList.indexOf('AM')<0){
							bModeEnable[i]="0";
						}
						break;
					case 8:
						bName="#rttyrButton";
						if (modeList.indexOf('RTTYR')<0){
							bModeEnable[i]="0";
						}
						break;
				};
				bName1=tModeOld.toLowerCase() ;
				bName1='#' + bName1 + 'Button';
				$(bName1).removeClass("btn-mode-sel");

				if (bModeEnable[i]=="0") {
					$(bName).removeClass("btn-info");
					$(bName).addClass("btn-secondary");
				}else{
					$(bName).removeClass("btn-secondary");
					$(bName).addClass("btn-info");
				};
			};
		});
	});
	};

	function meterTimer(tr){

		if (tr==0 ){
			$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'SMeterIn'}, function(response) {
				var cMeterVal=pmeter.getByName("Slider1");
				if (response<46){
					var mtr1=Number(parseInt(response)+54);
					mtr=255/100 * mtr1;
					cMeterVal.setValue(mtr, true);
					var cBarVal=ppanel.getByName("LinearLevel1");
					mtr=Math.floor(mtr1/7);
					mtr=mtr*7;
					cBarVal.setValue(mtr, true);
				}
			});
		}else{
			$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'txMeterIn'}, function(response) {
				if (response>0){
					var cMeterVal=pmeter.getByName("Slider1");
					var mtr1=parseInt(100*response,10)/tMeterCal;
					if (mtr1 < 0){
						console.log('oops")');
						return;
					}
					mtr=mtr1;//using mtr direct provides more accurate calibration, limited testing
					cMeterVal.setValue(mtr, true);
					console.log("MTR: " + mtr);
					var cBarVal=ppanel.getByName("LinearLevel1");
					mtr=Math.floor(mtr1/7);
					mtr=mtr*7;
					cBarVal.setValue(mtr, true);
				}
			});
		}
	}
//	}
	function updateTimer()
	{
		if (waitRefresh>0)
		{
			console.log( waitRefresh);
			waitRefresh=waitRefresh-1;
			return;
		}
		var tTrx1=10;
			if(tTrx==0)
			{

//			console.log(tTrx + " " +  trXmit);

				if (tTrx==1 && trXmit==true){
					setPTT(1, 0);
				}
				$.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:tMyCall }, function(response)
				{
				if (response.indexOf("NG")>-1){
					return;
				}

				var tAData=response.split('`');
				if (startConnection==1){ //this allows several cycles of timer to test for connection
					if (tAData[0]=="OFF" && startCounter>0){
						startCounter=startCounter-1;
						return true;
					}
					checkConnection(tAData[0]);
					startConnection=0;
				}
				tTrx=tAData[9];

				tPTT=tAData[7];
				var tBW=tAData[17];
				tMode=tAData[3];
				console.log("MODE: " + tMode);
				tMain=tAData[0];
				var cRX=ppanel.getByName("TR");
				cRX.setNeedRepaint(true);
				cRX.refreshElement();
				if (tDisconnected==0){
					if (tBW!==tBandWidth){
						tBandWidth=tBW;
					}
					if (tAData[11]!=sliderRFRef){
						var tP=tAData[11];
						if (tP<100 && tP>0) tP=tP-1;
						outputRF.innerHTML = tP;
						$('#sliderRF').slider('value',tAData[11]);
					};
					if (tAData[12]!=sliderAFRef){
						sliderAFRef=tAData[12];
						var tP=parseInt(tAData[12]);
						if (tP<100 && tP>0) tP=tP-1;
						outputAF.innerHTML = tP;
						$('#sliderAF').slider('value',tAData[12]);
						if (tAData[12]>1){
							updateButtonLabels('RADIO AF MUTE',tAData[12]-1);
						}
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
					if (tAData[15]!=(USBAFGain+1) || doUSBUpdate==1){
						doUSBUpdate=0;
						USBAFGain1=tAData[15]-1;
						if (USBAFGain1>0){
							USBAFGainOld=USBAFGain1;
							USBAFGain=USBAFGain1;
							var tM=USBAFGain1;
							updateButtonLabels('RADIO USB MUTE', tM);
						}

						if (USBAFGain==0){
							USBAFGain1=USBAFGain1;
						}
						if (tAData[15]>0){
							$.post("/programs/SetSettings.php", {field: "USBAFGainOld", radio: tMyRadio, data: USBAFGain1, table: "RadioInterface"}, function(response){
							});

						};
					}
				};
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
			$.post("/programs/SetSettings.php", {field: "RadioData", radio: tMyRadio, data: "", table: "RadioInterface"});
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
				$.post("/programs/SetSettings.php", {field: "IsAlive", radio: tMyRadio, data: "1", table: "RadioInterface"});
				tMain=tAData[0];
				tSub=tAData[2];
				tPTT=tAData[7];
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
						toggleSplitButton(tSplitOn, 1);
					}else{
						$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'SplitOut'}, function(response)
						{
							if (tSplitOn!=response){
								tSplitOn=response;
								toggleSplitButton(tSplitOn, 1);
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
/*												if (tSplitOn!=tSplit){
						tSplitOn=response
						toggleSplitButton(tSplitOn);
						cSub.setNeedRepaint(true);
						cSub.refreshElement();
					}
*/											}
				var cMode=ppanel.getByName("Mode");
				if (tMode=="PKTUSB"){
					tMode="USB-D";
				}
				if (tMode=="PKTLSB"){
					tMode="LSB-D";
				}
//						console.log("Mode: " + tModeOld + " radio: " + tMode);
				if (tModeOld !== tMode){
					tModeOld=tMode;
					setBandMemory();
					console.log("here goes "+typeof tMode, tMode);

					var tNew=tMode.toLowerCase();
					if (tNew=='usb-d'){tNew='usbd'};
					updateModeSelection();
					var tB='#'+tNew+'Button';
					$(tB).addClass('btn-mode-sel');
				}
				if (tBandOld!==tAData[5] && tAData[5]!=='UNK'){
//						console.log("Band: " + tBandOld + " radio: " + tAData[5]);
					tBandOld=tAData[5];
					setBandMemory();
					tBand=tAData[5];
					var tMo=tBandOld;
					var tNew=tBand;
					updateButtons();
					updateModeSelection();
					$('#' + tNew + 'Button').addClass('btn-mode-sel');
				}

				cMode.setText(tMode);
				cMode.setNeedRepaint(true);
				cMode.refreshElement();
				$("#fPanel4").text("User: "+tMyCall+" (" +tUserName+")");
				if (tAData[0]=="00000000"){
//						if (tDisconnectedTest==0){
						tDisconnected=1;
						$tDisconnectedTest=1;
						$("#fPanel1").html("&nbsp;No Radio");
						$("#fPanel2").text("");
						$("#fPanel3").text("");
						$('#fPanel1').attr('style', 'background-color:red');
						var sl=$('#spinner');
						if (sl.hasClass('d-none')){
							  //not connecting
							  var pl=$('#play');
							  pl.removeClass('d-none');
							pl=$('#playOK');
							pl.addClass('d-none');
						  }else{
							  //is connecting, no radio yet
							  var pl=$('#play');
							pl.addClass('d-none');
							  pl=$('#playOK');
							  pl.addClass('d-none');
						  };
//						  };
				}else{
					tDisconnected=0;
					tDisconnectedTest=0;
					var sl=$('#spinner');
					pl=$('#playOK');
					pl.removeClass('d-none');
					pl=$('#play');
					pl.addClass('d-none');

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
					$("#fPanel3").text("Mode: "+tMode+ " - BW: " + tBandWidth);
					$('#fPanel1').attr('style', 'background-color:black');
					tNoRadio=false;
				};

				if (trOn==1 && 	waitRefresh==0){
					tPTT=tAData[9];
					if (tPTT==1 && spacePTT==0){
						setPTT(1,0);
					}
				}

			});
			meterTimer(tBusy);
		}else{  //in transmit
			$.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:tMyCall }, function(response)
			{
				console.log("xmit response " + response);
				tAData=response.split('`');
				if (tAData[0]==""){
					tAData[0]='145000000';
				}
				tTrx=tAData[9];
				tPTT=tAData[7];
				tF=tAData[0];
				tF=addPeriods(tF);
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
				$("#fPanel3").text("Mode: "+tMode+ " - BW: " + tBandWidth);
				$('#fPanel1').attr('style', 'background-color:black');
				tNoRadio=false;
				console.log("tTrx: " + tTrx);
				if (tTrx==0){
					tPTT=0;
					setPTT(0,0);
					ptt1.setVisible(false);
					ptt.setVisible(true);
					return;
				}
				if (showVideo==1 || showVideo==3 || showVideo==4){
/*					var cMeterVal=pmeter.getByName("Slider1");
					var mtr1=parseInt(100*tAData[4],10)/tMeterCal;
					mtr=mtr1;//using mtr direct provides more accurate calibration, limited testing
					cMeterVal.setValue(mtr, true);
					var cBarVal=ppanel.getByName("LinearLevel1");
					mtr=Math.floor(mtr1/7);
					mtr=mtr*7;
					cBarVal.setValue(mtr, true);
*/				}
		//Start PTT=momentary in transmit5
		if (tPTTLatch==2 && waitRefresh==0){
			tBusy=tTrx;
			var tPr=ptt.getPressed();
			if (tKnobPTT==0){
				if (tBusy==1){
					if (tKnobPTT==0 && trOn==0 && spacePTT==0){
					}else{
						setPTT(1,0);

					}
				}else{
					setPTT(0, 0);
				}
			}else{
				if (tTrx==1){
//					AbsoluteOrientationSensor
				var xo = tPr || spacePTT==1;
					if (xo==false){
						setPTT(0, 0);
//								tKnobPTT=0;
						tBusy=0;
//dupe							$.post("/programs/SetSettings.php", {field: "PTTOut", radio: tMyRadio, data: "0", table: "RadioInterface"});
					}else{
						setPTT(1, 0);
						tBusy=1;
//dupe							$.post("/programs/SetSettings.php", {field: "PTTOut", radio: tMyRadio, data: "1", table: "RadioInterface"});
					}
				}
			}
		}
		//End PTT Momentary
		//Start PTT Latch
		if (tPTTLatch==1){
			if (tTrx==0 ){
				setPTT(0, 0);
				checkPTT=0;
			}else{
				setPTT(1, 1);
				checkPTT=1;
			}
			tBusy=tPTT;
		}
		//End PTT Latch
			});
			meterTimer(tBusy);
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
	}
	$(document).on('click', '#cwButton', function()
	{
		if (bModeEnable[3]==1){
			$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'CW', table: "RadioInterface"});
		};
	});


	$(document).on('click', '#fmButton', function()
	{
		if (bModeEnable[6]==1){
			$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'FM', table: "RadioInterface"});
		};
	});

			$(document).on('click', '#cwButton', function()
	{
		if (bModeEnable[3]==1){
			$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'CW', table: "RadioInterface"});
		}
	});

	$(document).on('click', '#fmButton', function()
	{
		if (bModeEnable[6]==1){
			$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'FM', table: "RadioInterface"});
		};
	});

	$(document).on('click', '#lsbButton', function()
	{
		if (bModeEnable[0]==1){
			$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'LSB', table: "RadioInterface"});
		};
	});

	$(document).on('click', '#usbButton', function()
	{
		if (bModeEnable[1]==1){
			$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'USB', table: "RadioInterface"});
		};
	});

	$(document).on('click', '#cwrButton', function()
	{
		if (bModeEnable[4]==1){
			$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'CWR', table: "RadioInterface"});
		};
	});

	$(document).on('click', '#amButton', function()
	{
		if (bModeEnable[7]==1){
			$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'AM', table: "RadioInterface"});
		};
	});

	$(document).on('click', '#rttyButton', function()
	{
		if (bModeEnable[5]==1){
			$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'RTTY', table: "RadioInterface"});
		};
	});

	$(document).on('click', '#rttyrButton', function()
	{
		if (bModeEnable[8]==1){
			$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'RTTYR', table: "RadioInterface"});
		};
	});

	$(document).on('click', '#usbdButton', function()
	{
		if (bModeEnable[2]==1){
			updateModeButtons();
			$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'PKTUSB', table: "RadioInterface"});
		};
	});
	$.post("/programs/GetUserField.php", {un:tUserName, field:'ModeEnable'}, function(response)
		{
			if (response==""){
				response="1,1,1,1,1,1,1,1,1";
			}
			bModeEnable=response.split(",");
			updateModeButtons();
		});


		$('.modal').on('hide.bs.modal', function (e) {
			  $.post("/programs/SetSettings.php", {field: "waitReset", radio: tMyRadio, data: 0, table: "RadioInterface"});
		  });

		$(document).on('click', '#closeModalInput', function(){
			var x = document.getElementById("curFreq");
			var f = x.value.replaceAll(".","");
			if (f != null || f !=""){
				//note: mainout and subout set in and out
				$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: f, table: "RadioInterface"});
				$.post("/programs/SetSettings.php", {field: "SubOut", radio: tMyRadio, data: f, table: "RadioInterface"});
			}
			var x = document.getElementById("curMode1");
			var m = x.value;
			tMode=m;
			if (m != null || m !=""){
				var x = document.getElementById("curPassband1").value;
				var xP=x.split("=");
				xP[1]=xP[1].replace("Hz","");
				$.post("/programs/SetSettings.php", {field: "BWOut", radio: tMyRadio, data: xP[1],table:"RadioInterface"});
				$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: m,table:"RadioInterface"});
			};
		});
	}; //end no widgets

	$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'ID', table: 'MySettings'}, function(response)
	{
		tID=response;
	});

	$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DisableSplitPolling', table: 'MySettings'}, function(response)
	{
		tSplitDisabled=response;
		if (tSplitDisabled==1){
			$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'SplitOut'}, function(response)
			{
				tSplitOn=response;
				toggleSplitButton(tSplitOn, 2);
			});
		};
	});
	$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKFunction', table: 'Keyer'}, function(response)
	{
		tMyKeyerFunction=response;
	});
	$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'PTTLatch', table: 'MySettings'}, function(response)
	{
		tPTTLatch=response;
	});
	$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKRemotePort', table: 'Keyer'}, function(response)
	{
		tMyKeyerPort =response;
	});
	$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKRemoteIP', table: 'Keyer'}, function(response)
	{
		tMyKeyerIP=response;
	});
	$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKSpeedPotEnable', table: 'Keyer'}, function(response){
		  speedPotEnable=response;
	});


	$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'Model', table: 'MySettings'}, function(response)
	{
		tRadioModel=response;
	});
	$.get('/programs/GetMyRadio.php', 'f=KeyerPort&r='+tMyRadio, function(response1) {
		tCWPort= response1;
		if (tCWPort>4530 && tCWPort<5000){
			tCWPort='0.0.0.0:'+tCWPort;
		}
		$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'Keyer', table: 'MySettings'}, function(response)
		{
			$('#myKeyer').text("CW: "+response);
			tMyKeyer=response;
			});
			$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'SlaveCommand', table: 'MySettings'}, function(response){
			if (response == "Macro Decimal"){
				var ple=$('#dLED');
					ple.removeClass('d-none');
			}
		});
	});
	tUpdate = setInterval(updateTimer,300);
//	tUpdate = setInterval(meterTimer,30);

	$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'PTTMode', table: 'MySettings'}, function(response)
	{
		tMyPTT=response;
	});
	$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'RotorPort', table: 'MySettings'}, function(response)
	{
		tMyRotorPort=response;
		if (tMyRotorPort > 4532 && tMyRotorPort < 5000){  //VE9GJ
			tMyRotorRadio = (tMyRotorPort - 4531)/2;
		}else{
			tMyRotorRadio = tMyRadio;
		}
	});
	$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'LogName', table: 'MySettings'}, function(response)
	{
		$('#myLog').text("Log: "+response);
	});
	$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MacroBankTuner', table: 'RadioInterface'}, function(response){
			mBank=response
			$('#myBank').text("Macro Bank: "+response);
			var t=0;
	});
	$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
	{
		$('#searchText').val(response.toUpperCase());
	});
});
$(document).on('click', '#macroButton', function(){
		var win="/macros.php?x="+tUserName+"&c="+tMyCall;
		window.open(win, "_self");
	});
	$(document).on('click', '#modeButton', function(){
		var win="/modeFilter.php?x="+tUserName+"&c="+tMyCall;
		window.open(win, "_self");
	});
	$(document).on('click', '#bandButton', function(){
		var win="/bandFilter.php?x="+tUserName+"&c="+tMyCall;
		window.open(win, "_self");
	});
});

var jsonKnob='';
var jsonLED='';
if (notGotPerfectWidgets==0){
	<?php include $dRoot . "/includes/$tThemePanel"; ?>;
	<?php include $dRoot . "/includes/$tThemeMeter"; ?>;
	<?php include $dRoot . "/includes/bigPTT1.json"; ?>;
	<?php include $dRoot . "/includes/led.json"; ?>;

	//*************************************************//
	// The three following PerfectWidgets components are proprietary included under a commercial license and are NOT open source
	pmeter = new PerfectWidgets.Widget("dMeter", jsonSMeter);
	ppanel = new PerfectWidgets.Widget("dPanel", jsonPanel);
	pknob = new PerfectWidgets.Widget("dKnob", jsonKnob);
	pled = new PerfectWidgets.Widget("dLED", jsonLED);
	var theme=0;//<?php echo $theme; ?>

	switch(theme){
	case 0:
		bk="Orange";
		break;
	case 1:
		bk="Night";
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
	//*************************************************//

	if (tFirstUse>0){
		$("#play").removeClass('d-none');
		$("#playOK").addClass('d-none');
	}
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
	if (tDisconnected==1){
		showConnectAlert();
		return false;
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
	waitRefresh=4;

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
	}
}


$(".slider" ).on('touchstart',function() {
	stopSliderScroll();
});

$(".slider" ).on('touchend',function(e) {
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
const pttb = document.getElementById('dKnob');
function handleInteraction(event) {
  event.preventDefault();
		if (tPTTLatch==2){
	  setPTT(0, 0);
	  spacePTT=0;
  }
}
if (window.PointerEvent) {  //to fix PTT lag for mobile and pc browsers
  pttb.addEventListener('pointerup', handleInteraction);
} else {
  pttb.addEventListener('mouseup', handleInteraction);
  pttb.addEventListener('touchend', handleInteraction);
}
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
		username: '',
		password: ''});
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
		if (tDisconnected==1){
			showConnectAlert();
			return false;
		}
		if (tDisconnected==0){
			$('#dt').blur().focus();
		}else{
			$(".modalA-body").html("<br>&nbsp;&nbsp;The radio is not connected.<p><p>");
			$(".modalA-title").html("Radio Connection");
			  $("#myModalAlert").modal('show');
			  setTimeout(function(){
					$("#myModalAlert").modal('hide');
			   },
				2000);
			  return;
		}
	}

   function plusClicked(updateVal)
	{
		if (tDisconnected==1){
			showConnectAlert();
			return false;
		}
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
		if (tDisconnected==1){
			showConnectAlert();
			return false;
		}
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
		if (tDisconnected==1){
			showConnectAlert();
			return false;
		}
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
		if (tDisconnected==1){
			showConnectAlert();
			return false;
		}
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
$(document).keydown(function(e){
	var t=e.key;
	e.multiple
	var w=e.which;
	if (w==13){
		$("#searchButton").click();
	}
});

	$("input").bind("keydown", function(event)
	{
		// track enter key
		var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
		if (keycode == 13) { // keycode for enter key
			if ($('#searchText').val()==''){
				return false;
			}
			tDX=$('#searchText').val().toUpperCase();
			$('#searchText').val(tDX);
			document.getElementById('searchButton').click();
			$.post("/programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tDX, table: "MySettings"});
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

	function checkConnection(response){
		if (response.indexOf("OFF")==-1){
			tDisconnected=0;
			var text= "<br>"+tRadioName + " is connected, frequency is: <br><br><h2c>" + addPeriods(response) + " MHz<br><br>";
		}else{
			setTimeout(() => console.log("First"), 1000);
			$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MainIn', table: 'RadioInterface'}, function(response){
				  if (response=="OFF"){
					tDisconnected=1;
					var text="<br>RigPi is waiting for radio " + tRadioName + "...<p><p>";
				  }else{
					var text= "<br>"+tRadioName + " is connected, frequency is: <br><br>1<h2c>" + addPeriods(response) + " MHz<br><br>";
				  }
			})
		}
		if (tDisconnected==0){
			$(".modalA-body").html(text);
			$(".modalA-title").html("Radio Connection");
			  $("#myModalAlert").modal('show');
			  if (tMyPTT==3){
				  $.post('/programs/doGPIOPTT.php', {PTTControl: "on"});
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
			  var text="<br>RigPi is waiting...<p><p>"
			  $(".modalA-body").html(text);
			$("modalA-title").html("Radio Problem");
			  $("#myModalAlert").modal('show');
			  setTimeout(function(){
				  $("#myModalAlert").modal('hide');
			 },
			  2000);
		  }
	}
	function doHamlib(){
		$.post('/programs/hamlibDo.php', {test: 0, keyer:tMyKeyer, radio:tMyRadio, user:tUserName, radioPort:tRadioPort, CWPort:tCWPort, tcpPort:tMyTCPPort, rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction, UDPPort: tUDPPort}, function(response) {
			if (response.indexOf("OFF")>-1){
				checkConnection(response);
				startCounter=0;
				startConnection=0;
			}else{
				startConnection=1;
				startCounter=4;
			}
		})
	}

		function connectRadio(){
			var pl=$('#play');
			pl.addClass('d-none');
			var pl=$('#playOK');
			pl.addClass('d-none');
			var el=$('#spinner');
			el.addClass('fa-spin');
			el.removeClass('d-none');
			waitRefresh=4;
			tDisconnected=0;
			if (tMyKeyer=='RigPi Keyer' || tMyKeyer == 'rpk1'){
				tMyKeyer='rpk1';
				$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKSpeed'}, function(response) {
					tSpeed=response;
					updateButtonLabels("/OLD", tSpeedOriginal);//tSpeed);
					$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tSpeed, table: "Keyer"});
					$.post("/programs/SetSettings.php", {field: "CWChangeCk", radio: tMyRadio, data: 1, table: "RadioInterface"});
					$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: "", table: "RadioInterface"});
				});
			}else if (tMyKeyer=='via CAT'){
				tMyKeyer='cat';
			}else if (tMyKeyer=="WinKeyer"){
				tMyKeyer="wkr";
			}else if (tMyKeyer=="External CTS"){
				tMyKeyer="ext";
			}
			var response=false;
			doDisconnect();
			var el=$('#spinner');
			el.addClass('fa-spin');
			el.removeClass('d-none');

			$.post('/programs/h.php',{test: 0, keyer: tMyKeyer,radio:tMyRadio, user:tUserName, radioPort:tRadioPort, CWPort:tCWPort, tcpPort:tMyTCPPort, rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction, UDPPort: tUDPPort, startUpDelay: 0, lite: 0},function(response){
				doHamlib();
			});
	};
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
		function doDisconnect(){
			tSplitOn=0;
			toggleSplitButton(tSplitOn, 1);
			var el=$('#spinner');
			  el.addClass('d-none');
			  tDisconnected=1;
		   $.post("/programs/SetSettings.php", {field: "SplitOut", radio: tMyRadio, data: tSplitOn, table: "RadioInterface"});
			  $.post('/programs/disconnectRadio.php', {radio: tMyRadio, port: tRadioPort, id: tID, user: tUserName, rotor: tMyRotorRadio, instance:<?php echo $_SESSION[
         "myInstance"
     ]; ?>}, function(response) {
			  var pl=$('#play');
				pl.removeClass('d-none');
				var pl=$('#playOK');
				pl.addClass('d-none');
				if (tPC.indexOf('Off')>0){
					$(".modalA-body").html('<br>Radio is disconnected and power is off.<br><br>');
				}else{
					$(".modalA-body").html('<br>Radio is disconnected.<br><br>');
				}
				$(".modalA-title").html("Radio Connection");
				  $("#myModalAlert").modal('show');
				  setTimeout(function(){
					  $("#myModalAlert").modal('hide');
				 },
				  2000);
				   $.post("/programs/SetSettings.php", {field: "SubIn", radio: tMyRadio, data: "OFF", table: "RadioInterface"});
				   $.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: "OFF", table: "RadioInterface"});
				  if (tMyPTT==3){
					  $.post('/programs/doGPIOPTT.php', {PTTControl: "off"});
				  }
			});
			waitRefresh=4;

		}


		function disconnectRadio(){
			return; //not used
			tSplitOn=0;
				toggleSplitButton(tSplitOn, 1);
				var el=$('#spinner');
				  el.addClass('d-none');
				  tDisconnected=1;
			   $.post("/programs/SetSettings.php", {field: "SplitOut", radio: tMyRadio, data: tSplitOn, table: "RadioInterface"});
				  $.post('/programs/disconnectRadio.php', {radio: tMyRadio, port: tRadioPort, id: tID, user: tUserName, rotor: tMyRotorRadio}, function(response) {
				  var pl=$('#play');
					pl.removeClass('d-none');
					var pl=$('#playOK');
					pl.addClass('d-none');
					$(".modalA-body").html("<br"+response+"<br><br>");
					$(".modalA-title").html("Radio Connection");
					  $("#myModalAlert").modal('show');
					  setTimeout(function(){
						  $("#myModalAlert").modal('hide');
					 },
					  2000);
					   $.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: "OFF", table: "RadioInterface"});
					  if (tMyPTT==3){
						  $.post('/programs/doGPIOPTT.php', {PTTControl: "off"});
					  }
				});
				waitRefresh=4;
		}

		$(document).on('click', '#disconnect', function()
		{
			doDisconnect();
		});


		$(document).on('click', '#A2BButton', function()
		{
			if (tDisconnected==1){
				showConnectAlert();
				return false;
			}
			if (tSplitOn==0){
				showDisabedAlert();
				return false;
			}
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
			if (tDisconnected==1){
				showConnectAlert();
				return false;
			}
			if (tSplitOn==0){
				showDisabledAlert();
				return false;
			}
			$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'MainIn'}, function(response)
			{
				var tA=response;
				$.post('/programs/SetSettings.php', {field: 'M', radio: tMyRadio, data: tA, table: 'RadioInterface'});
			})
		});

		$(document).on('click', '#M2AButton', function()
		{
			if (tDisconnected==1){
				showConnectAlert();
				return false;
			}
			if (tSplitOn==0){
				showDisabledAlert();
				return false;
			}
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
			if (tDisconnected==1){
				showConnectAlert();
				return false;
			}
			if (tSplitOn==0){
				showDisabledAlert();
				return false;
			}
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

		<?php require_once $dRoot . "/includes/buildMacros.php"; ?>

		$(document).on('click', '#SplitaButton', function()
		{
			if (tDisconnected==1){
				showConnectAlert();
				return false;
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
					toggleSplitButton(tSplit, 1);
					waitRefresh=0;
					$.post("/programs/SetSettings.php", {field: "SplitOut", radio: tMyRadio, data: tSplitOn, table: "RadioInterface"});
				 });
			}else{
				if (tSplit==0){
					tSplit=1;
					doDigit(0, 1);
					doSubDigit(tuningIncrement, 1);
				}else{
					tSplit=0;
					doSubDigit(0, 1);
					doDigit(tuningIncrement, 1)
				}
				toggleSplitButton(tSplit, 1);
				$.post("/programs/SetSettings.php", {field: "SplitOut", radio: tMyRadio, data: tSplit, table: "RadioInterface"});
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



		$.post('/programs/GetUserField.php', {un:tUserName,field:'WSJTXPort'}, function(response){
		  tUDPPort=response;
		});

	var tUpdate = setInterval(bearingTimer,1000)
	function bearingTimer()
	{
		$.post("/programs/GetRotorIn.php", {rotor: tMyRotorRadio},function(response){
			var tAData=response.split('`');
			tAData=response;
			if (tAData=="+"){
				tAData="--";
			}
			tAz=Math.round(tAData)+"&#176;";
			tCurBeam=tAz;
			for (i = 0; i < 32; i++) {
				x=aMacros[i];
				if (typeof x=='string'){
					if (x.indexOf("ROTATE")>-1){
						var mB="m" + i + "Button";
						y=x.split("|");
						if (document.getElementById(mB)){
							document.getElementById(mB).innerHTML=y[0] + " (" + tCurBeam + ")";
						}
					};
				};
			};

	//		refreshButtons();
			$(".angle").html(tAz);
		});
		tBand=GetBandFromFrequency(tMain);
//		if (tBand.indexOf('NK')>0){
//			setTopBand(tBand);
//		}else{
			if (tBand==70 || tBand==23){
				setTopBand(tBand+'cm');
			}else{
				setTopBand(tBand+'m');
			}
//		}
		$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKSpeed'}, function(response) {
			if (response.indexOf("NG")<0){
			tSpeed=response;
			for (i = 0; i < 32; i++) {
				x=aMacros[i];
				if (typeof x=='string'){

				if (x.indexOf("/OLD")>-1){
					var mB="m" + i + "Button";
					y=x.split("|");
					if (document.getElementById(mB)){
						document.getElementById(mB).innerHTML=y[0] + " (" + tSpeed + ")";
					}
				};
			};
			};

			if (speedPotEnable==1 && speedLock==0){
				$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKPot'}, function(response) {
					if (speedPot!=response){
						speedPot=response;
						tSpeed=speedPot;
						updateButtonLabels("/OLD", tSpeedOriginal);//tSpeed);
						$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKMinWPM'}, function(response) {
							var tMin=response;
							tSpeed=parseInt(tSpeed)+parseInt(tMin);
							$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tSpeed, table: "Keyer"});
							$.post("/programs/SetSettings.php", {field: "CWChangeCk", radio: tMyRadio, data: 1, table: "RadioInterface"});
							waitRefresh=2;

						});
					};

				});
			}
		};
		});
	}

	$(document).keyup(function(e)
	{
		if (tDisconnected==1){
	//		showConnectAlert();
	//		return false;
		}
		var t=e.key;
		var w=e.which;
		if (e.key==="Escape")
		{ // escape key maps to keycode `27`
			holdText='';
			var tNum=String.fromCharCode(10);
			if (tNum==""){
				return;
			}
			$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: tNum, table: "RadioInterface"});
	//		$.post("./programs/SetSettings.php", {field: "CommandOut", radio: tMyRadio, data: "*\\stop_morse", table: "RadioInterface"});
			trOn=0;   //false?
			spacePTT=0;
			setPTT(0,0);
			return false;
		} else if (e.key===" "){
			if(tIgnoreRepeating==true){
				tIgnoreRepeating=false;
				if (tPTTLatch==2){
					spaceUp=true;
					spacePTT=0;
	//dupe			$.post("./programs/SetSettings.php", {field: "PTTOut", radio: tMyRadio, data: 0, table: "RadioInterface"});
					setPTT(0, 0);
					$('#dt').blur().focus();
					waitRefresh=3;
				}
			}
		}
	});

	windowLoaded=true;


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
			var w=e.which;
			if (w==13){
				$("#seachButton").click();
				return false;
			}
			if (e.ctrlKey){
				if (tDisconnected==1){
					showConnectAlert()
					return false;
				}
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
					case 65: //a
						showCalendar();
						e.preventDefault();
						break;
					case 67: //c
						connectRadio();
						break;
					case 68: //d
						$.post('/programs/disconnectRadio.php', {radio: tMyRadio, port: tRadioPort, id: tID, user: tUserName, rotor: tMyRotorRadio, instance:<?php echo $_SESSION[
          "myInstance"
      ]; ?>}, function(response) {
						});
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
					case 88: // xx
						openWindowWithPost("/login.php", {
						status: "loggedout",
						password: '',
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
						  $("#myModalCancelOnly").modal('show');
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
					if (tDisconnected==1){
						showConnectAlert();
						return false;
					}
					if (tIgnoreRepeating==false) { //|| tPTTLatch==1){
						clearTimeout(resetTimer);

						tIgnoreRepeating=true;
						if (spacePTT==0){
							setPTT(1,0);
							spacePTT=1;
						}else{
							setPTT(0,0);
							spacePTT=0;
						}
					}
					e.preventDefault();
//					resetTimer=setTimeout(resetIgnoreRepeating,800);
					console.log("PTT Off");
					waitRefresh=3;
					return false;
				}else if (t ==="+"){
					if (tDisconnected==1){
						showConnectAlert();
						return false;
					}
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
					if (tDisconnected==1){
						showConnectAlert();
						return false;
					}
					if (tOverPanel==true){
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
				}
					return false;
				}else if (w==39){
					if (tDisconnected==1){
						showConnectAlert();
						return false;
					}
					if (tOverPanel==true){
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
	}); //end ready

	function updateColors(){
			var which=mBank;
			$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'Macros'+mBank}, function(response)
			{
				if (response.indexOf("NG")<0){
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'LatchBtn'+mBank, table: 'RadioInterface'}, function(response1){
				if (response1==""){
					response=",".repeat(32);
				}
				latchBtn=response1.split(",");

				var tMacros=decodeURIComponent((response+'').replace(/\+/g,'%20'));
				loadMacroBank(mBank);
				aMacros=tMacros.split('~');
				var mBtn;
				aMCommands=[];
				for (i = 0; i < 32; i++) {
					var mID='m'+i+'Button';
					var tLabel = aMacros[i];
					latchBtn=latchBtn+1;
					tLabel=tLabel.split('|');
					var tLatch=latchBtn[i];
					if (tLatch.indexOf("+")>0){
						btnLatchColor=tLatch.substr(tLatchindexOf("+")+1);
					}else{
						btnLatchColor="btn-color";
					}
					var btn =document.getElementById(mID);
					btn.innerHTML=tLabel[0];
					var arlbtn=latchBtn[i];
					if (arlbtn==null || arlbtn==""){
						arlbtn="?";
					}
					if (arlbtn!="?"){
						$(btn).removeClass(btnLatchColor);
						$(btn).addClass("btn-color");
					}else{
						$(btn).removeClass("btn-color");
						$(btn).addClass(btnLatchColor);
					}
					if (tLabel[0].trim()=="BANK"){
						mBtn=btn;
						mBtn.innerHTML="BANK "+mBank;
					}
					aMCommands.push(tLabel[1]);
				};
			});
		};
		});
	};

	function updateButtonLabels (which, what){
		for (i = 0; i < 32; i++) {
			x=aMacros[i];
				if (typeof x=='string'){
					if (which!='/OLD'){
						if (x.indexOf(which)>-1){
							mB="m" + i + "Button";
							y=x.split("|");
							if (document.getElementById(mB)){
								document.getElementById(mB).innerHTML=y[0] + " (" + what + ")";
							}
						};
					}else{
					x=x.replace("ZZ",tSpeedOriginal);
					var x1=x.indexOf('/OLD');
					if (x1>-1){
						var mB="m" + i + "Button";
						y=x.split("|");
						what=tSpeedOriginal;
						if (document.getElementById(mB)){
							document.getElementById(mB).innerHTML=y[0] + " (" + what + ")";
						}
					};

				}
			};
		};
	};

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
		if (checkPTT==1 && state == 1 ){
			return;
		}
		checkPTT=0;
		waitRefresh=4;
		if (state==1){
			console.log("PTT ON");
		};
		if (state==0){
			console.log("PTT OFF");
		};


		if (notGotPerfectWidgets==0 && tAccessLevel < 4){
			var ttPTT=tMyRadio;
			if (tRadioModel=="NET rigctl"){
				if (tRadioPort.indexOf('4532')!=-1){
					ttPTT=1;
				}else if (tRadioPort.indexOf('4534')!=-1){
					ttPTT=2;
				}else if (tRadioPort.indexOf('4536')!=-1){
					ttPTT=3;
				}else if (tRadioPort.indexOf('4538')!=-1){
					ttPTT=4;
				}else if (tRadioPort.indexOf('4540')!=-1){
					ttPTT=5;
				}else if (tRadioPort.indexOf('4543')!=-1){
					ttPTT=6;
				}else if (tRadioPort.indexOf('4544')!=-1){
					ttPTT=7;
				}else if (tRadioPort.indexOf('4546')!=-1){
					ttPTT=8;
				}else{
					ttPTT=tMyRadio;
				}
			}
			if (state==1  && tPTTIsOn==0 && tDisconnected==0){	// added tDisconnected to prevent transmit when radio disconnected
				tKnobPTT=1;
				xmit=true;
				trXmit=true;
				if (pttBypass==0){
					$.post("/programs/SetSettings.php", {field: "PTTOut", radio: ttPTT, data: "1", table: "RadioInterface"});
					$.post("/programs/SetSettings.php", {field: "Transmit", radio: ttPTT, data: "1", table: "RadioInterface"});
				}

				if (tMyPTT==1){
					$.post('/programs/doGPIOPTT.php', {PTTControl: "on"});
				}
				var cRX=ppanel.getByName("TR");
				cRX.setText("XMIT");
				ptt1.setVisible(true);
				ptt.setVisible(false);

				cRX.setNeedRepaint(true);
				cRX.refreshElement();
				ptt1.setVisible(true);
				ptt.setVisible(false);
				var cMeterLabel=pmeter.getByName("MtrFn");
				cMeterLabel.setText(mtrLabel);
				cMeterLabel.setNeedRepaint(true);
				cMeterLabel.refreshElement();
			}else{
				tKnobPTT=0;
				xmit=false;
				tTrx=0;
				trXmit=false;
				trOn=0;
				if (pttBypass==0){
					$.post("/programs/SetSettings.php", {field: "PTTOut", radio: ttPTT, data: "0", table: "RadioInterface"});
					$.post("/programs/SetSettings.php", {field: "Transmit", radio: ttPTT, data: "0", table: "RadioInterface"});
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
			}
		}
	}

	function toggleSplitButton(split, forgetAlert)
	{
		if (tDisconnected==1 && forgetAlert==0){
			showConnectAlert();
			return false;
		}
		if (split==1)
		{
			$('.btn-toggle').removeClass('btn-color');
			$('.btn-toggle').removeClass('btn-primary');
			$('.btn-toggle').addClass('btn-danger');
			$('#A2BButton').addClass('btn-color');
			$('#A2BButton').addClass('btn-primary');
			$('#A2BButton').removeClass('btn-secondary');
			$('#A2MButton').addClass('btn-color');
			$('#A2MButton').addClass('btn-primary');
			$('#A2MButton').removeClass('btn-secondary');
			$('#M2AButton').addClass('btn-color');
			$('#M2AButton').addClass('btn-primary');
			$('#M2AButton').removeClass('btn-secondary');
			$('#ABButton').addClass('btn-color');
			$('#ABButton').addClass('btn-primary');
			$('#ABButton').removeClass('btn-secondary');
		}
		else   //split off
		{
			$('.btn-toggle').addClass('btn-primary');
			$('.btn-toggle').addClass('btn-color');
			$('.btn-toggle').removeClass('btn-danger');
			$('#A2BButton').removeClass('btn-primary');
			$('#A2BButton').removeClass('btn-color');
			$('#A2BButton').addClass('btn-secondary');
			$('#A2MButton').addClass('btn-secondary');
			$('#A2MButton').removeClass('btn-color');
			$('#A2MButton').removeClass('btn-primary');
			$('#M2AButton').removeClass('btn-primary');
			$('#M2AButton').removeClass('btn-color');
			$('#M2AButton').addClass('btn-secondary');
			$('#ABButton').removeClass('btn-primary');
			$('#ABButton').removeClass('btn-color');
			$('#ABButton').addClass('btn-secondary');
		}
	}

	function loadMacroBank(which)
	{
		which=which.toString();
		$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'LatchBtn'+which, table: 'RadioInterface'}, function(response){
			if (response.indexOf("NG")<0){

			if (response==""){
				response="?,".repeat(32);
			}
			latchBtn=response.split(",");

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
				if (tLabel[0].trim()=="BANK"){
					mBtn=btn;
					mBtn.innerHTML="BANK "+mBank;
				}
				if (tLabel[0].substring(0,6)=="ROTATE" && tLabel[0].indexOf("STOP")==-1){
					tRotateButton=btn;
					tRotateButtonLabel=tLabel[0];
					mBtn=btn;
					mBtn.innerHTML=tRotateButtonLabel + " (" + tCurBeam+")";
				}
				aMCommands.push(tLabel[1]);
			}
			if (tAccessLevel<4){
				$.post('/programs/isSupported.php',{radio: tMyRadio, getSet: 'set', text:'AF('}, function(response){
					if (response==1 && sliderAFGainOride>0){
						$("#AF").removeClass('d-none');
						tVal=$("#sliderAF").slider('value');
						var tV=tVal;
						if (tV==0){
							tV=1;
						}
						updateButtonLabels('RADIO AF MUTE', tV-1);
					}
				});
				$.post('/programs/isSupported.php',{radio: tMyRadio, getSet: 'set', text:'USB_AF'}, function(response){
					supportsUSB_AF=response;
				});

				$.post('/programs/isSupported.php',{radio: tMyRadio, getSet: 'set', text:'RF('}, function(response){
					if (response==1 && sliderRFGainOride>0){
						$("#RF").removeClass('d-none');
					}
				});
				$.post('/programs/isSupported.php',{radio: tMyRadio, getSet: 'set', text:'RFPOWER('}, function(response){
					if (response==1 && sliderPwrOutOride>0){
						$("#Pwr").removeClass('d-none');
					}
				});
				$.post('/programs/isSupported.php',{radio: tMyRadio, getSet: 'set', text:'MICGAIN('}, function(response){
					if (response==1 && sliderMicLvlOride > 0){
						$("#Mic").removeClass('d-none');
					}
				});
			}
		})
	};
		});
	}

	var aMCommands=[];
	//processCommand ties into /includes/buildMacros.php
	function showConnectAlert(){
		$(".modalA-body").html("<br>&nbsp;&nbsp;The radio is not connected.<p><p>");
		$(".modalA-title").html("Radio Connection");
		  $("#myModalAlert").modal('show');
		  setTimeout(function(){
			  $("#myModalAlert").modal('hide');
		 },
		  2000);
		return;
	}

	function showDisabledAlert(){
		$(".modalA-body").html("<br>VFO buttons work when Split is on.<p><p>");
		$(".modalA-title").html("Split Alert");
		  $("#myModalAlert").modal('show');
		  setTimeout(function(){
			  $("#myModalAlert").modal('hide');
		 },
		  2000);
		return;
	}

	function doFKey(which,btn){
		var thisOne=which.substring(0, 1);
		var tCommand=which.substr(which.indexOf(":")+1);
		processCommand(tCommand, btn);
	}

	function processCommand(which,btn)
	{
		var tMe=tMyCall;
		var tWhat = which.replace(/'\*/g, tMe);
		which = tWhat.replace(/'X/g,$('#searchText').val());
		tPre=which.substring(0, 1);
		tPost=which.substring(1);
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
			if (arlbtn !== "?"){
				//second state of latch
				tPost=latchBtn[lbtn];
				latchBtn[lbtn]="?";
				tPre=tPost.substr(0,1);
				tPost=tPost.substring(1);
				tPost=tPost.replace("XX",AFGainOld);
				tPost=tPost.replace("YY",USBAFGainOld);
				tPost=tPost.replace("ZZ",tSpeedOriginal);
				if (tPost.indexOf("+")!==-1){
					tPost=tPost.substring(0,tPost.indexOf("+"));
				}
				$(btn).removeClass(btnLatchColor);
				$(btn).addClass("btn-color");
				lt1=latchBtn.join(",");
				if (tPost.indexOf('L AF')!==-1){
					$.post("/programs/SetSettings.php", {field: "AFGain", radio: tMyRadio, data: AFGainOld, table: "RadioInterface"}, function(response){
						waitRefresh=1;
						var tOK=response;
						which=tPre+tPost;
						var tLat="latchBtn"+mBank;
						$.post("/programs/SetSettings.php", {field: tLat, radio: tMyRadio, data: lt1, table: "RadioInterface"}, function(response){
							});
					});
				}else{
					if (supportsUSB_AF==1 && tPost.indexOf('L USB_AF')>-1){
						$.post("/programs/SetSettings.php", {field: "USBAFGainOld", radio: tMyRadio, data: USBAFGainOld, table: "RadioInterface"}, function(response){
							updateButtonLabels('RADIO USB MUTE', USBAFGainOld);

						});

						$.post("/programs/SetSettings.php", {field: "USBAFGain", radio: tMyRadio, data: USBAFGainOld, table: "RadioInterface"}, function(response){

							var tOK=response;
							updateButtonLabels('USB MUTE', USBAFGainOld);
							which=tPre+tPost;
							var tLat="latchBtn"+mBank;
							$.post("/programs/SetSettings.php", {field: tLat, radio: tMyRadio, data: lt1, table: "RadioInterface"}, function(response){
								});
						});
					};
					waitRefresh=4;
				};
			}else{
				// first of latch
				tPre=tPost.substring(0,1);
				var tPost1=tPost.substring(tPost.indexOf("}")+1);
				tPost=tPost.substring(1,tPost.indexOf("}"));
				var tPost2=tPost1; //save second command plus color
				if (tPost1.indexOf("+")>0){
					tPost1=tPost1.substring(0,tPost1.indexOf("+"));
				}
				tPost2=tPost2.replace("XX",AFGainOld);
				tPost2=tPost2.replace("YY",USBAFGainOld);
				tPost2=tPost2.replace("ZZ",tSpeedOriginal);
				latchBtn[lbtn]=tPost2;
				$(btn).removeClass("btn-color");
				$(btn).addClass(btnLatchColor);
				lt1 = latchBtn.join(",");

				if (supportsUSB_AF==1 && tPost.indexOf('USB_AF') > -1){
					USBAFGain=0;
					updateButtonLabels('RADIO USB MUTE', 0);
					$.post("/programs/SetSettings.php", {field: "USBAFGain", radio: tMyRadio, data: 0, table: "RadioInterface"}, function(response){
					});
				}else if (tPost1.indexOf('L AF')>-1){
					AFGainOld=$("#sliderAF").slider('value');
					$.post("/programs/SetSettings.php", {field: "AFGain", radio: tMyRadio, data: 0, table: "RadioInterface"}, function(response){
					});
				}
			};
			which=tPre+tPost;
			var tLat="latchBtn"+mBank;
			$.post("/programs/SetSettings.php", {field: tLat, radio: tMyRadio, data: lt1, table: "RadioInterface"}, function(response){
				});
		};
		if (tPre=="/"){
			tDX=$('#searchText').val().toUpperCase();
			tPost=tPost.replace('<dxcall>',tDX);
			if (tPost.indexOf('<mode>')>0){
				var tM='';
				if (tMode=="USB" || tMode=="LSB"|| tMode=="AM" || tMode=="FM"){
					tM="PHONE";
				}else if (tMode=="PKTLSB" ||tMode=="PKTUSB" || tMode=="USB-D" || tMode=="LSB-D" || tMode=="RTTY" || tMode=="RTTYR"){
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
			if (tDisconnected==1){
				showConnectAlert();
				return false;
			}
			if (tPost.indexOf('<02>')==0){
				var tSpeedP=tPost.split(">");
				if (tSpeedP[1]==0){
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKSpeedOriginal', table: 'Keyer'}, function(response)
					{

						tSpeedOriginal=response;
						updateButtonLabels('/OLD', tSpeedOriginal);
						tPost="<02><"+parseInt(tSpeedOriginal)+">";
						$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tSpeed, table: "Keyer"});
					});
				}else{
					tPost="<02><"+parseInt(tSpeedP[1])+">";
					updateButtonLabels("/OLD", tSpeedOriginal);
					$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tSpeedP[1], table: "Keyer"});
					$.post("/programs/SetSettings.php", {field: "WKSpeedOriginal", radio: tMyRadio, data: tSpeedOriginal, table: "Keyer"});
				}
			}
				if (tPost.indexOf('<03>')>-1){
					$.post("/programs/SetSettings.php", {field: "WKSpeedPotEnable", radio: tMyRadio, data: 1, table: "Keyer"});
					speedPotEnable=1;
					return false;
				}
				if (tPost.indexOf('<04>')>-1){
					$.post("/programs/SetSettings.php", {field: "WKSpeedPotEnable", radio: tMyRadio, data: 0, table: "Keyer"});
					speedPotEnable=0;
					return false;
				}
			tPost=" " + tPost;
			$.post("/programs/ConcatSettings.php", {field: "CWIn", radio: tMyRadio, data: tPost, table: "RadioInterface"}, function(response){
				var tt=response;
			});
			return false;
		}
		if (which=="*PS1;")
		{
			if (tRadioModel !="NET rigctl"){
				if (tDisconnected==0){
					$("#modalCO-body").html("<br>Radio is already on.<br><br>");
					$("#modalCO-title").html("Radio Power");
					  $("#myModalCancelOnly").modal('show');
					setTimeout(function(){
						  $("#myModalCancelOnly").modal('hide');
					 },
					  2000);
					return true;
				}
				$("#modalCO-body").html("Radio is powering up, please wait.");
				$("#modalCO-title").html("Radio Power");
				  $("#myModalCancelOnly").modal('show');
				$.post('/programs/powerOn.php', {radio: tMyRadio, user: tUserName}, function(response){
					$.post('/programs/disconnectRadio.php', function(response1){  //defaults to instance 0
						$("#modalCO-body").html("Power is on.");
						  setTimeout(function(){
							  $("#myModalCancelOnly").modal('hide');
						 },
						  2000);
					  });
				});
			}else{
				$("#modalCO-body").html("Shared radio access (Net rigctl) can't control power on/off.");
				$("#modalCO-title").html("Radio Power");
				$("#myModalCancelOnly").modal('show');
			}
		}else if (which=="*PS0;"){
				if (tRadioModel !="NET rigctl"){
					tSplitOn=0;
					toggleSplitButton(tSplitOn, 1);
					$.post("/programs/SetSettings.php", {field: "SplitOut", radio: tMyRadio, data: tSplitOn, table: "RadioInterface"});
					$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: "OFF", table: "RadioInterface"}, function(response){
						if (tMyPTT==3){
							$.post('/programs/doGPIOPTT.php', {PTTControl: "off"});
						}
					});
					$.post('/programs/powerOff.php', {radio: tMyRadio, user: tUserName}, function(response){

						$.post('/programs/disconnectRadio.php', {id: tID, radio: tMyRadio, port: tRadioPort, user: tUserName, rotor: tMyRotorRadio, instance:<?php echo $_SESSION[
          "myInstance"
      ]; ?>}, function(response1){
							$(".modalA-body").html("<br>Radio is disconnected and power is off.<br><br>");
							$(".modalA-title").html("Radio Connection");
							$("#myModalAlert").modal('show');
							setTimeout(function(){
									$("#myModalAlert").modal('hide');
							   },
								4000);
							  return;
						});
					});
				}else{
					$("#modalCO-body").html("Shared radio access (Net rigctl) can't control power on/off.");
					$("#modalCO-title").html("Radio Power");
					$("#myModalCancelOnly").modal('show');
				};
		}else if (which.substr(0,7)=="!ROTATE"){
			if (tDisconnected==1){
				showConnectAlert();
				return false;
			}

			tRotateButtonCommand=which;
			var ro=which.split(" ");
			if (ro[1]){
				//special macro with space to rotate to specific value
				$.post("/programs/SetMyRotorBearing.php", {w: "turn", i: tMyRotorRadio, a: ro[1]});
			}else{
				$.post('/programs/GetSetting.php',{radio: tMyRotorRadio, field: 'RotorAzIn', table: 'RadioInterface'}, function(response)
				{
					if (tDisconnected==1){
						showConnectAlert();
						return false;
					}
					terr=0;
					if (response =="")
					{
						terr=1;
						response=0;
					}
						$.post("/programs/SetSettings.php", {field: "waitReset", radio: tMyRadio, data: 1, table: "RadioInterface"}, function(response1)
						{
						if (terr==1){
							var caption="Rotor bearing error.\n\nEnter new value and click OK.";
						}else{
							var caption="Rotor bearing is now " + parseInt(response) + " deg.\n\nEnter new value and click OK.";
						}
						var curBeam=parseInt(response);
						var text = prompt(caption, parseInt(response));
						if (text){
							$.post("/programs/SetMyRotorBearing.php", {w: "turn", i: tMyRotorRadio, a: text}); //VE9GJ
						}else{
							$.post("/programs/SetMyRotorBearing.php", {w: "err", i: tMyRotorRadio, a: 0});
						}
					});
				});
			}
		}else if (which=="!RTR STOP"){
			$.post("/programs/SetMyRotorBearing.php", {w: "stop", i: tMyRotorRadio, a: ''});  //VE9GJ
		}else if (which=="!BANK"){
			mBank=parseInt(mBank)+1;
			if (mBank==5){mBank=1};
			loadMacroBank(mBank);
			for (i=1;i<5;i++){
				var mB='#myBank'+i;
				$(mB).addClass('btn-color');
			}
			var mB='#myBank'+mBank;
			$(mB).removeClass('btn-color');
			$(mB).addClass("btn-info");
			$.post("/programs/SetSettings.php", {field: "MacroBankTuner", radio: tMyRadio, data: mBank, table: "RadioInterface"});
			return false;
		}else if (which=="!PTTON"){
			  if (tDisconnected==1){
				  showConnectAlert();
				  return false;
			  }
			  $.post('/programs/doGPIOPTT.php', {PTTControl: "on"});
		}else if (which=="!PTTOFF"){
			  if (tDisconnected==1){
				  showConnectAlert();
				  return false;
			  }
			  $.post('/programs/doGPIOPTT.php', {PTTControl: "off"});
		}else if (which.substring(0,3)=="!SW"){
			if (tDisconnected==1){
				showConnectAlert();
				return false;
			}
			$.post("/programs/SetSettings.php", {field: "CommandOut", radio: tMyRadio, data: which, table: "RadioInterface"});
		}else{
			switch(tPre)
			{
			case "*":	//direct radio command using hamlib format
				var pl=$('#play');
				if (tDisconnected==1){
					showConnectAlert();
					return false;
				}
				if (!pl.hasClass('d-none')){
					return;
				}
				$.post("/programs/SetSettings.php", {field: "CommandOut", radio: tMyRadio, data: "*"+tPost, table: "RadioInterface"});
				if (tPost=="\stop_morse"){
					setPTT(0, 0);
					var tNum=String.fromCharCode(10);
					$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: tNum, table: "RadioInterface"});
				}
				break;
			case '#':	//direct system command
				$.post("/programs/systemExec.php", {command: tPost});
				break;
			case '!':	//special command
				if (tPost=='ESC'){
					setPTT(0, 0);
					var tNum=String.fromCharCode(10);
					$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: tNum, table: "RadioInterface"});
				}else if (tPost=='TUNE'){
					if (tTuneOn==1){
						tTuneOn=0;
						var tNum=String.fromCharCode(11)+String.fromCharCode(0);
						$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: tNum, table: "RadioInterface"});
					}else{
						tTuneOn=1;
						var tNum=String.fromCharCode(11)+String.fromCharCode(1);
						$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: tNum, table: "RadioInterface"});
					}
				}else if (tPost=='VOIP1'){
					$.post("/programs/start-mumble1.php", function  (response) {
					});
				}else if (tPost=='TUNETO'){
					if (tDisconnected==1){
						showConnectAlert();
						return false;
					};
					$("#curMode1").val(tMode);
					$(".modal-title").html("Tune to");
					$(".modalI-body").html("<br>&nbsp;&nbsp;&nbsp;Enter any combination and click OK.");
					$("#myModalInput").modal('show');
					var x = document.getElementById("curFreq");
					x.value=addPeriods(parseInt(tMain));
					$.post("/programs/RadioID.php", {tRadio: tRadioName}, function(response1) {
						if (response1.length>1){
							transRadioID=response1;
						}

						$.post("/programs/getRigBandwidths.php", {myRadioName: transRadioName, myRadio: tMyRadio, myRadioID:transRadioID, mode: ""}, function(response){
							var x = document.getElementById("curPassband1");
							x.value=response;
							//("Select Passband");
						});

						$.post("/programs/getRigCaps.php", {myRadioName: transRadioName, myRadio: transRadioID, cap: "Mode list:"}, function(response){
							var tL="";
							var tList1=response;
							var tList=tList1.split(" ");
							for (i=0;i<tList.length-1;i++)  //skip last
							{
								tL=tL+"<div class='mymode' id=i<li><a class='dropdown-item' href='#'>"+tList[i]+"</a></li></div>";
							};
							var caps=response;
							x = document.getElementById("modeList");
							x.innerHTML=tL;
						});

						$.post("/programs/getRigBandwidths.php", {myRadio: transRadioID, mode: tMode}, function(response){
							var tB="";
							var tList=response.split("\t");
							for (i=1;i<tList.length;i++){
								var tB1=tList[i];
								var tB2=tB1;
								if (tB1.indexOf("kHz")>0){
									tB2=tB1.split("=");
									tB2[1]=tB2[1].replace(" kHz","");
									tB2[1]=tB2[1]*1000;
									tB2=tB2[0]+"=" + tB2[1]+" "+"Hz"
								}
								tB2=tB2.replace(".0","");
								tB=tB+"<div class='mypassband' id='"+i+"'<li><a class='dropdown-item' href='#'>"+tB2+"</a></li></div>";
							};
							x = document.getElementById("passbandList");
							x.innerHTML=tB;
						});

					});
				}else if (tPost=="T/R" && tAccessLevel<4){
					if (tDisconnected==1){
						showConnectAlert();
						return false;
					};
					if (tTrx==1){
						setPTT(0, 0);
						tTrx=0;
					}else{
						setPTT(1, 0);
					}
				}

			}
		}
	}

	$(document).on('click', '.mymode', function() {
		if (tDisconnected==1){
			showConnectAlert();
			return false;
		};
		if (tMain.indexOf('UNK')==-1  && tSub.indexOf('UNK')==-1){
			var text = $(this).text();
			$("#curMode1").val(text);
			$.post("/programs/getRigBandwidths.php", {myRadioName: transRadioName, myRadio: tMyRadio, myRadioID:transRadioID, mode: text}, function(response){
				var tB="";
				var tList=response.split("\t");
				for (i=1;i<tList.length;i++){
					var tB1=tList[i];
					if (tB1=="USB-D"){
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
					tBandWidth=tB2[1];
					tB=tB+"<div class='mypassband' id=i<li><a class='dropdown-item' href='#'>"+tB2+"</a></li></div>";
					if (tMain.indexOf('UNK')==-1 && tMain.indexOf('0000000000')==-1 && tSub.indexOf('UNK')==-1 && tMode.indexOf('UNK')==-1){				$.post("/programs/SetFrequencyMem.php",{radio: tMyRadio, main: tMain, sub: tSub, mode: tB1, bw: -1}, function(response){
									t=response;
						});
					};
				};
				x = document.getElementById("passbandList");
				x.innerHTML=tB;
			});
		}
	});

	$(document).on('click', '.mypassband', function() {
		var text = $(this).text();
		var x = document.getElementById("curPassband1");
		x.value=text;
	});

	function tuneMain(increment)
	{
		if (tDisconnected==1){
		};
		if (notGotPerfectWidgets==1){
			return;
		}
		if (tTuneFromTap=0){
			toggleSplitButton(0, 1);
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
		$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: cFreq1, table: "RadioInterface"}, function(response){
			t=response;
		});
		waitRefresh=3;
	}

	function tuneSplit(increment)
	{
		if (notGotPerfectWidgets==1){
			return;
		}
		toggleSplitButton(1, 1);
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
		waitRefresh=3;
	}

	function setBandMemory(){
		tBandwidth=-1;
		if (tMain.indexOf('UNK')==-1 && tMain.indexOf('0000000000')==-1 && tSub.indexOf('UNK')==-1 && tMode.indexOf('UNK')==-1){
			$.post("/programs/SetFrequencyMem.php", {radio: tMyRadio, main: tMain, sub: tSub, mode: tMode, bw: -1}, function(response){});
		};
	};

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

	function doDigit(whichIncrement, skipShift){
		if (tDisconnected==1){
//			return false;
		};
		if (notGotPerfectWidgets==1){
			return;
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
		waitRefresh=8;
		lu.setVisible(true);
		ld.setVisible(true);
		tLine1=ld;
		tLine2=lu;
	}

	function doSubDigit(whichIncrement, skipShift){
		if (tDisconnected==1){
			return false;
		};
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
		if (tDisconnected==1){
			showConnectAlert();
			return false;
		};
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
				};
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
			};
		};
	};
	  function addPeriods(nStr) {  //convert '14025000' to '14.025.000'
		nStr += "";
		x = nStr.split(".");
		x1 = x[0];
		x2 = x.length > 1 ? "." + x[1] : "";
		var rgx = /(\d+)(\d{3})/;
		while (rgx.test(x1)) {
		  x1 = x1.replace(rgx, "$1" + "." + "$2");
		}
		var newF = x1 + x2;
		if (newF.length > 12) {
		  newF = newF.replace(".", "");
		}
		var tF = newF;
		var tFS = "";
		if (newF != "0000.000.000") {
		  while (tF.charAt(0) == "0") {
			tF = tF.substring(1);
			tFS = tFS + " ";
			newF = tFS + tF;
		  }
		}
		return newF;
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

	</script>

	</head>
	<body class="body-black-scroll" id="tuner">
		<?php require $dRoot . "/includes/header.php"; ?>
		 <div class="container-fluid">
			 <p>
		<input class="textarea dummytext" type="tel" id="dt">
			<div class="row">
			<p>
			   <div class="col-sm-4 mx-auto" id="colPan">
					<div class="row fixed mx-auto noselect d-none"  style="margin-bottom:10px;" id="dPanel">
					</div>
					 <div class="row  fixed noselect mx-auto">
						<div class="col-12">
							 <iframe class="videoPanel embed-responsive-item d-none" id="i1" style="margin-left: -15px;" scrolling="no" width="320" height="240" src=""></iframe>
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
									<div class="row  fixed noselect mx-auto">
									<div class="col-12">
										<iframe class="videoMeter d-none embed-responsive-item" id="i2" style="margin-left: -15px;" scrolling="no" width="320" height="240" src=""></iframe>
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
									<button class="btn btn-color btn-sm btn-block" title="Select Macro Bank 1" id="myBank1" type="button">
										MACRO BANK <u>1</u>
									</button>
								</div>
									<div class='col-6 btn-padding'>
									<button class="btn btn-color btn-sm btn-block" title="Select Macro Bank 2" id="myBank2" type="button">
										MACRO BANK <u>2</u>
									</button>
								</div>
							</div>
							<div class="row" style="margin-left:20px;margin-right:20px">
								<div class='col-6 btn-padding'>
									<button class="btn btn-color btn-sm btn-block" title="Select Macro Bank 3" id="myBank3" type="button">
										MACRO BANK <u>3</u>
									</button>
								</div>
								<div class='col-6 btn-padding'>
									<button class="btn btn-color btn-sm btn-block" title="Select Macro Bank 4" id="myBank4" type="button">
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
						<div class="col-12">
							 <iframe class="videoKnob d-none embed-responsive-item" id="i3" style="margin-left: -15px;" scrolling="no" width="320" height="240" src=""></iframe>
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
	<?php if ($level !== 6) {
     require $dRoot . "/includes/modalxxx.txt";
 } ?>
	<?php if ($level !== 6) {
     require $dRoot . "/includes/modal.txt";
 } ?>
	<?php if ($level !== 6) {
     require $dRoot . "/includes/modalAlert.txt";
 } ?>
	<?php if ($level !== 6) {
     require $dRoot . "/includes/modalCancelAlert.txt";
 } ?>
	<?php if ($level !== 6) {
     require $dRoot . "/includes/modalCancelReboot.txt";
 } ?>
	<?php if ($level !== 6) {
     require $dRoot . "/includes/modalUpdate.txt";
 } ?>
	<?php if ($level !== 6) {
     require $dRoot . "/includes/modalCancelOnly.txt";
 } ?>
	<script src="/js/mscorlib.js" type="text/javascript"></script>
	<script src="/js/PerfectWidgets.js" type="text/javascript"></script>
	<script src="/Bootstrap/jquery-ui.js"></script>
	<script src="/js/jquery.ui.touch-punch.min.js"></script>
	<link rel="stylesheet" href="/Bootstrap/jquery-ui.css">
	<script src="/Bootstrap/bootstrap.min.js"></script>
	<script src="/js/nav-active.js"></script>
</html>

