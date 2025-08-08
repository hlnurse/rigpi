<?php
/*
 * RigPi Keyer
 *
 * Copyright (c) 2025 Howard Nurse, W6HN
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
*/
session_start();
$tUserName=$_SESSION['myUsername'];
$tCall=$_SESSION['myCall'];
$dRoot="/var/www/html";
require_once $dRoot . "/classes/Membership.php";
$membership = new Membership();
$membership->confirm_Member($tUserName);
require_once "/var/www/html/classes/MysqliDb.php";
require "/var/www/html/programs/sqldata.php";
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo $tCall; ?> RigPi Keyer</title>
	<meta name="description" content="RigPi Keyer">
	<meta name="author" content="Howard Nurse, W6HN">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="/Bootstrap/bootstrap.min.css">
	<script src="/Bootstrap/jquery.min.js" ></script>
	<link href="/awe/css/all.css" rel="stylesheet">
	<link href="/awe/css/fontawesome.css" rel="stylesheet">
	<link href="/awe/css/solid.css" rel="stylesheet">
	<link rel="stylesheet" href="/Bootstrap/jquery-ui.css">

	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="apple-touch-icon" href="/apple-touch-icon.png">
	<script type="text/javascript">
		var tMyCall="<?php echo $tCall; ?>";
		var holdText="";
		var char="";
		var holdCW=false;
		var speedPot=0;
		var clearingLeft=0;
		var cwSent="1";
		var test='';
		var tMyRadio="1";
		var tMyKeyer="rpk";
		var tUserName="<?php echo $tUserName; ?>";
		var tCall="<?php echo $tCall; ?>";
		var tUser='';
		var trOn=0;
		var xmit=false;
		var tSpeedRange=30;
		var tSpeed='';
		var tMinSpeed=5;
		var ptt;
		var ptt1;
		var cRX;
		var tMyPTT=1;
		var tAccessLevel="<?php echo $level; ?>";
		var tRadioPort=0; //used to redirect CW keying to actual radio port
		var mBank=1, tBand, tBandOld;
		var aMCommands=[];
		var tDisconnected=0;
		var trXmit=false;
		var tMyRotorRadio=1; //ve9gj
		var tRadioModel="";
		var tRotateButton;
		var tCurBeam=0, tPost, tPost1, tPost2, tAData=[], tBandWidth;
		var waitRefresh=0, tTuneOn=0;
		var doUSBUpdate=1;
		var AFGainOld=0, USBAFGain, USBAFGainOld=0;
		var outputAF, sliderAF, outputPwrOut, sliderPwrOut, sliderMic, outputMic, outputRF, sliderRF, tVal;
		var tSliderVal, tSliderStartVal;
		var sliderSpeedRef=0, sliderAFRef, sliderRFRef, sliderPwrOutRef, sliderMicRef, tMain, tMax
		var sliderAFGainOride, sliderRFGainOride, sliderPwrOutOride, sliderMicLvlOride;
		var latchBtn1=[],latchBtn2=[],latchBtn3=[],latchBtn4=[],latchBtn=[], btnLatchColor;
		var bEnable=[],bModeEnable=[],aMacros=[],tMode='', tModeOld='', tMain='',tSpeedOriginal,outputSpeed='';
		var supportsUSB_AF=0,tID,spaceTimer=0,tShowCWOut=1,tTrx=0,speedPotEnable=0;
		var speedLock=0, tSpeedCont, tSpeedSl;
		let modeList, transRadioName, transRadioID;
		tKeyerMode=0;
		$(document).ready(function() {
			outputSpeed = document.getElementById("myKeyerSpeedVal");

			tSpeedCont=document.getElementById("sliderSpeed");
			tSpeedSl=document.getElementById("containerSpeed");
			document.getElementById("cwi").focus();
			btnLatchColor="btn-warning";
			var tT=$("#fPanel1").text().trim();
			if (tT==="No Radio"){
				tDisconnected=1;
			}else{
				tDisconnected=0;
			}

			outputAF = document.getElementById("myAFVal");
			sliderAF = document.getElementById("sliderAF");
			outputRF = document.getElementById("myRFVal");
			sliderRF = document.getElementById("sliderRF");
			outputPwrOut = document.getElementById("myOutputPwrVal");
			sliderPwrOut = document.getElementById("sliderPwrOut");
			outputMic = document.getElementById("myMicVal");
			sliderMic = document.getElementById("sliderMic");

			outputSpeed.innerHTML="";
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
				  var tMyRadioPort=response1;
				  if (tMyRadioPort>4530 && tMyRadioPort<5000){
//						  var tMyRadioCalc=1+(tMyRadioPort-4532)/2;
//						  if (tMyRadioCalc>0){
//							tMyRadio=tMyRadioCalc;
//						}else{
						tMyRadio=response;
//						}
				  }else{
					  tMyRadio=response;
					  if (!tMyRadio.isNumeric){
						  tMyRadio=1;
					  }
				  }
				  $.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKRemotePort', table: 'Keyer'}, function(response){
//						if (response>0){
//							tShowCWOut=0;
//						}else{
							tShowCWOut=1;
//						}
					});

				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKSpeedPotEnable', table: 'Keyer'}, function(response){
					  speedPotEnable=response;
					  if (speedPotEnable==1){
						  $(tSpeedCont).removeClass('d-none');
						  $(tSpeedSl).addClass('d-none');
						  $(outputSpeed).css('margin-top',0);
					  }else{
						  $(tSpeedCont).addClass('d-none');
						  $(tSpeedSl).removeClass('d-none');
						  $(outputSpeed).css('margin-top','10px');

					  }
				});

				  $.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'RadioName', table: 'MySettings'}, function(response)
				  {
					  tRadioName=response;
					  transRadioName=tRadioName;
					  $.post("/programs/GetUserField.php", {un:tUserName, field:'ModeEnable'}, function(response)
					  {
						  if (response==""){
							  response="1,1,1,1,1,1,1,1,1";
						  }
						  bModeEnable=response.split(",");
						  updateModeButtons();
					  });
				  });

					  updateModeButtons();
					  $.post('/programs/RadioID.php', {tRadio: transRadioName}, function(response) {
						  if (response.length>0){
							  transRadioID=response;
						  }
					});
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
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKSpeed', table: 'Keyer'}, function(response){
										  tSpeed=response;
				//						  updateButtons();
									  });
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKSpeedOriginal', table: 'Keyer'}, function(response){
						tSpeedOriginal=response;
						updateButtonLabels('/OLD', tSpeedOriginal);//response);
