<?php
/**
 * @author Howard Nurse, W6HN
 *
 * This is the spots window
 *
 * It must live in the html folder
 */

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

<!DOCTYPE html>
<html lang="en">
  <meta name="viewport" content="width=device-width, initial-scale=1">

<head>
	<meta charset="utf-8" /><!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title><?php echo $tCall; ?> RigPi Spots</title>
	<meta name="description" content="RigPi Spots" />
	<meta name="author" content="Howard Nurse, W6HN" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" /><!-- Bootstrap CSS -->
	<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
	<link rel="shortcut icon" href="/favicon.ico" />
	<link rel="apple-touch-icon" href="/favicon.ico" />
	<link rel="stylesheet" href="./Bootstrap/bootstrap.min.css" type="text/css" />
	<link rel="stylesheet" href="./Bootstrap/jquery-ui.css" type="text/css" />
	<script src="/Bootstrap/jquery.min.js" ></script>
	<script defer src="./awe/js/all.js" ></script>
	<link href="./awe/css/all.css" rel="stylesheet">
	<link href="./awe/css/fontawesome.css" rel="stylesheet">
	<link href="./awe/css/solid.css" rel="stylesheet">	
	<?php require $dRoot . "/includes/styles.php"; ?>
	
	<script type="text/javascript">
			var searchCall='';
			var tMyRadio='1';
			var bsCenterFreq=0;
			var waitRefresh=0;
			var tBand='20';
			var tBandOld='20';
			var tHiFreq='';
			var pCount=0;
			var selectedRow=0;
			var tUserName="<?php echo $tUserName; ?>";
			var tCall="<?php echo $tCall; ?>";
			var tUser='';
			var tMain=0;
			var tNeed='';
			var tFil='20';
			var tFilMode='All';
			var tFollowMe=0;
			var tCurFreq='';
			var tCurMode='';
			var tSort='';
			var tSortDir='';
			var waitSpots=4;
			var tNoRadio=0;
			var tNoInternet=0;
			var speedPot=0;
			$(document).ready(function() {
					$.post('/programs/testInternet.php',function(response){
						if (response !=0){
							tNoInternet=1;
						}
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


				$.post('/programs/GetSelectedRadio.php',{un:tUserName}, function(response)
				{
					tMyRadio=response;
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'SpotsSort', table: 'MySettings'}, function(response){
						tSort=response;
					});
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'SpotsSortDir', table: 'MySettings'}, function(response){
						tSortDir=response;
					});
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'SpotBand', table: 'MySettings'}, function(response){
						tFil=response;
						 $.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'SpotMode', table: 'MySettings'}, function(response){
							tFilMode=response;
							 $.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'SpotNeed', table: 'MySettings'}, function(response){
								tNeed=response;
								$.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:<?php echo "'" .
          $tCall .
          "'"; ?>}, function(response) 
								{
									var tAData=response.split('`');
									if (!$.isNumeric(tAData[0])){
										tNoRadio=1;
										tAData[0]="0";
									}else{
										tNoRadio=0;
									}
									tMain=tAData[0];
									if (tMain != bsCenterFreq){
										bsCenterFreq=tMain;
										tBand=tAData[5];
										var tFreqHi=getBSHiFreq(bsCenterFreq, tBand);
										var scrollPixels=((tFreqHi-bsCenterFreq)/100)-400;
										$("#marker").html("");
										paintBS(bsCenterFreq,tBand);
										paintBSSpots(tBand);
										getBSFreqFromFreq(bsCenterFreq,tBand);
//										getBSFreq(tBand);
										$("#bs").animate({ scrollTop: scrollPixels });
										paintBSMarker(scrollPixels);
//										$.post("/programs/SetSettings.php", {field: "LastFreq", radio: tMyRadio, data: tMain, table: "MySettings"});
//										$.post("/programs/SetSettings.php", {field: "LastBand", radio: tMyRadio, data: tBand, table: "MySettings"});
									}
				
									var tSplitState=tAData[1];
									if (tSplitState=="0")
									{
										//no split marker
									}else{
										//add split marker
									}
				
									tSplit=tAData[2];
									var tMode=tAData[3];
								});
							});
						});
					});

					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response){
						$('#searchText').val(response);
					});

					
