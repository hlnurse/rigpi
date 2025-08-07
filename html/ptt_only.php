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
$tUserName=$_SESSION['myUsername'];
$tCall=$_SESSION['myCall'];
$dRoot = "/var/www/html";
require_once $dRoot . "/classes/Membership.php";
if (!empty($_SESSION["firstuse"])) {
  $firstUse = $_SESSION["firstUse"];
} else {
  $firstUse = 1;
}
$_SESSION["firstUse"] = 0;
$dRoot = "/var/www/html";
require $dRoot . "/programs/GetMyRadioFunc.php";
require_once $dRoot . "/classes/Membership.php";
$membership = new Membership();
$membership->confirm_Member($_SESSION['myUsername']);
require_once $dRoot . "/programs/GetUserFieldFunc.php";
$theme = 0;//getUserField($tUserName, "Theme");
  $tThemePanel = "panelNUMOrange1000.json";
  $tThemeMeter = "smeterOrange.json";
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
  $tUDPPort = $row["WSJTXPort"];
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
		<link rel="shortcut icon" href="./favicon.ico">
		<link rel="apple-touch-icon" href="./favicon.ico">
		<?php require $dRoot . "/includes/styles_PTT.php"; ?>
		<link href="./awe/css/all.css" rel="stylesheet">
		<link href="./awe/css/fontawesome.css" rel="stylesheet">
		<link href="./awe/css/solid.css" rel="stylesheet">	
		<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">

	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<script type="text/javascript">
		var resetTimer, spaceUp, oldState;
		var autoConnect=true;
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
		var tKnobLock=0, tUDPPort=2333;
		var mtrLabel="S-Meter",tID;
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
					};
				}); 
			}
			
			var tdt='#dt';
			if (tAccessLevel==10){
				$(tdt).addClass('d-none');
			}else{
				$(tdt).removeClass('d-none');
			}
				
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
				  $.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'ID', table: 'MySettings'}, function(response)
				  {
					  tID=response;
				  });
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
					if (tAccessLevel<10){

					sliderAFGainOride=response;

				
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MicLvlOride', table: 'RadioInterface'}, function(response){
							
						});
					};
				});
									
			var mtrCalField="";


					$.post("/programs/GetInfo.php", {what: 'IPAdr'}, function(response){
						  var d = new Date();
						  var n = d.getTime();

						var aData=response.split('+');
						wanIP="http://"+aData[2]+":8081"+"?"+n;
						lanIP="http://"+aData[0]+":8081"+"?"+n;
						$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'ShowVideo', table: 'MySettings'}, function(response)
						{
							showVideo=0; //response;
									
						});
					});
	
				//Main buttons
					if (notGotPerfectWidgets==0){
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
							var rec = document.getElementById('dKnob').getBoundingClientRect();
							var position=rec.top + window.scrollY - window.pageYOffset;
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
									if ($(window).width()<835 || tAccessLevel ==10){  //if small screen, show bar s-meter and keypad button
										var st=$(".status");
										  st.addClass('d-none');
										if (tAccessLevel<10){
											$("#bottomVFO").html(bVFO);
										}
										keypad=ppanel.getByName("NUMBut")
										keypad.addOnClickHandler(keypadClicked);
										$("#colPan").removeClass("col-sm-4");
										$("#colPan").addClass("col-sm-12");
										$("#colKnob").removeClass("col-sm-4");
										$("#colKnob").addClass("col-sm-12");
										$("#colVid").removeClass("col-sm-4");
										$("#colVid").addClass("col-sm-12");

									};
								
								});
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
						
						$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'Model', table: 'MySettings'}, function(response)
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
					var theme=0;//<?php echo $theme; ?>;
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

				function stopSliderScroll(){
					$('.slider').bind('touchmove', function(e) {
					  e.preventDefault();
					  return true;
					});
				};

 
				$( "#dPanel" ).mouseover(function() {
					tOverPanel=true;
					$('#dPanel').bind('mousewheel', function(e) {
						  e.preventDefault();
					});
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
					form.submit();
				};
				
				var knobOld=0;


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

				
				function keypadClicked(updateVal)
				{
				}
	
			   function plusClicked(updateVal)
				{
				}
				
				function plusPressed(which){
				}
	
				 function minusClicked()
				{
				}
				
			  function minusPressed(which){
				}
	
				function changeMacroColor(which){
				}
				
				//vfo
				$(document).on('click', '#connect', function() 
				{
					connectRadio();
				});	
				
				function setSplit(which){
				}

				function disconnectRadio(){
					return;
				}

				$(document).on('click', '#disconnect', function() 
				{
					doDisconnect();
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
				

			function setTopBand(tText){
			}

			function setMiddleBand(tText){
			}

			function getBandMemory(nBand)
			{};
			
			var tUpdate = setInterval(bearingTimer,1000)
			function bearingTimer()
			{};
			
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
	
					function doBandUp(){};
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
function logOut(){
	openWindowWithPost("/login.php", {
	status: "loggedout",
	username: tUserName});
};				
				$(document).keydown(function(e){
					var t=e.key;
					e.multiple
					var w=e.which;
					if (e.ctrlKey){
						switch(w){
							case 49: //1
								getBandMemory('160');
								break;
							case 50: //2
								getBandMemory('80');
								break;
							case 51: //3
								getBandMemory('60');
								break;
							case 52: //4
								getBandMemory('40');
								break;
							case 53: //5
								getBandMemory('30');
								break;
							case 54: //6
								getBandMemory('20');
								break;
							case 55: //7
								getBandMemory('17');
								break;
							case 56: //8
								getBandMemory('15');
								break;
							case 57: //9
								getBandMemory('12');
								break;
							case 65: //a
								getBandMemory('10');
								break;
							case 66: //b
								getBandMemory('6');
								break;
							case 67: //c
								getBandMemory('2');
								break;
							case 68: //d
								getBandMemory('125');
								break;
							case 69: //e
								getBandMemory('70');
								break;
							case 70: //f
								getBandMemory('23');
								break;
							case 83: //usbd
								$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'PKTUSB', table: "RadioInterface"});
								waitRefresh=1;
								break;
							case 76: //lsb
								$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'LSB', table: "RadioInterface"});
								waitRefresh=1;
								break;
							case 77: //am
								$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'AM', table: "RadioInterface"});
								waitRefresh=1;
								break;
							case 82: //cwr
								$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'CWR', table: "RadioInterface"});
								waitRefresh=1;
								break;
							case 84: //rtty
								$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'RTTY', table: "RadioInterface"});
								waitRefresh=1;
								break;
							case 85: //usb
								$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'USB', table: "RadioInterface"});
								waitRefresh=1;
								break;
							case 87: //cw
								$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'CW', table: "RadioInterface"});
								waitRefresh=1;
								break;
							case 88:
								logOut();	function logOut(){
									openWindowWithPost("/login.php", {
									status: "loggedout",
									username: tUserName});
								};
								break;
									logOut();	function logOut(){
										openWindowWithPost("/login.php", {
										status: "loggedout",
										username: tUserName});
									};
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
								doDisconnect()