//?							loadMacroBank(1);
//>							updateBank(1);
					});



				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'AFGainOride', table: 'RadioInterface'}, function(response){
					sliderAFGainOride=response;
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'AFGain', table: 'RadioInterface'}, function(response){
						$( function() {
							$("#sliderAF").slider({
								min: 0,
								max:sliderAFGainOride,
								range: 'min'
							});
							outputAF.innerHTML = response;
							updateButtonLabels('RADIO AF MUTE', response-1);
							$("#sliderAF").slider('value',response);
						});
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
							updateButtonLabels('RADIO AF MUTE', tV);
						};
						$("#sliderAF").on("slide",function(event,ui){
							waitRefresh=8;
							tVal=$("#sliderAF").slider('value');
							var tV=tVal;
							if (tV>0 && tV<100) tV=tV-1;
							outputAF.innerHTML =tV;
							sliderHandle=ui.handle;
						});
					});
					$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'Keyer', table: 'MySettings'}, function(response)
					{
						tMyKeyer=response;
					});
					$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'ID', table: 'MySettings'}, function(response)
					{
						tID=response;
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
					});

					});

				function updateBank(which){
					var mB='#myBank'.trim()+mBank;
					var tNum="Macro <u>B</u>ank "+which;
					document.getElementById('myBank').innerHTML=tNum;
					$(mB).removeClass(btnLatchColor);
					$(mB).addClass('btn-color');
					mBank=which;
					loadMacroBank(mBank);
					var mB='#myBank'.trim()+mBank;
					$(mB).removeClass('btn-color');
					$(mB).addClass("btn-info");
					$.post("/programs/SetSettings.php", {field: "MacroBankKeyer", radio: tMyRadio, data: mBank, table: "RadioInterface"});
				};

				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MacroBankKeyer', table: 'RadioInterface'}, function(response){
//						mBank=response
					document.getElementById('myBank').innerHTML="Macro <u>B</u>ank "+response;
//.						updateBank(mBank);
					mBank=response
//?						loadMacroBank(mBank);
					var mB='#myBank'.trim()+mBank;
					$(mB).removeClass('btn-color');
					$(mB).addClass('btn-info');
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

				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'Keyer', table: 'MySettings'}, function(response)
				{
					$('#myKeyer span').text("CW "+response);
				});

				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
				{
					$('#searchText').val(response.toUpperCase());
				});

				$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'Model', table: 'MySettings'}, function(response)
				{
					tRadioModel=response;
				});

				$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKPot'}, function(response) {
					speedPot=response;
				$.post('./programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKSpeed'}, function(response) {
////					tSpeed=response;
					updateButtonLabels("/OLD", tSpeedOriginal);//tSpeed);
					$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tSpeed, table: "Keyer"});
					$.post("/programs/SetSettings.php", {field: "CWChangeCk", radio: tMyRadio, data: 1, table: "RadioInterface"});
					waitRefresh=2;
				});
				$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKMinWPM'}, function(response) {
					tMinSpeed=parseInt(response);
					$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKWPMRange'}, function(response) {
						tMinSpeedRange=response;
						$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKSpeed'}, function(response)
						{
							tMax=tMinSpeed+parseInt(tMinSpeedRange);
//								console.log("min: "+tMinSpeed);
							if (speedPotEnable==0){
								$("#sliderSpeed").removeClass('d-none');
							$(function() {
								$("#sliderSpeed").slider({
									min: tMinSpeed,
									max: tMax,
									range: 'min',
									animate: true
								});
								$("#sliderSpeed").slider('value',response);
								outputSpeed.innerHTML = '<H3b>'+response+" WPM"+"</H3b>";
//								outputSpeed.innerHTML=tSpeed+" WPM"+' (Use Speed Pot)';

								for (i = 0; i < 32; i++) {
									x=aMacros[i];
									if (x.indexOf("/OLD")>-1){
										var mB="m" + i + "Button";
										y=x.split("|");
										if (document.getElementById(mB)){
											document.getElementById(mB).innerHTML=y[0] + " (" + response + ")";
										}
									};
								};

							});
						}else{
							$("#sliderSpeed").addClass('d-none');
						}
							if (speedPotEnable==0){
							$("#sliderSpeed").on("click", function(){
								waitRefresh=2;
//									if (tSliderStartVal<tSliderVal){
									tVal=$('#sliderSpeed').slider('value');
									tSpeedOriginal=tVal;
									if (tVal<tMinSpeed){
//											tVal=tMinSpeed;
									}
									if (tVal>tMax){
//											tVal=tMax;
									}
									outputSpeed.innerHTML='<H3b>'+tVal+" WPM" + "</H3b>";
//									outputSpeed.innerHTML=tSpeed+" WPM"+' (Use Speed Pot)';


//										$("#mySpeed").text(tVal+' WPM');
									for (i = 0; i < 32; i++) {
										x=aMacros[i];
										if (x.indexOf("/OLD")>-1){
											var mB="m" + i + "Button";
											y=x.split("|");
											if (document.getElementById(mB)){
												document.getElementById(mB).innerHTML=y[0] + " (" + tVal + ")";
											}
										};
									};

									$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tVal, table: "Keyer"}, function(response){
										tSpeed=tVal;
										});
									$.post("/programs/SetSettings.php", {field: "WKSpeedOriginal", radio: tMyRadio, data: tSpeedOriginal, table: "Keyer"}, function(response){
										});
									$.post("/programs/SetSettings.php", {field: "CWChangeCk", radio: tMyRadio, data: "1", table: "RadioInterface"});
//									if (speedPotEnablde==1){
										sliderSpeedRef=tVal
//									}
//									}
//										console.log("click: "+tVal);
/////
									$("#cwi").focus();
							});

							$("#sliderSpeed").on("slide",function(event,ui){
								waitRefresh=2
//									$("#mySpeed").text(tVal+' WPM');
								if (speedPotEnable==0){
									tVal=$('#sliderSpeed').slider('value');
									outputSpeed.innerHTML='<H3b>'+tVal+" WPM</H3b>";
									tSliderVal=tVal;
									$("#sliderSpeed").slider('value',tVal);
								}
								console.log("slide: "+tVal);
								for (i = 0; i < 32; i++) {
									x=aMacros[i];
									if (x.indexOf("/OLD")>-1){
										var mB="m" + i + "Button";
										y=x.split("|");
										if (document.getElementById(mB)){
											document.getElementById(mB).innerHTML=y[0] + " (" + tVal + ")";
										}
									};
								};

								$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tVal, table: "Keyer"});
								$.post("/programs/SetSettings.php", {field: "WKSpeedOriginal", radio: tMyRadio, data: tVal, table: "Keyer"}, function(response){
									});
								$.post("/programs/SetSettings.php", {field: "CWChangeCk", radio: tMyRadio, data: "1", table: "RadioInterface"});
								sliderSpeedRef=tVal