//					getBSHiFreq(tBand);

					$(document).on('click', '.clickme', function(event) {
						$(this).addClass('highlight').siblings().removeClass('highlight');
						var tCall = $(this).attr('call');
						$('#searchText').val(tCall);
						$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tCall, table: "MySettings"});
						if (tNoRadio==0){
							var tFreq = $(this).attr('frequency');
							selectedRow=$(this).attr('id');
							tBand = $(this).attr('band');
							var tMode = $(this).attr('mode');
							if (tMode=='follow'){
								tMode=tCurMode;
							}
							var tFreqHi=getBSHiFreq(tFreq,tBand);
							var scrollPixels=((tFreqHi-parseInt(tFreq))/100)-400;
							$("#marker").html("");
							getBSFreqFromFreq(bsCenterFreq,tBand);
//							getBSFreq(tBand);
							paintBS(bsCenterFreq,tBand);
							paintBSSpots(tBand);
							$("#bs").animate({ scrollTop: scrollPixels });
							paintBSMarker(scrollPixels);
							$.post("/programs/SetSettings.php", {field: "ModeOutCk", radio: tMyRadio, data: "1", table: "RadioInterface"},function(response){
								$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: tMode, table: "RadioInterface"},function(response){
									$.post("/programs/SetSettings.php", {field: "ModeIn", radio: tMyRadio, data: tMode, table: "RadioInterface"}, function(){
										$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: tFreq, table: "RadioInterface"}, function(){
											$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: tFreq, table: "RadioInterface"}, function(){
												$.post("/programs/SetSettings.php", {field: "ModeOutCk", radio: tMyRadio, data: "1", table: "RadioInterface"}, function(){
													$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: tMode, table: "RadioInterface"}, function(){
														$.post("/programs/SetSettings.php", {field: "ModeIn", radio: tMyRadio, data: tMode, table: "RadioInterface"}, function(){
															$.post("/programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tCall, table: "MySettings"}, function(){
																$.post("/programs/SetSettings.php", {field: "ModeOutCk", radio: tMyRadio, data: "1", table: "RadioInterface"},function(response){
																	$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: tMode, table: "RadioInterface"},function(response){
																		$.post("/programs/SetSettings.php", {field: "ModeIn", radio: tMyRadio, data: tMode, table: "RadioInterface"}, function(){
																		});
																	});
																});
															});
														});
													});
												});
											});
										})
									});
								});
							});
							waitRefresh=2;
							bsCenterFreq=tFreq;
						};
					});
					
					$(document).on('click', '#bandCanvas', function(e){
						var fTune=e.pageY;
						var fT=e.clientY;
						var sT=$('#bandCanvas').scrollTop();
						var cP=$('#bandCanvas').offset().top;
						var oF=(cP-fTune)*100;
						var tFreq=getBSHiFreq(tMain,tBand)+oF;
						if (GetBandFromFrequency(tFreq)=='UNK'){
							return;
						}
						bsCenterFreq=tFreq;
						 var scrollPixels=((-1*oF/100)-400);
						$("#bs").animate({ scrollTop: scrollPixels+"px" });
						 $("#marker").html("");
//						getBSFreq(tBand);
						paintBS(bsCenterFreq,tBand);
getBSFreqFromFreq(bsCenterFreq,tBand);
						paintBSMarker(scrollPixels);
						 paintBSSpots(tBand);
						$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: bsCenterFreq, table: "RadioInterface"});
						$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: bsCenterFreq, table: "RadioInterface"});
						$('tr').removeClass('highlight'); // remove class from other rows
						waitRefresh=2;
					});

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
						}else if (nFreq > 222000000 && nFreq < 225000000){
							return "1.25";
						}else if (nFreq > 420000000 && nFreq < 450000000){
							return "1.25";
						}else if (nFreq > 1240000000 && nFreq < 1300000000){
							return "1.25";
						}else {
							return "UNK";
						}
					} 

					$(document).on('click', '.BSbutton', function(event) {
							var tFreq = $(this).attr('frequency');
							tBand = $(this).attr('band');
							var tCall = $(this).attr('call');
							var tMode = $(this).attr('mode');
							var idSpot =$(this).attr('id');
							var tID=idSpot.substring(1);
						selectedRow=tID;
						$.post("/programs/SetSettings.php", {field: "ModeOutCk", radio: tMyRadio, data: "1", table: "RadioInterface"},function(response){
							$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: tMode, table: "RadioInterface"},function(response){
								$.post("/programs/SetSettings.php", {field: "ModeIn", radio: tMyRadio, data: tMode, table: "RadioInterface"}, function(){
									$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: tFreq, table: "RadioInterface"});
									$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: tFreq, table: "RadioInterface"});
								});
							});
						});
						waitRefresh=4;
						bsCenterFreq=tFreq;
						$('#searchText').val(tCall);
						var tFreqHi=getBSHiFreq(bsCenterFreq,tBand);
							var scrollPixels=((tFreqHi-tFreq)/100)-400;
						$("#marker").html("");
						$("#bs").animate({ scrollTop: scrollPixels });
						paintBSMarker(scrollPixels);
						$('tr').removeClass('highlight'); // remove class from other rows
						$('#'+tID).addClass('highlight');
						$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tCall, table: "MySettings"});
						});
						
					$(document).on('click', '.band', function(event) {
						tFil = $(this).attr('id');
						getSpots(tFil,tNeed, tFilMode, tSortDir, tSort);
						$.post("/programs/SetSettings.php", {field: "SpotBand", radio: tMyRadio, data: tFil, table: "MySettings"});
						pCount=10000;
					});

					$(document).on('click', '.needed', function(event) {
						tNeed = $(this).attr('id');
						getSpots(tFil,tNeed, tFilMode, tSortDir, tSort);
						$.post("/programs/SetSettings.php", {field: "SpotNeed", radio: tMyRadio, data: tNeed, table: "MySettings"});
						pCount=10000;
					});

					$(document).on('click', '.mode', function(event) {
						tFilMode = $(this).attr('id');
						getSpots(tNeed, tFil, tFilMode, tSortDir, tSort);
						$.post("/programs/SetSettings.php", {field: "SpotMode", radio: tMyRadio, data: tFilMode, table: "MySettings"});
						pCount=10000;
					});

				});
					
					function showSettings(){
						var set=document.getElementById('Preview');
						set.click();
					}
					
					function showHelp(){
						var set=document.getElementById('help');
						set.click();
					}
					
					$(document).keydown(function(e){
					var t=e.key;
					e.multiple
					var w=e.which;
					if (w==229){
						return;
					}
					if (w==191)
					{
						if (e.shiftKey){
							<?php require $dRoot . "/includes/shortcutsSpots.php"; ?>
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
						var win="/calendar_main.php?x="+tUserName+"&c="+tCall;
						window.open(win, "_self");
						break;
					case 67: //c
						document.getElementById('connectButton').click();
						e.preventDefault();
						break;
					case 68: //d
						document.getElementById('disconnectButton').click();
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
					
					fillDescription();

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
					tUpdate = setInterval(updateTimer,500);
					
					$.getScript("/js/modalLoad.js");
			});

			var tUpdate = setInterval(bearingTimer,1000)
			function bearingTimer()
			{
				$.post("/programs/GetRotorIn.php", {rotor: tMyRadio},function(response){
					var tAData=response.split('`');
					if (tAData[0]=="+"){
						tAData[0]="--";
					}
					var tAz=Math.round(tAData[0])+"&#176;";
					$(".angle").html(tAz);
				});	
/*				$.post('./programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKPot'}, function(response) {
					var tSpeed=response;
					if (tSpeed!=speedPot){
						speedPot=tSpeed;
						$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKMinWPM'}, function(response) {
							var tMin=response;
							tSpeed=parseInt(tSpeed)+parseInt(tMin);
							$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tSpeed, table: "Keyer"});
						});
					}				
				});				
*/			}

			function updateTimer()
			{
				waitRefresh=waitRefresh-1;				
				if (waitRefresh>0)
				{
					return;
				}
				waitRefresh=0;
				$.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:<?php echo "'" .
      $tCall .
      "'"; ?>}, function(response) 
				{
					var tAData=response.split('`');
					if (!$.isNumeric(tAData[0])){
						tNoRadio=1;
						tAData[0]="0";
					}else{
						tNoRadio=0;
					}
					tMain=tAData[0];
					updateFooter();
					if (tNoRadio==0){
//						return;
						var tMainBand=GetBandFromFrequency(tMain);
						if (tMain != bsCenterFreq){// && tMain>0){//&& tMainBand==tAData[5]){
							var tMainx=addPeriods(tMain);
//							$("#BSFreqDisp").html('');
							$("#BSFreqDisp").html(tMainx);
							bsCenterFreq=tMain;
							tBand=tMainBand;  /////////////////////////////////////////////
							var tFreqHi=getBSHiFreq(bsCenterFreq,tBand);
							var scrollPixels=((tFreqHi-bsCenterFreq)/100)-400;
							$("#marker").html("");
							$("#bs").animate({ scrollTop: scrollPixels });
							paintBSMarker(scrollPixels);
//							$.post("/programs/SetSettings.php", {field: "LastFreq", radio: tMyRadio, data: tMain, table: "MySettings"});
//							$.post("/programs/SetSettings.php", {field: "LastBand", radio: tMyRadio, data: tBand, table: "MySettings"});
						}
						var tSplitState=tAData[1];
						tSplit=tAData[2];
						var tMode=tAData[3];
					}
				 });
				
				var now = new Date();
				var now_hours=now.getUTCHours();
				now_hours=("00" + now_hours).slice(-2);
				var now_minutes=now.getUTCMinutes();
				now_minutes=("00" + now_minutes).slice(-2);
				$("#fPanel5").text(now_hours+":"+now_minutes+'z');
				waitSpots=waitSpots-1;
				if (waitSpots>0){
					return;
				}else{
					waitSpots=4;	//throttle spot dump
					getSpots(tFil,tNeed, tFilMode, tSortDir, tSort);
				}
			}

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
					window.open("/login.php","_self");
					form.submit();
				};                

			$("#menuPopup").bind('mouseout',function(){
				$('#menuPopup').css('display','none');
			});
			
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
				}else if (nFreq > 219000000 && nFreq < 225000000){
					return "1.25";
				}else {
					return "UNK";
				}
			} 

			function getBandFilter(tWhat){
				if (tWhat!=tFil || tBandOld!=tBand){
					pCount=10000;
				};
				tBandOld=tBand;
				var tFilx='';
				switch (tWhat){
					case ('followMe'):
						tFilx = 'Band='+"'"+ tBand + "'";
						break;
					case ('hf'):
						tFilx = 'Band>6 and Band <=160';
						break;
					case ('low'):
						tFilx = 'Band>30 and Band <=160';
						break;
					case ('high'):
						tFilx = 'Band=10 or Band=15 or Band=20';
						break;
					case ('warc'):
						tFilx = 'Band=12 or Band=17 or Band=30';
						break;
					case ('vhfUHF'):
						tFilx = 'Band<10 or Band=23 or Band=70';
						break;
					case ('160'):
						tFilx = 'Band=160';
						break;
					case ('80'):
						tFilx = 'Band=80';
						break;
					case ('60'):
						tFilx = 'Band=60';
						break;
					case ('40'):
						tFilx = 'Band=40';
						break;
					case ('30'):
						tFilx = 'Band=30';
						break;
					case ('20'):
						tFilx = 'Band=20';
						break;
					case ('17'):
						tFilx = 'Band=17';
						break;
					case ('15'):
						tFilx = 'Band=15';
						break;
					case ('10'):
						tFilx = 'Band=10';
						break;
					case ('6'):
						tFilx = 'Band=6';
						break;
					case ('2'):
						tFilx = 'Band=2';
						break;
					default:
						tFilx = '1=1';
				}
				var tMainx=addPeriods(tMain);
//				$("#BSFreqDisp").html('');
				$("#BSFreqDisp").html(tMainx);
				return tFilx;
			}
	
	