//									disconnectRadio();
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
/*							if (e.shiftKey){
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
*/						};
					if (w>111 && w<125){
						x=w-111;
						x="F"+x+":";
						doFCheck(x);
						return false;
					};
					if (w==32 && notGotPerfectWidgets==0){
						console.log("rpt: " + tIgnoreRepeating);
						if (tIgnoreRepeating==false) { //|| tPTTLatch==1){
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

		function connectRadio(){
			tDisconnected=1;
			$.post('./programs/h.php',{test: 0, keyer: tMyKeyer, radio: tMyRadio, user: tUserName, port: tCWPort, tcpPort: "30001", rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction, UDPPort: tUDPPort},function(response){
			  $.post('./programs/hamlibDo.php', {test: 0, keyer: tMyKeyer, radio: tMyRadio, user: tUserName, port: tCWPort, tcpPort: "30001", rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction, UDPPort: tUDPPort}, function(response) {
			  });
		  });
		}
		
		
		function disconnect() 
		{
			if (tAccessLevel==10){
				return;
			}
			  tDisconnected=1;
			   $.post('./programs/disconnectRadio.php', {radio: tMyRadio, id: tID, user: tUserName, rotor: ""}, function(response) {
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
		};
		

		
		$.getScript("/js/modalLoad.js");
///check here
		function setPTT(state, pttBypass){
			if (notGotPerfectWidgets==0 && tAccessLevel !=4 ){
				var ttPTT=tMyRadioReal;
				if (tRadioModel=="Net rigctl"){
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
				if (state==1  && tPTTIsOn==0 && tDisconnected==0){	// added tDisconnected to prevent transmit when radio disconnected
//					tKnobPTT=1;
waitRefresh=4;

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
					$("#ptt").removeClass("PTTButton");
					$("#ptt").addClass("PTTButton-red");

					$("#ptt").addClass("PTTButton-red");
					$("#ptt").removeClass("PTTButton");

				}else{
					waitRefresh=4;
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
					$("#ptt").removeClass("PTTButton-red");
					$("#ptt").addClass("PTTButton");

					$("#ptt").removeClass("PTTButton-red");
					$("#ptt").addClass("PTTButton");
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
		}

		var aMCommands=[];
		//processCommand ties into /includes/buildMacros.php
		
		function processCommand(which,btn)
		{
		}
		function tuneMain(increment)
		{
}

		function tuneSplit(increment)
		{
		}

		function setBandMemory(){
		}

		function GetBandFromFrequency(nFreq)
		{
		} 
				function connectRadio1(){
								tDisconnected = 1;
					  $.post('./programs/hamlibDo_PTT.php', {test: 0, keyer: tMyKeyer, radio: tMyRadio, user: tUserName, port: tCWPort, tcpPort: "30001", rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction, UDPPort: tUDPPort}, function(response) {
									  if (tDisconnected==1){
										  return;
									  }
				});
		};

		function updateFreqDisp()
		{
			$.post('./programs/GetSelectedRadio.php', {un:tUserName}, function(response) 
			{
				$.get('/programs/GetMyRadio.php', 'f=Port&r='+response, function(response1) {
				tMyRadio=response;
				$.post('/programs/GetInterfaceIn_PTT.php',{radio: tMyRadio, un: tUserName, myCall:tMyCall }, function(response) 
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
					$("#fPanel5").text(now_hours+":"+now_minutes+'z');
					$("#fPanel4").text("User: "+tMyCall+" (" +tUserName+")");
					
	
			}
		)}
	)};
function doDisconnect(){
	tSplitOn=0;
	toggleSplitButton(tSplitOn, 1);
	var el=$('#spinner');
	  el.addClass('d-none');
	  tDisconnected=1;
   $.post("/programs/SetSettings.php", {field: "SplitOut", radio: tMyRadio, data: tSplitOn, table: "RadioInterface"});
	  $.post('./programs/disconnectRadio.php', {radio: tMyRadio, port: tRadioPort, id: tID, user: tUserName, rotor: tMyRotorRadio, instance:<?php echo $_SESSION['myInstance'];?>}, function(response) {
	  var pl=$('#play');
		pl.removeClass('d-none');
		var pl=$('#playOK');
		pl.addClass('d-none');
	});
	waitRefresh=4;
	
}
		function updateTimer()
		{
			if (autoConnect==true ){
				doDisconnect();
				autoConnect=false;
				connectRadio();
				$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: "OFF", table: "RadioInterface"});
				$.post('/programs/GetInterfaceIn_PTT.php',{radio: tMyRadio, un: tUserName, myCall:tMyCall });
				setPTT(0,0);
				waitRefresh=4;
				return;
			}
			if (waitRefresh>0)
			{
				waitRefresh=waitRefresh-1;
				return;
			}
				if(tTrx==0)
				{	   
					$.post('/programs/GetInterfaceIn_PTT.php',{radio: tMyRadio, un: tUserName, myCall:tMyCall }, function(response) 
					{
					var tAData=response.split('`');
					tTrx=tAData[9];
					tPTT=tAData[7];
					if (tTrx==1){
						return;
					}
					if (waitRefresh==0){
						$("#ptt").removeClass("PTTButton-red");
						$("#ptt").addClass("PTTButton");
					}
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
					$.post("/programs/SetSettings.php", {field: "IsAlive", radio: tMyRadio, data: "0", table: "RadioInterface"});
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
						}else{
							$.post('/programs/GetInterfaceIn_PTT.php',{radio: tMyRadio, field: 'SplitOut'}, function(response) 
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
					}else{
						tDisconnected=0;
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
						$("#ptt").addClass("PTTButton-red");
						$("#ptt").removeClass("PTTButton");
						if (tPTT==1 && spacePTT==0){
							setPTT(1,0);
						}
					}

					//Start PTT = momentary ***					
					if (tPTTLatch==2 && waitRefresh==0){
						if (ptt.getPressed()==false){
							if (tBusy==1){
								$("#ptt").removeClass("PTTButton-red");
								$("#ptt").addClass("PTTButton");
								return;
							}else{
								$("#ptt").removeClass("PTTButton-red");
								$("#ptt").addClass("PTTButton");
							}
						}else{
							var xo=ptt.getPressed();
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
							tPTT=tAData[9];
							if (tPTT==1 || spacePTT==1){
//								setPTT(0, 0);
							}
						}else{
//							setPTT(1,1);
						}
					}
					//End PTT = latch
					if (tPTT==0 ){
					}
				});
			}else{
				$.post('/programs/GetInterfaceIn_PTT.php',{radio: tMyRadio, un: tUserName, myCall:tMyCall }, function(response) 
				{
					var tAData=response.split('`');
					tTrx=tAData[9];
					tPTT=tAData[7];
					if (tTrx==0){
						tPTT=0;
						setPTT(0,0);
						return;
					}
					if (waitRefresh==0){
						$("#ptt").addClass("PTTButton-red");
						$("#ptt").removeClass("PTTButton");
//							ptt1.setVisible(true);
						ptt.setVisible(false);
					}else{
						waitRefresh=waitRefresh-1;
					}
			//Start PTT=momentary in transmit5
			if (tPTTLatch==2 && waitRefresh==0){
				tBusy=tTrx;
				console.log("new " + tPTT);
				var tPr=ptt.getPressed();
//				if (tPr==false){
//					if (tBusy==1){
				if (tKnobPTT==0){
					if (tBusy==1){
						if (tKnobPTT==0 && trOn==0 && spacePTT==0){
							setPTT(1, 1);
						}else{
							setPTT(1,0);
						}
					}else{
						setPTT(0, 0);
					}
				}else{
					if (tTrx==1){
						var xo=tPTTIsOn;
						if (xo==false ){
							setPTT(0, 0);
			//							tKnobPTT=0;
							tBusy=0;
							$.post("/programs/SetSettings.php", {field: "PTTOut", radio: tMyRadio, data: "0", table: "RadioInterface"});
						}else if(spacePTT==1){
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

$(document).mouseup(function()
{
tPTTIsOn=0;
});

$(document).on('mousedown', '#ptt', function()
{
	if (tDisconnected==1){
		return;
	}
	if (xmit==true ){
		xmit=false;
		$("#ptt").removeClass("PTTButton-red");
		$("#ptt").addClass("PTTButton");
		setPTT(0,0);
	}else{
		xmit=true;
		$("#ptt").removeClass("PTTButton");
		$("#ptt").addClass("PTTButton-red");
		setPTT(1,0);
		tKnobPTT=1;
		tPTTIsOn=1;
	};
});	


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
		}
		
		function doSubDigit(whichIncrement, skipShift){};
		
		function numTune(num){
		};
			
	</script>

	</head>
	<body class="body-black" id="tuner">
		<?php require $dRoot . "/includes/footer.php"; ?>
		<?php if ($level < 10) {
 require $dRoot . "/includes/header.php";
} ?>
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
								<?php if ($level < 10) {echo $tCall;} ?>
								<hr>
							 </div>
							<div class="row" style="margin-left:20px;margin-right:20px">
								<div class='col-6 btn-padding'>
									<button class="d-none btn btn-color btn-sm btn-block" title="Change Bank" id="myBank1" type="button">
										MACRO BANK <u>1</u>
									</button>
								</div>
									<div class='col-6 btn-padding'>
									<button class="d-none btn btn-color btn-sm btn-block" title="Change Bank" id="myBank2" type="button">
										MACRO BANK <u>2</u>
									</button>
								</div>
							</div>
							<div class="row"style="margin-left:20px;margin-right:20px">
								<div class='col-6 btn-padding'>
									<button class="d-none btn btn-color btn-sm btn-block" title="Change Bank" id="myBank3" type="button">
										MACRO BANK <u>3</u>
									</button>
								</div>
								<div class='col-6 btn-padding'>
									<button class="d-none btn btn-color btn-sm btn-block" title="Change Bank" id="myBank4" type="button">
										MACRO BANK <u>4</u>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-sm-4 mx-auto" id="colKnob">
					<div class="row fixed mx-auto noselect d-none" style="margin-left:-10px;" id="dKnob">
					</div>
					<div class="row  fixed mx-auto">
						<div class="col d-none embed-responsive-item" id="videoKnob">
							<img class="rigvid" src="" alt="RigVideo" style="margin-left:-10px;width:320px;height:240px;" id="i3">
						</div>
						<div class="col-2 col-sm-4"></div>
							<div class="col-8 col-sm-4" id="colKnob">
								<button class='btn btn-color block PTTButton' data-toggle="button" aria-pressed="true" id='ptt' type='button'>
								<span style="font-size:30px; background: rgba(0,0,0,0); color: gray">RigPi</span><br><span style="font-size:40px; background: rgba(0,0,0,0); color: #ffffff;">PTT</span>
								</button>
							</div
						<div class="col-2 col-sm-4"></div>
						</div>

					</div>

					<div class="row fixed mx-auto noselect d-none" id="dLED"></div>
				</div>

			<div class="row" style="margin-top:10px">
				<div class="col-12" id="bottomVFO">
				</div>
			</div>
			<hr>
		   <div id="macroDiv">
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
		<?php require $dRoot . "/includes/styles_PTT.php"; ?>
	<script src="./Bootstrap/jquery-ui.js"></script>
	<script src="./Bootstrap/bootstrap.min.js"></script>
		<script src="/js/nav-active.js"></script>
</html>
	