//									$("#cwi").focus();
								});
							}else{
								$("#sliderSpeed").enabled=false;
							}
						});
					});
				});
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'USBAFGain', table: 'RadioInterface'}, function(response){
//						USBAFGain=response;
				});
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'USBAFGainOld', table: 'RadioInterface'}, function(response){
					USBAFGainOld=response;
					USBAFGain=response;
					doUSBUpdate=1;
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
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MacroBankKeyer', table: 'RadioInterface'}, function(response){
					mBank=response
					document.getElementById('myBank').innerHTML="Macro <u>B</u>ank "+response;
					loadMacroBank(mBank);
//.						updateBank(mBank);
				})

				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'Keyer', table: 'MySettings'}, function(response)
				{
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKFunction', table: 'Keyer'}, function(response1)
					{
						tKeyerMode=response1;
						var tKeyer=response;
						var tKeyerOut="";
						var tKeyerIn="";
						if (response=="via CAT"){
							tKeyerOut="CW via CAT not shown"
							tKeyerIn="CW to be Sent"
							clearLeft();
						}else if(tKeyer=="RigPi Keyer"){
							if (response1==1 || tShowCWOut==0){
								tKeyerOut="Station CW not shown"
							}else{
								tKeyerOut="Sent CW"
							}
							tKeyerIn="CW to be Sent"
						}else if(tKeyer=="WinKeyer"){
							tKeyerOut="Sent CW"
							tKeyerIn="CW to be Sent"
						}else if(tKeyer=="None"){
							tKeyerOut="No Keyer"
							tKeyerIn="No Keyer"
						}
						$("#myOut").text(tKeyerOut);
						$("#myIn").text(tKeyerIn);
					});
				});

				$(document).on('click', '#logoutButton', function()
				{
					openWindowWithPost("/login.php", {
						status: "loggedout",
						username: tUserName});
				});
				$.get('/programs/GetMyRadio.php', 'f=PTTMode&r='+tMyRadio, function(response) {
					  tMyPTT=response;
				});

				function loadMacros(which){
					$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'Macros'+which}, function(response)
					{
						var tMacros=decodeURIComponent((response+'').replace(/\+/g,'%20'));
						aMacros=tMacros.split('~');
						var mBtn;
						for (i = 0; i < 32; i++) {
							var mID='m'+i+'Button';
							var tLabel = aMacros[i].trim();
							tLabel=tLabel.split('|');
							var btn =document.getElementById(mID);
							btn.innerHTML=tLabel[0];
							document.getElementById('myBank').innerHTML="Macro <u>B</u>ank "+which;
							aMCommands.push(tLabel[1]);
						}
						if (tLabel[1].trim()=="!BANK"){
							mBtn=btn;
							mBtn.innerHTML="BANK "+which;
						};
					});
				};
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MacroBankKeyer', table: 'RadioInterface'}, function(response){
						mBank=response
						loadMacros(mBank);
				})
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
					$.post("/programs/SetSettings.php", {field: "waitReset", radio: tMyRadio, data: 1, table: "RadioInterface"});

					return false;
				} else  {
					return true;
				}
			});
		});
//	});
			<?php require $dRoot . "/includes/buildMacros.php"; ?>

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
				if (w==229){
					return;
				}
				if (w==191 && $("#cwi").focus()==false)
				{
					if (e.shiftKey){
						<?php require $dRoot . "/includes/shortcutsKeyer.php"; ?>
						$("#modalCO-body").html(tSh);
						$("#modalCO-title").html("Shortcut Keys");
						  $("#myModalCancelOnly").modal({show:true});
						  return false;
					}else{
						var tS1=document.activeElement.tagName;
						if (tS1=='INPUT'){
							return true;
						}else{
//							$("#searchText").focus();
							return false;
						}
					}
				};
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
							document.getElementById("fmButton").click();
							break;
						case 71: //g (f used for FM)
							document.getElementById("23Button").click();
							break;
						case 83: //usbd
							document.getElementById("usbdButton").click();
							break;
						case 76: //lsb
							document.getElementById("lsbButton").click();
							break;
						case 77: //lsb
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
							document.getElementById("skButton").click();
							break;
						case 50: //2
							document.getElementById("knButton").click();
							break;
						case 51: //3
							document.getElementById("arButton").click();
							break;
						case 52: //4
							document.getElementById("btButton").click();
							break;
						case 53: //5
							document.getElementById("rrButton").click();
							break;
						case 54: //6
							document.getElementById("atButton").click();
							break;
						case 65: //a
							showCalendar();
							e.preventDefault();
							break;
						case 66: // b
							document.getElementById("myBank").click();
							e.preventDefault();
							break;
						case 69: // e
							showSettings();
							e.preventDefault();
							break;
						case 70: //f
							document.getElementById("clearLeftButton").click();
							e.preventDefault();
							break;
						case 72: // h
							showHelp();
							e.preventDefault();
							break;
						case 71: //g
							document.getElementById("clearRightButton").click();
							e.preventDefault();
							break;
						case 75: //k
							var win="/keyer.php?x="+tUserName+"&c="+tCall;
							window.open(win, "_self");
							break;
						case 76: //l
							var win="/log.php?x="+tUserName+"&c="+tCall;
							window.open(win, "_self");
							break;
						case 79: //O
							document.getElementById("holdButton").click();
							e.preventDefault();
							break;
						case 82: //R
							document.getElementById("repeatButton").click();
							e.preventDefault();
							break;
						case 83: // s
							var win="/spots.php?x="+tUserName+"&c="+tCall;
							window.open(win, "_self");
							break;
						case 84: // t
							var win="/index.php?x="+tUserName+"&c="+tCall;
							window.open(win, "_self");
							break;
						case 87: // w
							var win="/web.php?x="+tUserName+"&c="+tCall;
							window.open(win, "_self");
							break;
					}
					return false
				}
				if (w>111 && w<125){
					x=w-111;
					x="F"+x+":";
					doFCheck(x);
				};
			});
			$(document).on("click", "#searchButton", function () {
						  var dx = $("#searchText").val().toUpperCase();
						  if (dx.length == 0 || ~dx.indexOf("*") || ~dx.indexOf("=")) {
							return;
						  }
			//			  tUserName='admin';
			//			  tMyRadio=1;
						  $.post(
							"/programs/GetUserField.php",
							{ un: tUserName, field: "uID" },
							function (response) {
							  tUser = response;
							  $.post("/programs/SetSettings.php", {
								field: "waitReset",
								radio: tMyRadio,
								data: 1,
								table: "RadioInterface",
							  });

							  $.post(
								"./programs/GetCallbook.php",
								{ call: dx, what: "QRZData", user: tUser, un: tUserName },
								function (response) {
								  $(".modal-body").html(response);
								  $.post(
									"./programs/GetCallbook.php",
									{ call: dx, what: "QRZpix", user: tUser, un: tUserName },
									function (response) {
									  var aPix = response.split("|");
									  var h = aPix[1];
									  var w = aPix[2];
									  if (h > 0) {
										var wP = aPix[2] / 280;
										var tW = w / wP;
										var tH = h / wP;
										$(".modal-pix").attr("height", tH + "px");
										$(".modal-pix").attr("width", tW + "px");
										$(".modal-pix").attr("src", aPix[0]);
									  } else {
										$(".modal-pix").attr("height", "0px");
										$(".modal-pix").attr("width", "0px");
										$(".modal-pix").attr("src", "about:blank");
									  }
									  $(".modal-title").html(dx);
									  $("#myModal").modal({ show: true });
									}
								  );
								}
							  );
							  $.post("./programs/SetSettings.php", {
								field: "DX",
								radio: tMyRadio,
								data: dx,
								table: "MySettings",
							  });
							}
						  );
						});
			$(document).keyup(function(e)
			{
				if (e.keyCode == 27)
				{ // escape key maps to keycode `27`
					holdText='';
					var tNum=String.fromCharCode(10);
					$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: tNum, table: "RadioInterface"});
					setPTT(0);
//					$.post("./programs/SetSettings.php", {field: "CommandOut", radio: tMyRadio, data: "*\\stop_morse", table: "RadioInterface"});
					document.getElementById("cwi").focus();
				}
//				var cwIText=$('#cwi').val();
				if (e.ctrlKey || e.altKey){
					return;
				}
//				$('#cwi').keydown(function(e){
					var tWhich=e.keyCode;
					tC1=e.key.toUpperCase();

					if ((tWhich>47 && tWhich<91) || (tWhich>187 && tWhich < 192) || tWhich==32 || tWhich==8){ //javascript keycodes!
						if (tWhich==8){
							sendCWMessage("!"); //backspace is ! so chars in output buffer can be replaced
							return;
						}else{
							var dummyEl = document.getElementById('searchText');
							var dummyEl1 = document.getElementById('curFreq');
							var isFocused = (document.activeElement === dummyEl || document.activeElement===dummyEl1);
							if (isFocused == false){
								sendCWMessage(tC1);
							}
						}
					}