function getBSHiFreq(bsfre, band){
	switch (band){
		case '160':
			tFreq=2100000;
			break;
		case '80':
			tFreq=4050000;
			break;
		case '60':
			tFreq=5500000;
			break;
		case '40':
			tFreq=7400000;
			break;
		case '30':
			tFreq=10250000;
			break;
		case '20':
			tFreq=14450000;
			break;
		case '17':
			tFreq=18270000;
			break;
		case '15':
			tFreq=21500000;
			break;
		case '12':
			tFreq=25090000;
			break;
		case '10':
			freq=30000000 - parseInt(bsfre);
			if (freq>1500000){
				tFreq=28550000;
			}else if (freq>1000000){
				tFreq=29050000;
			}else if (freq>500000){
				tFreq=29550000;
			}else{ 
				tFreq=30050000;
			}
			break;
//			tFreq=28500000;
//						tFreq=parseInt(bsfre)+300000;//1246300000;
//			break;
		case '6':
			tFreq=parseInt(bsfre/500000)*500000+550000;//1246300000;
			break;
		case '2':
			tFreq=parseInt(bsfre/500000)*500000+550000;//1246300000;
			break;
		case '1.25':
			tFreq=parseInt(bsfre/500000)*500000+550000;//1246300000;
			break;
		case '70':
			tFreq=parseInt(bsfre/500000)*500000+550000;//1246300000;
			break;
		case '23':
			tFreq=parseInt(bsfre/500000)*500000+550000;//1246300000;
			break;
		default:
			tFreq=14025000;
			break;
	}
	tHiFreq=tFreq;
	return tFreq;
}
			function getBSHiFreqxxx(freq, band){
				switch (band){
					case '10':
						freq=30000000 - parseInt(freq);
						if (freq>1500000){
							tFreq=28550000;
						}else if (freq>1000000){
							tFreq=29050000;
						}else if (freq>500000){
							tFreq=29550000;
						}else{ 
							tFreq=30050000;
						}
						break;
					default:
						freq1=parseInt(freq);
						freq2=parseInt(freq1/500000);
						var tOffset=0;
						if (band=='160'){
							tOffset=210000;
						}else if (band=='80'){
							tOffset=404000;
						}else if (band=='40'){
							tOffset=550000;
						}else if (band=='15'){
							tOffset=500000;
						}else if (band=='15'){
							tOffset=500000;
						}else{
							tOffset=450000
						}
						tFreq=freq2*500000+tOffset;
						break;
				}
				tHiFreq=tFreq;
				return tFreq;
			}

			function paintBS(freq,band){
				var strokeUpper=0;
				var strokeLower=0;
				switch (band){
					case '160':
						strokeUpper=1000;
						strokeLower=3000;
						break;
					case '80':
						strokeUpper=500;
						strokeLower=500;
						break;
					case '60':
						strokeUpper=950;
						strokeLower=4000;
						break;
					case '40':
						strokeUpper=1000;
						strokeLower=2000;
						break;
					case '30':
						strokeUpper=1000;
						strokeLower=4500;
						break;
					case '20':
						strokeUpper=1000;
						strokeLower=1500;
						break;
					case '17':
						strokeUpper=1020;
						strokeLower=3980;
						break;
					case '15':
						strokeUpper=500;
						strokeLower=1000;
						break;
					case '12':
						strokeUpper=1000;
						strokeLower=4000;
						break;
					case '10':
						freq=30000000 - parseInt(freq);
						if (freq>1500000){
							strokeUpper=500;
							strokeLower=500;
						}else if (freq>1000000){
							strokeUpper=500;
							strokeLower=500;
						}else if (freq>500000){
							strokeUpper=500;
							strokeLower=500;
						}else if (freq>300000){
							strokeUpper=3500;
							strokeLower=500;
						}
						break;
					default:
						strokeUpper=500;
						strokeLower=500;
						break;
				}
				var startLower=6000-strokeLower;
				$('#frequencies').empty();
				$('#frequencies').line(136,0, 337, 0, {color:"red", stroke:strokeUpper, zindex:0});
				$('#frequencies').line(136, startLower, 337, startLower, {color:"red", stroke:strokeLower, zindex:0});
				getSpots(tFil,tNeed, tFilMode, tSortDir, tSort);

			}
			
			function paintBSMarker(freq){
				var bsFreq=freq+400;//window.innerHeight/2-50;
				$('#marker').line(17, bsFreq, 130, bsFreq, {color:"red", stroke:'2', zindex:0});
				$('#marker').line(17, bsFreq+2, 130, bsFreq+2, {color:"FireBrick", stroke:'2', zindex:0});
			}
			
			function paintBSSpots(bandx){
				$.post('/programs/GetBandSpotter.php',{radio:tMyRadio,band:bandx,frequency: bsCenterFreq, folder:'Inbox',need:tNeed}, function(response){
					 var aRSpots=response.split("top: ");
					 var aLSpots=response.split("top='");
					 var arr1;
					 var arr2;
					 var i=0;
					 var len=aRSpots.length;
					for(i=1;i<len;i++){
						arr1 = parseInt(aLSpots[i])+15;
						arr2 = parseInt(aRSpots[i])+15;
						$('#frequencies').line(135, arr1, 180, arr2, {color:"black", stroke:'2', zindex:0});
					}
					 $("#frequencies").append(response);
				 });
			}
			
			function getBSFreqFromFreq(nfreq,nband)
			{
				$.post("/programs/GetBSFrequenciesFreq.php",{freq: nfreq, band: nband},function(response){
					var freqs=response;
					$("#frequencies").append(freqs);
				});
			};

			function getSubSpots(response)
			{
				var pCount1=response.substr(3, response.indexOf(">")-3);
				if (pCount1==pCount){
					return;
				}
				pCount=pCount1;
				response=response.substr(response.indexOf(">")+1);
				$('#tbody').empty();
				$('#tbody').append(response);

				var tFreqHi=tHiFreq;
				if (tNoRadio==0){
					var scrollPixels=((tFreqHi-bsCenterFreq)/100)-400;
					$("#marker").html("");
					paintBS(bsCenterFreq,tBand);
					paintBSSpots(tBand);
					getBSFreqFromFreq(bsCenterFreq,tBand);
					$("#bs").animate({ scrollTop: scrollPixels });
					paintBSMarker(scrollPixels);
					$('tr').removeClass('highlight'); // remove class from other rows
					$('#'+selectedRow).addClass('highlight');
				}

				$(function () {
					$('.BSdelete').on('click', function () {
						var tID = $(this).attr('id');
						if (tID.substring(0,1)=="b"){
							tID=tID.substring(1);
							   if (confirm('Delete one Spot from list?')){
								   $.post('/programs/deleteSpot.php',{id:tID},function(response){
									$("#modalA-body").html(response);			  				
									$("#modalA-title").html("Delete Spot");
									  $("#myModalAlert").modal({show:true});//			  				alert(response);
									   getSpots(tFil,tNeed, tFilMode, tSortDir, tSort);
									var tFreqHi=tHiFreq;
									var scrollPixels=((tFreqHi-bsCenterFreq)/100)-400;
									$("#marker").html("");
									paintBS(bsCenterFreq,tBand);
									paintBSSpots(tBand);
									getBSFreqFromFreq(bsCenterFreq,tBand);
//									getBSFreq(tBand);
									$("#bs").animate({ scrollTop: scrollPixels });
									paintBSMarker(scrollPixels);
								   })
							   }
						 }
					 })
				 })

				$('.hClk').on('click', function() {
					var tIDV=$(this).attr('id');
					if (!(tSort==tIDV)){
						tSortDir='ASC';
					}else{
						if (tSortDir=='ASC'){
							tSortDir='DESC';
						}else{
							tSortDir='ASC';
						}
					}
					tSort=tIDV;
					pCount=1000;
					getSpots(tFil,tNeed, tFilMode, tSortDir, tSort);
					  $.post("/programs/SetSettings.php",{field: "SpotsSortDir", radio: tMyRadio, data: tSortDir, table: "MySettings"},function(response){
						  $.post("/programs/SetSettings.php",{field: "SpotsSort", radio: tMyRadio, data: tSort, table: "MySettings"},function(response){
						  });
					  });
				 });
			}

			function fillDescription(){
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'ClusterID', table: 'MySettings'}, function(response){
					var clusterID=response;
					$.post('/programs/GetCluster.php',{id: clusterID, field: 'NodeCall', table: 'Clusters'},function(response) {
						var tClusterNode=response;
						var tFilx='';
						if (tFil=='followMe'){
							tFilx='Follow Me';
						}else if (tFil=='hf'){
							tFilx='HF';
						}else if (tFil=='vhfUHF'){
							tFilx='VHF-UHF';
						}else if (tFil=='allSpots'){
							tFilx='All Spots';
						}else if (tFil=='warc'){
							tFilx='WARC';
						}else if (tFil=='low'){
							tFilx='Low';
						}else if (tFil=='high'){
							tFilx='High';
						}else{
							tFilx=tFil+'M';
						}
						var tNeedx='no status';
						if (tNeed=='callWorked'){
							tNeedx="Call Worked";
						}else if (tNeed=='callConfirmed'){
							tNeedx="Call Confirmed";
						}else if (tNeed=='callWorkedBand'){
							tNeedx="Call Worked this Band";
						}else if (tNeed=='callConfirmedBand'){
							tNeedx="Call Confirmed this Band";
						}else if (tNeed=='entityWorked'){
							tNeedx="Entity NOT Worked";
						}else if (tNeed=='entityWorkedBand'){
							tNeedx="Entity NOT Worked this Band";
						}else if (tNeed=='entityConfirmed'){
							tNeedx="Entity NOT Confirmed";
						}else if (tNeed=='entityConfirmedBand'){
							tNeedx="Entity NOT Confirmed this Band";
						}
						var tModex='';
						if (tFilMode=='cw'){
							tModex='on CW';
						}else if (tFilMode=='phone'){
							tModex='on Phone';
						}else if (tFilMode=='follow'){
							tModex='on '+tCurMode;
						};
						var descr=tClusterNode+" SPOTS";
						$('#call').text(descr);
						descr=tFilx+': ' +tNeedx+' '+tModex;
						$('#descr').text(descr);
					});
				});
			}
			
			function getSpots(tBandT,tNeedT, tModeT, tSortDirT, tSortT){
				var tFilterOut=getBandFilter(tBandT)
				var tFilEnc=tFilterOut;
				var tModeFilter=tModeT;
				if (tModeT=='follow'){
				   tModeFilter=tCurMode; 
				}
				$.post('/programs/getSpotList.php',{radio: tMyRadio, folder: 'Inbox', order: 'Webdate', need: tNeedT, band: tFilEnc, mode: tModeFilter, direction: tSortDirT, sort: tSortT },function(response) {
					getSubSpots(response);
				})
				fillDescription();
			};

			  $.getScript("/js/addPeriods.js");
			$(document).on('click', '#connectButton', function() {
				if (tNoInternet==1){
					$("#modalA-body").html("<br>RSS cannot reach the Internet so can't connect to the Telnet site for spots.<br><br>");			  				
					$("#modalA-title").html("No Internet");
					  $("#myModalAlert").modal({show:true});
				}else{
					if (tCall.toLowerCase()=='admin'){
						$("#modalA-body").html("Please enter your call in SETTINGS>Accounts>Account Editor. Spots NOT started.");			  				
						$("#modalA-title").html("Spots Status");
						  $("#myModalAlert").modal({show:true});
					}else{
						  $.post('./programs/SpotsStart.php', {action: 'start', radio: tMyRadio, call: tCall}, function(response) {
							$("#modalA-body").html(response);			  				
							$("#modalA-title").html("Spots Status");
							  $("#myModalAlert").modal({show:true});
						});
					};
				};
			 });	
	
			$(document).on('click', '#disconnectButton', function() {
				  $.post('/programs/SpotsStart.php', {action: 'stop', radio: tMyRadio, call: tCall}, function(response) {
						$("#modalA-body").html(response);			  				
						$("#modalA-title").html("Spots Status");
						  $("#myModalAlert").modal({show:true});//			  				alert(response);
				});
			});
	

	</script>