//				)};
			});

			$(document).on('click', '#clearRightButton', function() {
				var input = document.getElementById("cwi");
				input.value='';
				holdText='';
				var tNum=String.fromCharCode(10);
				$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: tNum, table: "RadioInterface"});
//				$.post("./programs/SetSettings.php", {field: "CommandOut", radio: tMyRadio, data: "*\\stop_morse", table: "RadioInterface"});
				document.getElementById("cwi").focus();
			});
			$(document).on('click', '#clearLeftButton', function() {
				clearLeft();
				document.getElementById("cwi").focus();
			});
			$(document).on('click', '#btButton', function() {
				sendCWMessage("=");
				document.getElementById("cwi").focus();
			});
			$(document).on('click', '#arButton', function() {
				if (tMyKeyer=="via CAT"){
					sendCWMessage("AR");
				}else{
					sendCWMessage("<");
				}
				document.getElementById("cwi").focus();
			});
			$(document).on('click', '#skButton', function() {
				if (tMyKeyer=="via CAT"){
					sendCWMessage("SK");
				}else{
					sendCWMessage(">");
				}
				document.getElementById("cwi").focus();
			});
			$(document).on('click', '#knButton', function() {
				if (tMyKeyer=="via CAT"){
					sendCWMessage("KN");
				}else{
					sendCWMessage("(");
				}
				document.getElementById("cwi").focus();
			});
			$(document).on('click', '#atButton', function() {
				sendCWMessage("@");
				document.getElementById("cwi").focus();
			});
			$(document).on('click', '#rrButton', function() {
				sendCWMessage("<1b>BK");
				document.getElementById("cwi").focus();
			});

			$(document).on('click', '#holdButton', function() {
				if (holdCW==false){
					holdCW=true;
					$("#myHold").text('HOLD'+'\xa0');
					toggleHoldButton(1);
					$.post("/programs/SetSettings.php", {field: "CWOutCk", radio: tMyRadio, data: "1", table: "RadioInterface"});
				}else{
					holdCW=false;
					$.post("/programs/SetSettings.php", {field: "CWOutCk", radio: tMyRadio, data: "0", table: "RadioInterface"});
					$("#myHold").text('');
					toggleHoldButton(0);
				}
				document.getElementById("cwi").focus();
			});

			function toggleHoldButton(hold)
			{
				if (hold==1)
				{
					$('#holdButton').removeClass('btn-color');
					$('#holdButton').removeClass('btn-primary');
					$('#holdButton').addClass('btn-danger');
				}
				else
				{
					$('#holdButton').removeClass('btn-danger');
					$('#holdButton').addClass('btn-color');
					$('#holdButton').addClass('btn-primary');
				}
			}

			$(document).on('click', '#repeatButton', function() {
				var input = document.getElementById("cwi");
				var tText=input.value;
				$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: tText, table: "RadioInterface"});
					document.getElementById("cwi").focus();
			});
			$(document).on('click', '#escButton', function() {
				var tNum=String.fromCharCode(10);
				$.post("/programs/ConcatSettings.php", {field: "CWIn", radio: tMyRadio, data: tNum, table: "RadioInterface"});
//				$.post("./programs/SetSettings.php", {field: "CommandOut", radio: tMyRadio, data: "*\\stop_morse", table: "RadioInterface"});
				document.getElementById("cwi").focus();
			});
			$(document).on('click', '#tuneButton', function() {
				var tNum=String.fromCharCode(11)+parseInt(1);
				$.post("/programs/ConcatSettings.php", {field: "CWIn", radio: tMyRadio, data: tNum, table: "RadioInterface"});///
			});

			$(document).on('click', '#myBank', function()
			{
				mBank=parseInt(mBank)+1;
				if (mBank==5){mBank=1};
				var tNum="Macro <u>B</u>ank "+mBank;
				document.getElementById('myBank').innerHTML=tNum;
				loadMacroBank(mBank);

				$.post("/programs/SetSettings.php", {field: "MacroBankKeyer", radio: tMyRadio, data: mBank, table: "RadioInterface"});
				$("#cwi").focus();
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
			function updateButtons(){
				if (tDisconnected==1){
					showConnectAlert();
					return false;
				}
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
						$(bName).addClass("btn-success");
					}
				}
			}


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

			$.getScript("/js/modalLoad.js");
			$(document).on('click', '#macroButton', function(){
				var win="./macros.php";
				window.open(win, "_self");
			});
			$(document).on('click', '#modeButton', function(){
				var win="./modeFilter.php";
				window.open(win, "_self");
			});
			$(document).on('click', '#bandButton', function(){
				var win="./bandFilter.php";
				window.open(win, "_self");
			});

		}); //end ready

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


		function setPTT(state){
			if (tAccessLevel<4){
				if (state==1){
					xmit=true;
					trXmit=true;
					$.post("/programs/SetSettings.php", {field: "PTTOut", radio: tMyRadio, data: "1", table: "RadioInterface"});
					$.post("/programs/SetSettings.php", {field: "Transmit", radio: tMyRadio, data: "1", table: "RadioInterface"});
					if (tMyPTT==1){
						$.post('/programs/doGPIOPTT.php', {PTTControl: "on"});
					}
				}else{
					xmit=false;
					trXmit=false;
					tTrx=0;
					trOn=0;
					$.post("/programs/SetSettings.php", {field: "PTTOut", radio: tMyRadio, data: "0", table: "RadioInterface"});
					$.post("/programs/SetSettings.php", {field: "Transmit", radio: tMyRadio, data: "0", table: "RadioInterface"});
					  if (tMyPTT==1){
						  $.post('/programs/doGPIOPTT.php', {PTTControl: "off"});
					  }
				}
			}
		}

		function clearLeft()
		{
			clearingLeft=1;
			$.post("/programs/SetSettings.php", {field: "CWOut", radio: tMyRadio, data: "", table: "RadioInterface"}, function(response){
				var input = document.getElementById("cwo");
				input.value='';
				$("#cwi").focus();
				clearingLeft="0";

			});
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
					var tLabel = aMacros[i].trim();
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
					if (tLabel[0].substring(0,6)=="ROTATE" && tLabel[0].indexOf("STOP")==-1){
						tRotateButton=btn;
						mBtn=btn;
						mBtn.innerHTML=tLabel[0]+ " (" + tCurBeam+")";

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
//								USBAFGain=0;
								updateButtonLabels('RADIO AF MUTE', tV-1);
								if (USBAFGain<100 && USBAFGain>0){ USBAFGain=USBAFGain-1;
								};
								updateButtonLabels('RADIO USB MUTE', USBAFGain);

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
		}
		function showConnectAlert(){
			$("#modalA-body").html("<br>The radio is not connected.<p><p>");
			$(".modalA-title").html("Radio Connection");
			  $("#myModalAlert").modal({show:true});
			  setTimeout(function(){
				  $("#myModalAlert").modal('hide');
			 },
			  2000);
			return;
		}
		function addPeriods(nStr) {
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


		function processCommand(which, btn)
		{
			if (tDisconnected==1  && (which !== "*PS1;" && which !== "*PS0;")){
				showConnectAlert();
				return false;
			}
			var tMe=tCall;
			var tWhat = which.replace(/'\*/g, tMe);
			which = tWhat.replace(/'X/g,$('#searchText').val());
			var tPre=which.substring(0, 1);
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
					if (tPost.indexOf("+")>0){
						tPost=tPost.substring(0,tPost.indexOf("+"));
					}
					USBAFGain=USBAFGainOld;
					$(btn).removeClass(btnLatchColor);
					$(btn).addClass("btn-color");
					lt1=latchBtn.join(",");
					if (tPost.indexOf('L AF')>-1){
						$.post("/programs/SetSettings.php", {field: "AFGain", radio: tMyRadio, data: AFGainOld, table: "RadioInterface"}, function(response){
							waitRefresh=1;
							var tOK=response;
							which=tPre+tPost;
							var tLat="latchBtn"+mBank;
							$.post("/programs/SetSettings.php", {field: tLat, radio: tMyRadio, data: lt1, table: "RadioInterface"}, function(response){
								});
						});
					}else{
						if (supportsUSB_AF==1  && tPost.indexOf('USB_AF') > -1){
							$.post("/programs/SetSettings.php", {field: "USBAFGainOld", radio: tMyRadio, data: USBAFGainOld, table: "RadioInterface"}, function(response){
								//USBAFGainOld=response;
								var tOK=response;
								updateButtonLabels('RADIO USB MUTE', USBAFGainOld);
								which=tPre+tPost;
								var tLat="latchBtn"+mBank;
								$.post("/programs/SetSettings.php", {field: tLat, radio: tMyRadio, data: lt1, table: "RadioInterface"}, function(response){
									});
							});
						};
						waitRefresh=4;
						};
					}else{
						//first of latch
						tPre=tPost.substring(0,1);
						tPost1=tPost.substring(tPost.indexOf("}")+1);
						tPost=tPost.substring(1,tPost.indexOf("}"));
						tPost2=tPost1; //save second command plus color
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
							$.post("/programs/SetSettings.php", {field: "USBAFGain", radio: tMyRadio, data: 0, table: "RadioInterface"}, function(response){
							});
							updateButtonLabels('RADIO USB MUTE',0);
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
				var tDX=$('#searchText').val().toUpperCase();
				tPost=tPost.replace('<dxcall>',tDX);
				if (tPost.indexOf('<mode>')>0){
					var tM="";
					if (tMode=="USB" || tMode=="LSB"|| tMode=="AM" || tMode=="FM"){
						tM="PHONE";
					}else if (tMode=="PKTLSB" || tMode=="PKTUSB" || tMode=="USB-D" || tMode=="LSB-D"|| tMode=="RTTY" || tMode=="RTTYR" || tMode=="FM-D" || tMode=="AM-D"){
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
				if (tPost.indexOf('<02>')>-1){
					tSpeed=tPost.split(">");
					if (tSpeed[1]==0){
						$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'WKSpeedOriginal', table: 'Keyer'}, function(response)
						{
							tSpeedOriginal=response;
							updateButtonLabels('/OLD', tSpeedOriginal);
							tPost="<02><"+parseInt(tSpeedOriginal)+">";
							$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tSpeed[1], table: "Keyer"});
								if (tSpeed[1]!==tSpeedOriginal){
									speedLock=1;
								}else{
									speedLock=0;
								}
							});
					}else{
						tPost="<02><"+parseInt(tSpeed[1])+">";
						updateButtonLabels("/OLD", tSpeedOriginal)
						$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tSpeed[1], table: "Keyer"});
						$.post("/programs/SetSettings.php", {field: "WKSpeedOriginal", radio: tMyRadio, data: tSpeedOriginal, table: "Keyer"});
						if (tSpeed[1]!==tSpeedOriginal){
							speedLock=1;
						}else{
							speedLock=0;
						}
					};
				};

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
					$.post("/programs/ConcatSettings.php", {field: "CWIn", radio: tMyRadio, data: tPost, table: "RadioInterface"}, function(response)
				{
				});

				return false;
			}
			if (which=="*PS1;")
			{
				if (tRadioModel !="NET rigctl"){
					if (tDisconnected==0){
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
						$("#modalCO-body").html('Power is on.');
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
				$.post("/programs/SetSettings.php", {field: "SplitOut", radio: tMyRadio, data: tSplitOn, table: "RadioInterface"});
				$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: "OFF", table: "RadioInterface"}, function(response){
					if (tMyPTT==3){
						$.post('./programs/doGPIOPTT.php', {PTTControl: "off"});
					}
				});
				$.post('/programs/powerOff.php', {radio: tMyRadio, user: tUserName}, function(response){

					$.post('./programs/disconnectRadio.php', {radio: tMyRadio, port: tRadioPort, id: tID, user: tUserName, rotor: tMyRotorRadio}, function(response1){
						$("#modalA-body").html("Radio is disconnected and power is off.");
						$(".modalA-title").html("Radio Connection");
						$("#myModalAlert").modal({show:true});
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
				$("#myModalCancelOnly").modal({show:true});
			};
				}else if (which.substr(0,7)=="!ROTATE"){
					var ro=which.split(" ");
					if (ro[1]){
						$.post("./programs/SetMyRotorBearing.php", {w: "turn", i: tMyRotorRadio, a: ro[1]});
					}else{
						$.post('./programs/GetSetting.php',{radio: tMyRotorRadio, field: 'RotorAzIn', table: 'RadioInterface'}, function(response)
						{
							var terr=0;
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
								tCurBeam=parseInt(response);
								var text = prompt(caption, parseInt(response));
								if (text){
									$.post("./programs/SetMyRotorBearing.php", {w: "turn", i: tMyRotorRadio, a: text}); //VE9GJ
								}else{
									$.post("./programs/SetMyRotorBearing.php", {w: "err", i: tMyRotorRadio, a: 0});
								}
							});
						});
					}
			}else if (which=="!RTR STOP"){
					$.post("/programs/SetMyRotorBearing.php", {w: "stop", i: tMyRotorRadio, a: ''});
			}else if (which.trim()=="!BANK"){
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
				var tNum="Macro <u>B</u>ank "+mBank;
				document.getElementById('myBank').innerHTML=tNum;
				$.post("/programs/SetSettings.php", {field: "MacroBankKeyer", radio: tMyRadio, data: mBank, table: "RadioInterface"});
				return false;
			}else if (which=="!PTTON"){
				  $.post('/programs/doGPIOPTT.php', {PTTControl: "on"});
			}else if (which=="!PTTOFF"){
				  $.post('/programs/doGPIOPTT.php', {PTTControl: "off"});
			}else if (which.substring(0,3)=="!SW"){
				$.post("/programs/SetSettings.php", {field: "CommandOut", radio: tMyRadio, data: which, table: "RadioInterface"});
			}else{
				switch(tPre){
				case '$':	//send CW
					sendCWMessage(tPost);
					break;
				case '*':	//direct radio command using hamlib format
					$.post("/programs/SetSettings.php", {field: "CommandOut", radio: tMyRadio, data: "*"+tPost, table: "RadioInterface"});
					if (tPost=="\stop_morse"){
						setPTT(0, 0);
						var tNum=String.fromCharCode(10);
						$.post("./programs/SetSettings.php", {field: "CWIn", radio: tMyRadioCW, data: tNum, table: "RadioInterface"});
					}
						break;
				case '#':	//direct system command
					$.post("/programs/systemExec.php", {command: tPost});
					break;
				case '!':	//special command
					if (tPost=='ESC'){
						setPTT(0);
						var tNum=String.fromCharCode(10);
						$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: tNum, table: "RadioInterface"});
//						$.post("./programs/SetSettings.php", {field: "CommandOut", radio: tMyRadio, data: "*\\stop_morse", table: "RadioInterface"});
					}else if (tPost=='TUNE'){
						if (tTuneOn==1){
							tTuneOn=0;
							var tNum=String.fromCharCode(10);
							$.post("./programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: tNum, table: "RadioInterface"});
						}else{
							tTuneOn=1;
							var tNum=String.fromCharCode(11)+String.fromCharCode(1);
							$.post("./programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: tNum, table: "RadioInterface"});
						}
					}else if (tPost=='TUNETO'){
						if (tDisconnected==1){
							alert("Radio is not ready, please connect and try again.");
							return false;
						}
						if (tMode==0){
							alert("Please try again, radio not ready.");
							return false;
						}
						$("#curMode1").val(tMode);
						$("#modal-title").html("Tune to");
						$("#modalI-body").html("<br>&nbsp;&nbsp;&nbsp;Enter any combination and click OK.");
						$("#myModalInput").modal({show:true});
						var x = document.getElementById("curFreq");
						x.value=parseInt(tMain);
						transRadioName=tRadioName;
						$.post('/programs/RadioID.php', {tRadio: transRadioName}, function(response) {
							if (response.length>0){
								transRadioID=response;
							}
							$.post("/programs/getRigBandwidths.php", {myRadioName: transRadioName, myRadio: tMyRadio, myRadioID:transRadioID, mode: ""}, function(response){
								var x = document.getElementById("curPassband1");
								x.value=response;//("Select Passband");
							});

						$.post("./programs/getRigCaps.php", {myRadioName: transRadioName, myRadio: transRadioID, cap: "Mode list:"}, function(response){
							var tL="";
							var tList1=response;
							var tList=tList1.split(" ");
							for (i=0;i<tList.length-1;i++)
							{
								tL=tL+"<div class='mymode' id=i<li><a class='dropdown-item' href='#'>"+tList[i]+"</a></li></div>";
							}
							var caps=response;
							x = document.getElementById("modeList");
							x.innerHTML=tL;
						});
						$.post("/programs/getRigBandwidths.php", {myRadio: transRadioID, mode: tMode}, function(response){

//						$.post("/programs/getRigBandwidths.php", {myRadioName: transRadioName, myRadio: tMyRadio, myRadioID:transRadioID, mode: tMode}, function(response){
							var tB="";
							if (tMode=="USB-D"){
//.								tMode="PKTUSB";
							}
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
				}else if (tPost=="T/R" && tDisconnected==0){
					if (tTrx==1){
						setPTT(0, 0);
						tTrx=0;
					}else{
						setPTT(1, 0);
					}
				}
			}
		}
	};

$(document).on('click', '.mymode', function() {
	var tB="";
	var text = $(this).text();
	$("#curMode1").val(text);
	$.post("/programs/getRigBandwidths.php", {myRadioName: transRadioName, myRadio: tMyRadio, myRadioID:transRadioID, mode: text}, function(response){
		if (tMode=="USB-D"){
//.			tMode="PKTUSB";
		}
		var tList1=response.trim();
		var tList=tList1.split("\t");
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
			tBandWidth=tB2[1];
			tB=tB+"<div class='mypassband' id='"+i+"'<li><a class='dropdown-item' href='#'>"+tB2+"</a></li></div>";
			$.post("./programs/SetFrequencyMem.php",{radio: tMyRadio, main: tMain, mode: tB1, bw: -1}, function(response){
				t=response;
			});

		};
		x = document.getElementById("passbandList");
		x.innerHTML=tB;

	});
});

$(document).on('click', '.mypassband', function() {
	var text = $(this).text();
	  $("#curPassband1").val(text);
});

			$(document).on('click', '#closeModalInput', function(){
				var x = document.getElementById("curFreq");
				var f = x.value.replaceAll(".","");
				if (f != null || f !=""){
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

		function sendCWMessage(what){
			if (tDisconnected==1){
				showConnectAlert();
				return false;
			}
			$.post("/programs/ConcatSettings.php", {field: "CWIn", radio: tMyRadio, data: what, table: "RadioInterface"},function(response){
				var tQ=response;
			});
		}

		var tUpdate = setInterval(bearingTimer,1000)

		function bearingTimer()
		{
			$.post("/programs/GetRotorIn.php", {rotor: tMyRotorRadio},function(response){
				var tAData=response.split('`');
				if (tAData[0]=="+"){
					tAData[0]="--";
				}
				var tAz=Math.round(tAData[0])+"&#176;";
				tCurBeam=tAz;
						for (i = 0; i < 32; i++) {
					x=aMacros[i];
					if (x.indexOf("ROTATE")>-1){
						var mB="m" + i + "Button";
						y=x.split("|");
						if (document.getElementById(mB)){
							document.getElementById(mB).innerHTML=y[0] + " (" + tCurBeam + ")";
						}
					};
				};
				$(".angle").html(tAz);
			});
		}

		var tFUpdate = setInterval(freqTimer, 1000);
		function freqTimer(){
//			console.log(outputSpeed.innerHTML);
			$.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:tCall }, function(response)
			{
				var tAData=response.split('`');
				tMain=tAData[0];
				tMode=tAData[3];
				if (waitRefresh>0){
					waitRefresh=waitRefresh-1;
					return;
				}
				if (tAData[11]!=sliderRFRef && sliderRFGainOride!=0){
					$('#sliderRF').slider('value',tAData[11]);
					var tP=tAData[11];
					if (tP<100 && tP>0) tP=tP-1;
					outputRF.innerHTML = tP;
				};
				if (tAData[12]!=sliderAFRef && sliderAFGainOride!=0){
					tP=parseInt(tAData[12]);
					if (tP<100 && tP>0) tP=tP-1;
					outputAF.innerHTML = tP;
					$('#sliderAF').slider('value',tAData[12]);
					for (i = 0; i < 32; i++) {
						x=aMacros[i];
						if (x.indexOf("AF MUTE")>-1){
							var mB="m" + i + "Button";
							y=x.split("|");
							if (document.getElementById(mB)){
								document.getElementById(mB).innerHTML=y[0] + " (" + tP + ")";
							}
						};
					};

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
				if (tAData[15]!=(USBAFGain+1) || doUSBUpdate==1){ //-1
					doUSBUpdate=0;
					USBAFGain1=tAData[15]-1;
					if (USBAFGain1>0){
						USBAFGainOld=USBAFGain1;
						USBAFGain=USBAFGain1;
						var tM=USBAFGain1;
						updateButtonLabels('RADIO USB MUTE', tM);
					}
					if (USBAFGain==0){
						USBAFGain1=USBAFGain1;//+1
					}
					if (tAData[15]>0){
						$.post("/programs/SetSettings.php", {field: "USBAFGainOld", radio: tMyRadio, data: USBAFGain1, table: "RadioInterface"}, function(response){ //-1
						});

					};
					waitRefresh=4;
				}
			});

			function updateFooter() {
				if (tAData.indexOf("NG")==-1) {

				  var tBW=tAData[17];
					var tRadioUpdate = tAData[8];
					if (tRadioUpdate.length != 0) {
					  $("#modalA-body").html(tRadioUpdate);
					  $(".modalA-title").html("RigPi Report");
					  $("#myModalAlert").modal({ show: true });
					  $.post("/programs/SetSettings.php", {
						field: "RadioData",
						radio: tMyRadio,
						data: "",
						table: "RadioInterface",
					  });
					}
				  }
				  if (!$.isNumeric(tAData[0])) {
					tAData[0] = "00000000";
					tAData[3] = "";
					tAData[2] = "00000000";
				  }
				  var cFreq2m = ("0000000000" + tAData[0]).slice(-10);
				  var tF = addPeriods(cFreq2m);
				  var tSplit = tAData[1];
				  tSplitOn = tSplit;
				  var cFreq2s = ("0000000000" + tAData[2]).slice(-10);
				  var tFs = addPeriods(cFreq2s);
				  $("#fPanel4").text("User: " + tCall + " (" + tUserName + ")");
				  if (tAData[0] == "00000000") {
					$("#fPanel1").html("&nbsp;No Radio");
					$("#fPanel2").text("");
					$("#fPanel3").text("");
					$("#fPanel1").attr("style", "background-color:red");
				  } else {
					tF = tF.trim();
					tFs = tFs.trim();
					if (tF.length < 9) {
					  tF = tF.substr(1);
					  tFs = tFs.substr(1);
					  if (tF.substr(0, 1) == "0") {
						tF = tF.substr(1);
					  }
					  if (tFs.substr(0, 1) == "0") {
						tFs = tFs.substr(1);
					  }
					  $("#fPanel1").text("Main: " + tF + " kHz");
					  if (tSplitOn == 1) {
						$("#fPanel2").text("Sub: " + tFs + " kHz");
					  } else {
						$("#fPanel2").text("");
					  }
					} else {
					  $("#fPanel1").text("Main: " + tF + " MHz");
					  if (tSplitOn == 1) {
						$("#fPanel2").text("Sub: " + tFs + " MHz");
					  } else {
						$("#fPanel2").text("");
					  }
					}
					var tM = tAData[3];
					if (tM == "PKTUSB") {
					  tM = "USB-D";
					}
					if (tM=="PKTLSB"){
					  tM="LSB-D";
					}
					$("#fPanel3").text("Mode: " + tM + " - BW: "+tBW);
					$("#fPanel1").attr("style", "background-color:black");
				  }
				 }

			updateFooter();
			if (speedPotEnable==1){
				  $(tSpeedCont).addClass('d-none');
				  $(tSpeedSl).addClass('d-none');
				  $(outputSpeed).css('margin-top',0);
			  }else{
				  $(tSpeedCont).removeClass('d-none');
				  $(tSpeedSl).removeClass('d-none');
				  $(outputSpeed).css('margin-top','10px');
			  outputSpeed.innerHTML='<H3b>'+tSpeed+" WPM</H3b>";

			  }
			if (speedPotEnable==1 && speedLock==0){
				$.post('./programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKPot'}, function(response) {
					tSpeed=response;
	//				if (response!=speedPot){
					speedPot=response;
					$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKMinWPM'}, function(response) {
						var tMin=response;
						var tMinI=parseInt(tMin);
						var tSpeedI=parseInt(tSpeed);
						tSpeed=tSpeedI+tMinI;
						$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tSpeed, table: "Keyer"});
						$.post("/programs/SetSettings.php", {field: "CWChangeCk", radio: tMyRadio, data: 1, table: "RadioInterface"});
							outputSpeed.innerHTML='<H3b>'+tSpeed+" WPM (use speed pot)</H3b>";
							$("#sliderSpeed").addClass('text-spacer');
							$("#cwi").focus();
							waitRefresh=2;
					});
				});
			}else{

				if (tSpeed.constructor===Array){
					var tS=tSpeed[1];
				}
			};
			var now = new Date();
			var now_hours=now.getUTCHours();
			now_hours=("00" + now_hours).slice(-2);
			var now_minutes=now.getUTCMinutes();
			now_minutes=("00" + now_minutes).slice(-2);
			$("#fPanel5").text(now_hours+":"+now_minutes+'z');
			var tT=$("#fPanel1").text().trim();

			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKSpeedOriginal'}, function(response) {
				tSpeedOriginal=response;
				$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKSpeed'}, function(response) {
						var tS=response;
						for (i = 0; i < 32; i++) {
							x=aMacros[i];
							if (x.indexOf("/OLD")>-1){
								var mB="m" + i + "Button";
								y=x.split("|");
								if (document.getElementById(mB)){
									document.getElementById(mB).innerHTML=y[0] + " (" + tS + ")";
								}
							};
						};
						if (tS!=sliderSpeedRef){
						}
				});
			});

			if (tT==="No Radio"){
				tDisconnected=1;
			}else{
				tDisconnected=0;
			};
		};
		function setBandMemory(){
			if (tMain.indexOf('UNK')==-1  &&
				tMain.indexOf('0000000000')==-1  &&
				tMode.indexOf('UNK')==-1)
				{
					console.log("xxxsetbanbdmem " + tMain + " " + tMode + " " + tModeOld);
//				$.post("/programs/SetFrequencyMem.php", {radio: tMyRadio, main: tMain,
//				 mode: tMode, bw: -1}, function(response){});  //this causes mode to leak to new band
			};

		}

		var tUpdate = setInterval(updateTimer,100);
		function updateTimer(){
			$.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:tMyCall }, function(response)
				{
				tAData=response.split('`');
				tTrx=tAData[9];
				tPTT=tAData[7];
				var tBW=tAData[17];
				if (tBW!==tBandWidth){
					tBandWidth=tBW;
				}

				tMode=tAData[3];//.substr(0,tAData[3].indexOf(" ")); mode is separate from bw
					if (tMode=="PKTUSB"){
					tMode="USB-D";
				}
				if (tMode=="PKTLSB"){
					tMode="LSB-D";
				}
				tMain=tAData[0];
			});

			if (clearingLeft=="0" && tKeyerMode!=1){
				var output = document.getElementById("cwo");
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'CWOut', table: "RadioInterface"}, function(response) {
					var tCW=response;
					if (tCW.length>0){
						tCW=tCW.replace(/\u001E/g, "")
						tCW=tCW.replace(new RegExp( String(RegExp.$1),"g"),"");
						tCW = tCW.replace(/[^\x20-\x7E]/g, "");
						if (tCW!=output.value){
							output.focus();
							output.value="";
							output.value=tCW;
						}
					}
					output.scrollTop=output.scrollHeight;
				});
			};
			if (tTrx==1){
//				console.log("transmiting...");
				return;
			}else{
//				console.log("receiving...");
			}
			if (tMode=="PKTUSB"){
				tMode="USB-D";
			}
			if (tMode=="PKTLSB"){
				tMode="LSB-D";
			}
			if (tModeOld!==tMode){
				console.log("Mode: " + tModeOld + " radio: " + tMode);
				setBandMemory();
				var tNew=tMode.toLowerCase();
				if (tNew=='usb-d' ||  tNew=='pktusb'){tNew='usbd'};
				updateModeSelection();
//				tModeOld=tMode;
				$('#' + tNew + 'Button').addClass('btn-mode-sel');
			}
			if (tBandOld!==tAData[5]){
				console.log("Band: " + tBandOld + " on radio: " + tAData[5] + " smode: " + tMode + " old: " + tModeOld);
				tBandOld=tAData[5];
				setBandMemory();
				tBand=tAData[5];
				var tMo=tBandOld;
				var tNew=tBand;
				updateButtons();
//						updateModeSelection();
				$('#' + tNew + 'Button').addClass('btn-mode-sel');
			}
		}

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
				  var tB=document.getElementById(tB2);
				  if (tB.classList.contains("btn-mode-sel")){
					  $(bName).removeClass("btn-mode-sel");
				  }
			  };