</head>

<body class="body-black" id="spots">
	<?php require $dRoot . "/includes/header.php"; ?>
   <div class="container-fluid">
		<div class="row" style="margin-top:10px;">
			<div class="col-12 col-md-1 btn-padding">
				<span class="label label-success text-white" id="call" style="cursor: default; text-center; margin-top:10px;"></span>
			</div>
			<div class="col-12 col-md-4 text-center text-spacer">
				<button class="btn btn-outline-success btn-sm my-1 my-sm-0 text-white" id="connectButton"  
					title="Click to connect to Cluster" type="button">
					<i class="fas fa-play"></i>
					<u>C</u>onnect
				</button>
				<button class="btn btn-outline-danger btn-sm my-1 my-sm-0 text-white" id="disconnectButton"  
					title="Click to disconnect from Cluster" type="button">
					<i class="fas fa-stop"></i>
					<u>D</u>isconnect
				</button>
			</div>
			<div class="col-12 col-md-2 btn-padding">
				<span class="label label-success text-white" id="descr" style="cursor: default; text-center; margin-top:10px;"></span>
			</div>
			<div class="col-12 col-md-3 btn-padding">
				<div class="dropdown" style="text-center; margin-top:10px;">
					<button class="btn btn-color dropdown-toggle hButton" id="pgNum" data-size="3" type="button" 
						title="Choose Band Filter" data-toggle="dropdown"><i class="fas fa-filter fa-lg"></i>
					</button>
					<ul class="dropdown-menu menu-scroll" id="fnList">
						<li role="presentation" class="dropdown-header">Band Filter</li>
						<li class="band" id='allSpots'><a class='dropdown-item ' id='fn' href='#'>All Spots</a></li>
						<li class="band" id='followMe'><a class='dropdown-item ' id='fn' href='#'>Follow Me</a></li>
						<li class="band" id='hf'><a class='dropdown-item ' id='fn' href='#'>HF</a></li>
						<li class="band" id='low'><a class='dropdown-item ' id='fn' href='#'>Low Bands</a></li>
						<li class="band" id='high'><a class='dropdown-item ' id='fn' href='#'>High Bands</a></li>
						<li class="band" id='warc'><a class='dropdown-item ' id='fn' href='#'>WARC</a></li>
						<li class="band" id='vhfUHF'><a class='dropdown-item ' id='fn' href='#'>VHF/UHF</a></li>
						<li class="band" id='160'><a class='dropdown-item ' id='fn' href='#'>160</a></li>
						<li class="band" id='80'><a class='dropdown-item ' id='fn' href='#'>80</a></li>
						<li class="band" id='60'><a class='dropdown-item ' id='fn' href='#'>60</a></li>
						<li class="band" id='40'><a class='dropdown-item ' id='fn' href='#'>40</a></li>
						<li class="band" id='30'><a class='dropdown-item ' id='fn' href='#'>30</a></li>
						<li class="band" id='20'><a class='dropdown-item ' id='fn' href='#'>20</a></li>
						<li class="band" id='17'><a class='dropdown-item ' id='fn' href='#'>17</a></li>
						<li class="band" id='15'><a class='dropdown-item ' id='fn' href='#'>15</a></li>
						<li class="band" id='12'><a class='dropdown-item ' id='fn' href='#'>12</a></li>
						<li class="band" id='10'><a class='dropdown-item ' id='fn' href='#'>10</a></li>
						<li class="band" id='6'><a class='dropdown-item ' id='fn' href='#'>6</a></li>
						<li class="band" id='2'><a class='dropdown-item ' id='fn' href='#'>2</a></li>
						<li role="presentation" class="dropdown-header">Mode Filter</li>
						<li class="mode" id='all'><a class='dropdown-item ' id='fn' href='#'>Show All</a></li>
						<li class="mode" id='follow'><a class='dropdown-item ' id='fn' href='#'>Follow Me</a></li>
						<li class="mode" id='cw'><a class='dropdown-item ' id='fn' href='#'>Only CW</a></li>
						<li class="mode" id='phone'><a class='dropdown-item ' id='fn' href='#'>Only Phone</a></li>
						<li class="mode" id='digital'><a class='dropdown-item ' id='fn' href='#'>Only Digital</a></li>
					</ul>
				</div>
				<div class="dropdown">
					<button class="btn btn-color dropdown-toggle hButton" id="selStyle" data-size="3" type="button"  
						title="Choose Worked Status" data-toggle="dropdown"><i class="fas fa-paint-brush fa-lg"></i>
					</button>
					<ul class="dropdown-menu menu-scroll" id="fnList">
						<li class="needed" id='noColor'><a class='dropdown-item' id='fn' href='#'>
							No color</a></li>
						<li class="needed" id='callWorked'><a class='dropdown-item' id='fn' href='#'>
							Teal: Call Worked</a></li>
						<li class="needed" id='callConfirmed'><a class='dropdown-item' id='fn' href='#'>
							Green: Call Confirmed</a></li>
						<li class="needed" id='callWorkedBand'><a class='dropdown-item ' id='fn' href='#'>
							Orange: Call Worked: this Band</a></li>
						<li class="needed" id='callConfirmedBand'><a class='dropdown-item ' id='fn' href='#'>
							Red: Call Confirmed: this Band</a></li>
						<li class="needed" id='entityWorked'><a class='dropdown-item ' id='fn' href='#'>
							Teal: Entity NOT Worked</a></li>
						<li class="needed" id='entityConfirmed'><a class='dropdown-item ' id='fn' href='#'>
							Green: Entity NOT Confirmed</a></li>
						<li class="needed" id='entityWorkedBand'><a class='dropdown-item ' id='fn' href='#'>
							Orange: Entity NOT Worked: this Band</a></li>
						<li class="needed" id='entityConfirmedBand'><a class='dropdown-item ' id='fn' href='#'>
							Red: Entity NOT Confirmed: this Band</a></li>
					</ul>
				</div>
			</div>
			  <div class="col d-none d-lg-block col-lg-2 text-spacer">
				<div class='text-white' id='BSFreqDisp'>14.025.333</div>
			  </div>
		</div>
		<div class="row" style="margin-top:10px;">
			<div class="col-xs-12 col-lg-9" id='sp' style="overflow-y: scroll; height:800px;" >
				<div id='tbody'></div>
			  </div>
			  <div class="col-xs-12 col-lg-3" id='bs' style="overflow-y: scroll; height:800px;">
				  <div class="row">
					  <div class="col">
						  <img src="./Images/BandMapperG.jpg" class="img rounded" id="bandCanvas" />
						<div id='frequencies'></div>;
						<div id='spots'></div>;
						<div id='marker'></div>;
					  </div>
				  </div>
			</div>
		</div>
	</div>
	<?php require $dRoot . "/includes/footer.php"; ?>
		<br />

	<?php require $dRoot . "/includes/modal.txt"; ?>
	<?php require $dRoot . "/includes/modalAlert.txt"; ?>
</body>
</html>
<?php require $dRoot . "/includes/modalCancelOnly.txt"; ?>
<script src="./Bootstrap/popper.min.js" type="text/javascript">
</script><script src="./Bootstrap/jquery-ui.js" type="text/javascript">
</script><script src="./Bootstrap/bootstrap.min.js" type="text/javascript"></script>
<script type="text/javascript" src="js/jquery.line.js"></script>
<script src="js/nav-active.js"></script>