//			  tModeOld="";
			  waitRefresh=4;
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
				  }else{
					  $(bName).removeClass("btn-secondary");
					  $(bName).removeClass("btn-mode-sel");
				  };
			  };
//			  tModeOld="";
			  waitRefresh=4;
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

		function getBandMemory(nBand){
			waitRefresh=6;
			var qBand=nBand+'L';
			var tF='0';
			if (tDisconnected==1){
				showConnectAlert();
				return false;
			}
//			tM1=tModeOld.toLowerCase();//tMode.toLowerCase();
			tM1=tMode.toLowerCase();
			if (tMain.indexOf('UNK')==-1){
				 $.post("/programs/SetFrequencyMem.php", {radio: tMyRadio, main: tMain, mode: tM1, bw: -1}, function(response){

				$.post('/programs/GetFrequencyMem.php',{radio: tMyRadio, band: qBand}, function(response)
				{
					var obj = JSON.parse(response);
					var tF=obj[0];
					var tM=obj[1];
					var tB=obj[2];
					var tM2=tM.toLowerCase();
//					console.log("tM: " +  tM1 + " " + tM);
					tBa=GetBandFromFrequency(tMain);
					tBa1=GetBandFromFrequency(tF);
					updateModeSelection();
					updateButtons();
					$('#' + tBa1+ 'Button').addClass('btn-mode-sel');
					$('#' + tM2 + 'Button').addClass('btn-mode-sel');

					$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: tF, table: "RadioInterface"}, function(response){
						var t = response;
						$.post("/programs/SetSettings.php", {field: "SubOut", radio: tMyRadio, data: tF, table: "RadioInterface"}, function(response){
							t = response;
							$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: tM.toUpperCase(), table: "RadioInterface"}, function(response){
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
	</script>
	<?php require_once($dRoot . "/includes/styles.php"); ?>
</head>
<body class="body-black-scroll" id="keyer">
	<?php require $dRoot . "/includes/header.php"; ?>
	<div class="container-fluid height-min: 100px">
		<div class="row top-buffer">
			<div class="col-md-6">
				<div class="form-group">
					<label class="wlbl" id="myOut">RigPi</label>
					<label class="wlbl" style="float:right;" id="mySpeed"></label>
				  <textarea class="form-control text-uppercase" rows="10" id="cwo" disabled="YES"></textarea>
<!-- 				NOTE: this is text coming FROM (CWO) keyer -->
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					<label class="wlbl" id="myIn">RigPi</label>
					<button class="btn btn-light btn-outline btn-small-color float-right" style="margin-top:0px" title="Change Bank" type="button">
						<span class="btn-small-color" id="myBank" style="height: 14px; font-size:16px;">
							Macro <u>B</u>ank
						</span>
					</button>
					<label class="wlbl" style="float:left;" id="myHold"></label>
					<textarea class="form-control text-uppercase cwi" rows="10" id="cwi"></textarea>
					<!-- 				NOTE: this is text going TO (CWI) keyer -->
<!-- 				NOTE: conversion to upper case seems to confuse iPhone Siri dictate unless you say "All caps on" -->
				</div>
			</div>
			<div class="col-sm-12">
				<div class="row" id="dPanel">
				</div>
				<div class="row">
					<div class="table-responsive">
					<table class="table-smxx table-dark" id="macroTable" >
					<tr>
						<td width=16.6% >
							<button class="btn btn-color btn-primary btn-sm btn-block" id="holdButton" type="button">
								H<u>O</u>LD
							</button>
						</td>
						<td width=16.6% >
						<button class="btn btn-color btn-primary btn-sm btn-block" id="repeatButton" type="button">
							<u>R</u>EPEAT
						</button>
						</td>
						<td width=16.6% >
						<button class="btn btn-color btn-primary btn-sm btn-block" id="clearLeftButton" type="button">
							CLEAR LE<u>F</u>T
						</button>
						</td>
						<td width=16.6% >
							<button class="btn btn-color btn-primary btn-sm btn-block" id="clearRightButton" type="button">
								CLEAR RI<u>G</u>HT
							</button>
						</td>
						<td colspan="2">
							<row>
							<span class="btn-small-color" style="height: 15px; margin-top: 0px;" id="myKeyerSpeedVal" >
							</span>
								<div  title='Keyer Speed Adjust' id="containerSpeed" class="d-none slidecontainerSpeed">
									<div class="d-none" id="sliderSpeed" style="height: 15px; margin-top: 4px; background: black;"></div>
								</div>
							</row>
						</td>
					</tr>
					<tr>
						<td width=16.6% >
							<button class="btn btn-color btn-primary btn-sm btn-block" id="skButton" type="button">
								<h8 style="color:gray;"><u>1</u>:</h8> SK
							</button>
						</td>
						<td width=16.6% >
							<button class="btn btn-color btn-primary btn-sm btn-block" id="knButton" type="button">
								<h8 style="color:gray;"><u>2</u>:</h8> KN
							</button>
						</td>
						<td width=16.6% >
							<button class="btn btn-color btn-primary btn-sm btn-block" id="arButton" type="button">
								<h8 style="color:gray;"><u>3</u>:</h8> AR
							</button>
						</td>
						<td width=16.6% >
							<button class="btn btn-color btn-primary btn-sm btn-block" id="btButton" type="button">
								<h8 style="color:gray;"><u>4</u>:</h8> BT
							</button>
						</td>
						<td width=16.6% >
							<button class="btn btn-color btn-primary btn-sm btn-block" id="rrButton" type="button">
								<h8 style="color:gray;"><u>5</u>:</h8> BK
							</button>
						</td>
						<td width=16.6% >
							<button class="btn btn-color btn-primary btn-sm btn-block" id="atButton" type="button">
								<h8 style="color:gray;"><u>6</u>:</h8> @
							</button>
						</td>
					</tr>
					</table>
					</div>
				</div>

			</div>
		</div>
		<hr>
		<?php require $dRoot . "/includes/macroButtons.php"; ?>
	</div>
	<?php require $dRoot . "/includes/footer.php"; ?>
</div>
	<?php require $dRoot . "/includes/modalxxx.txt"; ?>
	<?php require $dRoot . "/includes/modal.txt"; ?>
	<?php require $dRoot . "/includes/modalAlert.txt"; ?>
	<?php require $dRoot . "/includes/modalCancelOnly.txt"; ?>
	<?php require $dRoot . "/includes/modalCancelAlert.txt"; ?>
	<script src="/Bootstrap/popper.min.js"></script>
	<script src="/Bootstrap/jquery-ui.js"></script>
	<script src="/js/jquery.ui.touch-punch.min.js"></script>
	<script src="/Bootstrap/bootstrap.min.js"></script>
	<script src="/js/nav-active.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
	<script src="/js/summernote-case-converter.js"></script>
</html>
