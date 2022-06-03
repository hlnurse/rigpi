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
if (!isset($GLOBALS['htmlPath'])){
	$GLOBALS['htmlPath']=$_SERVER['DOCUMENT_ROOT'];
}
if (!empty($_SESSION['firstuse'])){
	$firstUse=$_SESSION['firstUse'];
}else{
	$firstUse=1;
}
$_SESSION['firstUse']=0;
if ($firstUse==1){
	$tColsExec="/usr/share/rigpi/col.sh";
	$cols = shell_exec($tColsExec);
}
$dRoot=$GLOBALS['htmlPath'];
$wanIP="http://rigpi.dyndns.org:8081";
require ($dRoot.'/programs/GetMyRadioFunc.php');
if (!empty($_GET['c']) && !empty($_GET["x"])){
	$tCall=strtoupper($_GET["c"]);
	$tUserName=$_GET["x"];
}else{
	$tCall="";
	$tUserName="";
}
require_once($dRoot.'/classes/Membership.php');
$membership = New Membership();
if (!$membership->confirm_Member($tUserName)){
	exit;
}
require_once($dRoot.'/programs/GetUserFieldFunc.php');
$theme=getUserField($tUserName,'Theme');
if ($theme==0){
	$tThemePanel="panelNUMOrange.json";
	$tThemeMeter="smeterOrange.json";
}else if($theme==1){
	$tThemePanel="panelNUMNite.json";
	$tThemeMeter="smeterNight.json";
}else if($theme==2){
	$tThemePanel="panelNUMLCD.json";
	$tThemeMeter="smeterLCD.json";
}else if($theme==3){
	$tThemePanel="panelNUMHigh.json";
	$tThemeMeter="smeterHigh.json";
}	
require_once($dRoot."/classes/MysqliDb.php");	
require($dRoot."/programs/sqldata.php");
$db = new MysqliDb ('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
$db->where ("Username", $tUserName);
$row = $db->getOne ("Users");
$level='10';
if ($row) {
    $level=$row['Access_Level'];
}
$db->where("IsAlive","1");
$rows=$db->get("RadioInterface");
$online=$db->count;
?>

<!--This is the Tuner window -->

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php echo $tCall?> RigPi Tuner</title>
        <meta name="description" content="RigPi Tuner">
        <meta name="author" content="Howard Nurse, W6HN">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="/Bootstrap/bootstrap.min.css">
        <link rel="stylesheet" href="/Bootstrap/bootstrap-slider.css">
        <script src="/Bootstrap/jquery.min.js" ></script>
	<script src="/js/bootstrap-slider.js" type="text/javascript"></script>

        <link rel="shortcut icon" href="./favicon.ico">
        <link rel="apple-touch-icon" href="./favicon.ico">
		<?php require($dRoot.'/includes/styles.php');?>
        <script src="/js/mscorlib.js" type="text/javascript"></script> <!-- causes sliders to not slide on Chrome, have to click -->
        <script src="/js/PerfectWidgets.js" type="text/javascript"></script>
		<script defer src="./awe/js/all.js" ></script>
		<link href="./awe/css/all.css" rel="stylesheet">
		<link href="./awe/css/fontawesome.css" rel="stylesheet">
		<link href="./awe/css/solid.css" rel="stylesheet">	
        <script type="text/javascript">
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
            var tPTT;
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
            var tCWPort="/dev/ttyS0";
            var tMyRotorPort="";
            var tUserName="<?php echo $tUserName;?>";
            var tAccessLevel="<?php echo $level;?>";
            var tFirstUse="<?php echo $firstUse;?>";
            var tMyCall="<?php echo $tCall;?>";
            var tOnline="<?php echo $online;?>";
            var tUser='';
            var tSplitOn=0;
            var tTuneFromTap=0;
            var classTimer;
            var trOn=0;
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
            var slider='';
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
			var tKnobLock=0;
			var mtrLabel="S-Meter";
			var outputAF, sliderAF, outputPwrOut, sliderPwrOut, sliderMic, outputMic, outputRF, sliderRF

            $(document).ready(function()
            {
var mySlider = $("input.slider").bootstrapSlider();
/*				sliderRF = document.getElementById("myRange1");
				outputRF = document.getElementById("myRFVal");
				sliderRF.oninput = function() {
					if (tNoRadio==0){
						waitRefresh=6;
						outputRF.innerHTML=this.value
						$.post("/programs/SetSettings.php", {field: "RFGain", radio: tMyRadio, data: this.value, table: "RadioInterface"}, function(response){
						});
					}
				};   
*/				sliderAF = document.getElementById("myRange2");
				outputAF = document.getElementById("myAFVal");
				sliderAF.oninput = function() {
					if (tNoRadio==0){
						waitRefresh=6;
						outputAF.innerHTML=this.value
						$.post("/programs/SetSettings.php", {field: "AFGain", radio: tMyRadio, data: this.value, table: "RadioInterface"}, function(response){
						});
					}
				};   
				sliderPwrOut = document.getElementById("myRange3");
				outputPwrOut = document.getElementById("myPwrOutVal");
				sliderPwrOut.oninput = function() {
					if (tNoRadio==0){
						waitRefresh=6;
						outputPwrOut.innerHTML = this.value;
						$.post("/programs/SetSettings.php", {field: "PwrOut", radio: tMyRadio, data: this.value, table: "RadioInterface"}, function(response){
						});
					}
				};   
				sliderMic = document.getElementById("myRange4");
				outputMic = document.getElementById("myMicVal");
				sliderMic.oninput = function() {
					waitRefresh=6;
					outputMic.innerHTML=this.value;
					$.post("/programs/SetSettings.php", {field: "MicLvl", radio: tMyRadio, data: this.value, table: "RadioInterface"}, function(response){
					});
				};   
	            if (tAccessLevel==1 && tFirstUse>0){
		            	$.post('/programs/testInternet.php',function(response){
		            		if (response !=0){
		            			tInternet=0;
		            			tNoReboot=1;
								$("#modalA-title").html("No Internet");
								$("#modalA-body").html("<br>RSS does not find an Internet connection. <p><p>"+
									"Certain functions such as Spots, QRZ Call Lookup, Version updating, and "+
									"Google maps will not function without a connection. (Try refreshing the Tuner page.)<p>");
							  	$("#myModalAlert").modal({show:true});
		            		}else{
		            			$.post('/programs/version.php', function(response){
			  					var tV=response;
			  					tNoReboot=0;
			  					$.post('/programs/GetUpdateVersion.php', function(response){
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
					tMyRadio=response;
/*					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'RFGain', table: 'RadioInterface'}, function(response){
						outputRF.innerHTML = response;
						sliderRF.value=response;
					});
*/					
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'AFGain', table: 'RadioInterface'}, function(response){
						outputAF.innerHTML = response;
						sliderAF.value=response;
					});
					
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'PwrOut', table: 'RadioInterface'}, function(response){
						outputPwrOut.innerHTML = response;
						sliderPwrOut.value=response;
					});
					
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'AFGain', table: 'RadioInterface'}, function(response){
						outputMic.innerHTML = response;
						sliderMic.value=response;
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
						}else if (response=="l RFPOWER_DEFAULT")
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
							$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tSpeed, table: "Keyer"});
							$.post("/programs/SetSettings.php", {field: "CWChangeCk", radio: tMyRadio, data: 1, table: "Keyer"});
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
										}else{
											$('#i1').attr('src', wanIP);
											$('#i2').attr('src', "");
										}
									}else{ //normal screens
										if (wIP==aData[2]){
											if (showVideo==2){
												$('#i2').attr('src', lanIP);
												$('#i1').attr('src', "");
											}else{
												$('#i1').attr('src', lanIP);
												$('#i2').attr('src', "");
											}
										}else{
											if (showVideo==2){
												$('#i2').attr('src', wanIP);
												$('#i1').attr('src', "");
											}else{
												$('#i1').attr('src', wanIP);
												$('#i2').attr('src', "");
											}
										};
									};
									
								}else{
									$('#i1').attr('src', '');
									$('#i2').attr('src', '');
								};
							});
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
		                
		                tLine1=ld4;
		                tLine2=lu4;
		                stLine1=sld4;
		                stLine2=slu4;
		                doDigit(1000, 1);
		                doSubDigit(1000, 1);
		                
		                plus = pknob.getByName("PlusButton");
		                plus.addOnClickHandler(plusClicked);
		                minus = pknob.getByName("MinusButton");
		                minus.addOnClickHandler(minusClicked);
		                ptt = pknob.getByName("PTT");
		                ptt1 = pknob.getByName("PTTOn");
		                cRX=ppanel.getByName("TR");
		                ptt.addOnClickHandler(function (sender, e) 
		                {
		                    var which1=sender.getPressed();
							var rec = document.getElementById('dKnob').getBoundingClientRect();
							var position=rec.top + window.scrollY - window.pageYOffset;
		                    if (which1==true && tDisconnected==0 && position>50)  //last && is to prevent PTT whene under hamburger menu on phones
		                    {
			                    if (xmit==true){
			                        setPTT(0);
			                    }else{
			                        setPTT(1);
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
					                if (showVideo==1 || showVideo==3){
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
					                if (showVideo==1){
										$("#videoPanel").addClass('d-none');
										$("#videoMeter").addClass('d-none');
										$("#dMeter").addClass('d-none');
										$("#dPanel").removeClass('d-none');
										$(".mycall").addClass('d-none');
										$("#lowerText").removeClass('d-none');
					                }else if (showVideo==2){ //meter
										$("#videoPanel").removeClass('d-none');
										$("#videoMeter").addClass('d-none');
										$("#dPanel").removeClass('d-none');
										$("#dMeter").addClass('d-none');
										$("#lowerText").removeClass('d-none');
										$(".mycall").addClass('d-none');
					                }else if (showVideo==3){ //panel 
										$("#videoPanel").removeClass('d-none');
										$("#videoMeter").addClass('d-none');
										$("#dPanel").addClass('d-none');
										$("#dMeter").addClass('d-none');
										$("#lowerText").removeClass('d-none');
										$(".mycall").addClass('d-none');
					                };
					            }else{ //1-0 digits will show
					                var st=$(".status");
						  			st.removeClass('d-none');
					                $("#topVFO").html(bVFO);
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
					                $("#colPan").removeClass("col-sm-12");
					                $("#colPan").addClass("col-sm-4");
					                $("#colKnob").removeClass("col-sm-12");
					                $("#colKnob").addClass("col-sm-4");
					                $("#colVid").removeClass("col-sm-12");
					                $("#colVid").addClass("col-sm-4");
					                if (showVideo==1){
										$("#videoPanel").addClass('d-none');
										$("#videoMeter").addClass('d-none');
										$("#dMeter").removeClass('d-none');
										$("#dPanel").removeClass('d-none');
										$(".mycall").removeClass('d-none');
										$("#lowerText").removeClass('d-none');
					                }else if (showVideo==2){ //meter
										$("#videoPanel").addClass('d-none');
										$("#videoMeter").removeClass('d-none');
										$("#dPanel").removeClass('d-none');
										$("#dMeter").addClass('d-none');
										$("#lowerText").removeClass('d-none');
										$(".mycall").addClass('d-none');
					                }else if (showVideo==3){ //panel same as meter
										$("#videoPanel").removeClass('d-none');
										$("#videoMeter").addClass('d-none');
										$("#dPanel").addClass('d-none');
										$("#dMeter").removeClass('d-none');
										$("#lowerText").removeClass('d-none');
										$(".mycall").removeClass('d-none');
					                };
				                };
			                
				            });
		                });
		            };
					
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
						$('#myRadio').text("Radio: "+response);
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
			        
			        $.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'PTTMode', table: 'MySettings'}, function(response)
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
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MacroBank', table: 'RadioInterface'}, function(response){
							mBank=response
							$('#myBank').text("Macro Bank: "+response);
							loadMacroBank(mBank);
					})

					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'AFGain', table: 'RadioInterface'}, function(response){
							sliderAF.value=response;
					})

					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'PwrOut', table: 'RadioInterface'}, function(response){
							sliderPwrOut.value=response;
					})

/*					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'RFGain', table: 'RadioInterface'}, function(response){
							sliderRF.value=response;
					})
*/
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MicLvl', table: 'RadioInterface'}, function(response){
							sliderMic.value=response;
					})

/*	                $.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'ClusterID', table: 'MySettings'}, function(response){
						var clusterID=response;
						$.post('./programs/GetCluster.php',{id: clusterID, field: 'NodeCall', table: 'Clusters'},function(response) {
							$('#mySpots').text("Spots: "+response);
			            })
			        })
*/			        $.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
			        {
						$('#searchText').val(response.toUpperCase());
				    });

				});

				var jsonKnob='';
				var jsonLED='';
	            if (notGotPerfectWidgets==0){

					<?php include ($dRoot."/includes/$tThemePanel");?>;
					<?php include ($dRoot."/includes/$tThemeMeter");?>;
					<?php include ($dRoot."/includes/bigPTT1.json");?>;
					<?php include ($dRoot."/includes/led.json");?>;
					
					$("#playOK").addClass('d-none');
					$("#play").removeClass('d-none');
					///////////////////////////////////////
					// The three following PerfectWidgets components are proprietary included under a commercial license and are NOT open source
				    pmeter = new PerfectWidgets.Widget("dMeter", jsonSMeter);
					ppanel = new PerfectWidgets.Widget("dPanel", jsonPanel);
	                pknob = new PerfectWidgets.Widget("dKnob", jsonKnob);
	                pled = new PerfectWidgets.Widget("dLED", jsonLED);
	                //End of commercially licensed components
	                ///////////////////////////////////////
					slider = pknob.getByName("Slider1");
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
					        	
				        }
	                    var cFreq2m=("000000000" + tMain).slice(-9);
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
					        	
				        }
	                    var cFreq2s=("000000000" + tSub).slice(-9);
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
				}

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
                
               $(document).on('click', '#myBank', function() 
                {
		            mBank=parseInt(mBank)+1;
	            	if (mBank==5){mBank=1};
	            	$('#myBank').text("Macro Bank: "+mBank);
					loadMacroBank(mBank);
	
	  				$.post("/programs/SetSettings.php", {field: "MacroBank", radio: tMyRadio, data: mBank, table: "RadioInterface"});
               });	
                
               $(document).on('click', '#myLock', function() 
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
	                slider.addValueChangedHandler(
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
	                            cFreq2=("000000000" + cFreq1).slice(-9);
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
	                            cFreq2=("000000000" + cFreq1).slice(-9);
	                            cFreq2=addPeriods(cFreq2);
	                            knobOld=parseInt(knobVal/10);
	                            cSub.setText(cFreq2);
	                            cSub.setNeedRepaint(true);
	                            cSub.refreshElement();
				                $.post("/programs/SetSettings.php", {field: "SubOut", radio: tMyRadio, data: cFreq1, table: "RadioInterface"});
                            }
                            waitRefresh=0;
                        }
                        if (showVideo==1 || showVideo==3){
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

                function sup100Clicked()
                {
	                tTuneFromTap=1;
	                doSubDigit(100000000, 0);
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
                

                //vfo
                $(document).on('click', '#connect', function() 
                {
                	tDisconnected=0;
					if (tMyKeyer=='RigPi Keyer'){
						tMyKeyer='rpk';
						tCWPort='/dev/ttyS0';
						$.post('./programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKSpeed'}, function(response) {
							var tSpeed=response;
							$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tSpeed, table: "Keyer"});
							$.post("/programs/SetSettings.php", {field: "CWChangeCk", radio: tMyRadio, data: 1, table: "Keyer"});
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
                });	

                $(document).on('click', '#disconnect', function() 
                {
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
				<?php require ($dRoot."/includes/buildMacros.php");?>
				
				
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
                    getBandMemory('160');
                });	

                $(document).on('click', '#80Button', function() 
                {
                    getBandMemory('80');
                });	

                $(document).on('click', '#60Button', function() 
                {
                    getBandMemory('60');
                });	

                $(document).on('click', '#40Button', function() 
                {
                    getBandMemory('40');
                });	

                $(document).on('click', '#30Button', function() 
                {
                    getBandMemory('30');
                });	

                $(document).on('click', '#20Button', function() 
                {
                    getBandMemory('20');
                });	

                $(document).on('click', '#17Button', function() 
                {
                    getBandMemory('17');
                });	

                $(document).on('click', '#15Button', function() 
                {
                    getBandMemory('15');
                });	

                $(document).on('click', '#12Button', function() 
                {
                    getBandMemory('12');
                });	

                $(document).on('click', '#10Button', function() 
                {
                    getBandMemory('10');
                });	

                $(document).on('click', '#6Button', function() 
                {
                    getBandMemory('6');
                });	

                $(document).on('click', '#2Button', function() 
                {
                    getBandMemory('2');
                });	

                $(document).on('click', '#cwButton', function() 
                {
                    $.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'CW', table: "RadioInterface"});
                });	

                $(document).on('click', '#fmButton', function() 
                {
                    $.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'FM', table: "RadioInterface"});
                });	

                $(document).on('click', '#lsbButton', function() 
                {
                    $.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'LSB', table: "RadioInterface"});
                });	

                $(document).on('click', '#usbButton', function() 
                {
                    $.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'USB', table: "RadioInterface"});
                });	

                $(document).on('click', '#cwrButton', function() 
                {
                    $.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'CWR', table: "RadioInterface"});
                });	

                $(document).on('click', '#amButton', function() 
                {
                    $.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'AM', table: "RadioInterface"});
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
		            setTopBand(nBand+"m");
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
					setTopBand(tBand+'m');	
				}
				$.post('./programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKSpeed'}, function(response) {
					var tSpeed=response;
					$.post('./programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKPot'}, function(response) {
						if (speedPot!=response){
							console.log("sp2: "+speedPot+ " "+response);
							speedPot=response;
							tSpeed=speedPot;
							$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKMinWPM'}, function(response) {
								var tMin=response;
								tSpeed=parseInt(tSpeed)+parseInt(tMin);
								$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tSpeed, table: "Keyer"});
								$.post("/programs/SetSettings.php", {field: "CWChangeCk", radio: tMyRadio, data: 1, table: "Keyer"});
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
				    trOn=false;
				    spacePTT=0;
                    setPTT(0);
					return false;
			    }
			});			


		    windowLoaded=true;
			tUpdate = setInterval(updateTimer,500);


			$.post('/programs/GetRadioCaps.php', {r: tMyRadio, q:'radio'}, function(response) {
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
	            
	            $(document).keydown(function(e){
		            var t=e.key;
		            e.multiple
		            var w=e.which;
		            if (w==32){
			            tIgnoreRepeating=true;
			            trOn=1;
			            if (spacePTT==0){
		                    if (notGotPerfectWidgets==0){
		                    	setPTT(1);
		                    	waitRefresh=10;
		                    }
				            spacePTT=1;
		                }else{
		                    if (notGotPerfectWidgets==0){
		                    	setPTT(0);
		                    	waitRefresh=10;
		                    }
		                    spacePTT=0;
			            }
	/*		            clearTimeout(lastSpaceTimer); //timer because in some browsers multiple keyups even though keydown is constant
			            lastSpaceTimer=setTimeout(function(){
					    	setPTT(0);
					    	spacePTT=0;
				            waitRefresh=10;
			            }, 600)
	*/		            return false;
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
					            	doSubDigit(1,1);
					            	break;
				            }
			            }
			            return false;
		            }else if (w==39){
			            if (tSplitOn==0){
				            switch (tLine1){
					            case ld1:
					            	doDigit(100000000,1);
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
				            }
				        }else{
				            switch (stLine1){
					            case sld1:
					            	doSubDigit(100000000,1);
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
		
		function padDigits(number, digits) {
			return Array(Math.max(digits - String(number).length + 1, 0)).join(0) + number;
		};
		
		$.getScript("/js/modalLoad.js");
		function setPTT(state){
			if (notGotPerfectWidgets==0 && tAccessLevel < 4){
				var ttPTT=tMyRadio;
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
						ttPTT=tMyRadio;
					}
				}
            	if (state==1){
            		tKnobPTT=1;
                    xmit=true;
//	                    spacePTT=0;
                    trXmit=true;
                    $.post("/programs/SetSettings.php", {field: "PTTOut", radio: ttPTT, data: "1", table: "RadioInterface"});
            		if (tMyPTT==1){
            			$.post('/programs/doGPIOPTT.php', {PTTControl: "on"});
            		}
            		cRX.setText("XMIT");
            		cRX.setNeedRepaint(true);
            		cRX.refreshElement();
					ptt1.setVisible(true);
            		ptt.setVisible(false);
            	}else{
          			tKnobPTT=0;
                    xmit=false;
                    trXmit=false;
                    $.post("/programs/SetSettings.php", {field: "PTTOut", radio: ttPTT, data: "0", table: "RadioInterface"});
          			if (tMyPTT==1)
          			{
           				$.post('/programs/doGPIOPTT.php', {PTTControl: "off"});
          			}
          			cRX.setText("RCV");
           			cRX.setNeedRepaint(true);
           			cRX.refreshElement();
					ptt1.setVisible(false);
           			ptt.setVisible(true);
            	}
  	            waitRefresh=4;
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
	
        var aMCommands=[];
 		function processCommand(which)
        {
			var tMe=tMyCall;
			var tWhat = which.replace(/'\*/g, tMe);
			which = tWhat.replace(/'X/g,$('#searchText').val());
            var tPre=which.substring(0, 1);
            var tPost=which.substring(1);
			if (tPre=='$'){
                $.post("/programs/ConcatSettings.php", {field: "CWIn", radio: tMyRadioCW, data: tPost, table: "RadioInterface"});
                return false;
			}
            if (which=="*PS1;")
            {
				if (tRadioModel !="NET rigctl"){
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
            	$('#myBank').text("Macro Bank: "+mBank);
				loadMacroBank(mBank);

  				$.post("/programs/SetSettings.php", {field: "MacroBank", radio: tMyRadio, data: mBank, table: "RadioInterface"});
//  				location.reload(); //required to accomodate BANK buttons in different places
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
						setPTT(0);
						var tNum=String.fromCharCode(10);
						$.post("./programs/SetSettings.php", {field: "CWIn", radio: tMyRadioCW, data: tNum, table: "RadioInterface"});
					}else if (tPost=='TUNE'){
						var tNum=String.fromCharCode(11)+String.fromCharCode(1);
						$.post("./programs/SetSettings.php", {field: "CWIn", radio: tMyRadioCW, data: tNum, table: "RadioInterface"});
					}
                	if (tPost=="T/R" && tDisconnected==0 && tAccessLevel<4){
                		if (trXmit==true){
                			trXmit=false;
                			trOn=0;
			  				if (tMyPTT==1){
			  					$.post('/programs/doGPIOPTT.php', {PTTControl: "off"}); 			  				
			  				}
                			$.post("/programs/SetSettings.php", {field: "PTTOut", radio: tMyRadio, data: "0", table: "RadioInterface"});
							if (notGotPerfectWidgets==0){
								setPTT(0);
	                		}
                		}else{
                			trXmit=true;
                			trOn=1;
			  				if (tMyPTT==1){
			  					$.post('/programs/doGPIOPTT.php', {PTTControl: "on"}); 			  				
			  				}
                			$.post("/programs/SetSettings.php", {field: "PTTOut", radio: tMyRadio, data: "1", table: "RadioInterface"});
							if (notGotPerfectWidgets==0){
								setPTT(1);
	            			};
	                	}
                	}
					break;
				}
            }
        }
        
		function loadMacroBank(which)
        {
            $.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'Macros'+which}, function(response)
            {
				var tMacros=decodeURIComponent((response+'').replace(/\+/g,'%20'));
				var aMacros=tMacros.split('~');
				var mBtn;
				aMCommands=[];
				for (i = 0; i < 32; i++) {
					var mID='m'+(i+1)+'Button';
					var tLabel = aMacros[i];
					tLabel=tLabel.split('|');
					var btn =document.getElementById(mID);
					btn.innerHTML=tLabel[0];
					if (tLabel[0]=="BANK"){
						mBtn=btn;
						mBtn.innerHTML="BANK "+mBank;
					}
					aMCommands.push(tLabel[1]);
				}				
				$.post('/programs/isSupported.php',{radio: tMyRadio, getSet: 'set', text:'RFPOWER('}, function(response){
					if (response==1){
						$("#Pwr").removeClass('d-none');
					}
				});
				$.post('/programs/isSupported.php',{radio: tMyRadio, getSet: 'set', text:'AF('}, function(response){
					if (response==1){
						$("#AF").removeClass('d-none');
					}
				});
				$.post('/programs/isSupported.php',{radio: tMyRadio, getSet: 'set', text:'RF('}, function(response){
					if (response==1){
						$("#RF").removeClass('d-none');
					}
				});
				$.post('/programs/isSupported.php',{radio: tMyRadio, getSet: 'set', text:'MICGAIN('}, function(response){
					if (response==1){
						$("#Mic").removeClass('d-none');
					}
				});
			})
        }



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
            cFreq2=("000000000" + cFreq1).slice(-9);
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
            cFreq2=("000000000" + cFreq1).slice(-9);
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
	        }else if (nFreq > 219000000 && nFreq < 225000000){
	            return "1.25";
	        }else {
	            return "UNK";
	        }
        } 

		function updateTimer()
		{
            if(tTrx==0)
            {	   
//	            console.log(waitRefresh);         
	            if (waitRefresh>0)
	            {
	                waitRefresh=waitRefresh-1;				
	                return;
	            }
	            if (waitRefresh<=0)
	            {
		            waitRefresh=0;
		        }
//		        console.log("timer");
                $.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:tMyCall }, function(response) 
                {
                    var tAData=response.split('`');
//                    console.log(response);
//					outputRF.innerHTML = tAData[11];
//					sliderRF.value=tAData[11];
					outputAF.innerHTML = tAData[12];
					sliderAF.value=tAData[12];
					outputPwrOut.innerHTML = tAData[13];
					sliderPwrOut.value=tAData[13];
					outputMic.innerHTML = tAData[14];
					sliderMic.value=tAData[14];

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
	                   if (tTrx==1){
							ptt1.setVisible(true);
		            		ptt.setVisible(false);
	                    }else{
							ptt1.setVisible(false);
		            		ptt.setVisible(true);
	                    }
                    }
                    var tRadioUpdate=tAData[8];
                    if (tRadioUpdate.length!=0){
						$("#modalA-body").html(tRadioUpdate);			  				
						$("#modalA-title").html("RigPi Report");
					  	$("#myModalAlert").modal({show:true});
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
                    tMode=tAData[3];
                    tPTT=tAData[7];
                    tBusy=tPTT;
                    tMain=("000000000" + tMain).slice(-9);
                    var tF=addPeriods(tMain);
		            if (notGotPerfectWidgets==1){
			            return;
		            }
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
                    var cFreq2s=("000000000" + tSub).slice(-9);
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
                    cSub.setNeedRepaint(true);
                    cSub.refreshElement();

                    var cMode=ppanel.getByName("Mode");
                    cMode.setText(tAData[3]);
                    cMode.setNeedRepaint(true);
                    cMode.refreshElement();
                    
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

                    if (trXmit==false){
	                    tPTT=tAData[7];
	                    if (tPTT==1 && spacePTT==0){
							setPTT(1);
						}
                    }

					if (tPTT==0){
                        var cMeterVal=pmeter.getByName("Slider1");
                        var mtr1=Number(tAData[4])+54;
                        mtr=255/100 * mtr1;
                        cMeterVal.setValue(mtr, true);
                        var cBarVal=ppanel.getByName("LinearLevel1");
                        mtr=Math.floor(mtr1/7);
                        mtr=mtr*7;
                        cBarVal.setValue(mtr, true);
	                    var cMeterLabel=pmeter.getByName("MtrFn");
			            cMeterLabel.setText("S-Meter");
			            cMeterLabel.setNeedRepaint(true);
			            cMeterLabel.refreshElement();
					}
                });
            }else{
               $.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:tMyCall }, function(response) 
                {
                    var tAData=response.split('`');
                    tTrx=tAData[9];
                    if (showVideo==1 || showVideo==3){
	                    var cMeterVal=pmeter.getByName("Slider1");
	                    var mtr1=parseInt(100*tAData[4],10)/tMeterCal;
	                    mtr=mtr1;//using mtr direct provides more accurate calibration, limited testing
	                    cMeterVal.setValue(mtr, true);
	                    var cBarVal=ppanel.getByName("LinearLevel1");
	                    mtr=Math.floor(mtr1/7);
	                    mtr=mtr*7;
	                    cBarVal.setValue(mtr, true);
	                    var cMeterLabel=pmeter.getByName("MtrFn");
			            cMeterLabel.setText(mtrLabel);
			            cMeterLabel.setNeedRepaint(true);
			            cMeterLabel.refreshElement();
                    }
//	                    if (trXmit==false){
                    tPTT=tAData[7];
//		                    if (tPTT==0 && spacePTT==0){
                    if (tTrx==0){
						setPTT(0);
						ptt1.setVisible(false);
	            		ptt.setVisible(true);
                    }
//	                    }
                    tBusy=tPTT;
                });
            }
            if (notGotPerfectWidgets==1){
	            return;
            }
           var now = new Date();
            var now_hours=now.getUTCHours();
            now_hours=("00" + now_hours).slice(-2);
            var now_minutes=now.getUTCMinutes();
            now_minutes=("00" + now_minutes).slice(-2);
            var timeUTC = ppanel.getByName("LocalTime");
            timeUTC.setText(now_hours+":"+now_minutes);
            timeUTC.setNeedRepaint(true);
            timeUTC.refreshElement();
            $("#fPanel5").text(now_hours+":"+now_minutes+'z');
        }

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
                	break;
                case 10:
                	ld=ld2;
                	lu=lu2;
                	break;
                case 100:
                	ld=ld3;
                	lu=lu3;
                	break;
                case 1000:
                	ld=ld4;
                	lu=lu4;
                	break;
                case 10000:
                	ld=ld5;
                	lu=lu5;
                	break;
                case 100000:
                	ld=ld6;
                	lu=lu6;
                	break;
                case 1000000:
                	ld=ld7;
                	lu=lu7;
                	break;
                case 10000000:
                	ld=ld8;
                	lu=lu8;
                	break;
                case 100000000:
                	ld=ld9;
                	lu=lu9;
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
			        	
			    	}
			        var cFreq2m=("000000000" + tMain).slice(-9);
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
				        	
			        }
			        var cFreq2s=("000000000" + tSub).slice(-9);
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
        <?php require($dRoot."/includes/header.php");?>
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
									<img class="rigvid" src="" alt="RigVideo" style="width:320px;height:240px;" id="i2">
								</div>
							</div>	
	                    </div>
	                </div>
	                <div class="row">
	                	<div class="col">
	                       <div class="mycall text-white-medium d-none" style="margin-top:-60px">
		                        <?php echo $tCall;?>
								<hr>
	 	                    </div>
	                        <div class="row" style="margin-top: 10px; margin-bottom: 10px;" id="lowerText">
	                        <div class="col-6" >
									<button class="btn btn-light btn-outline btn-block btn-small-color " style="margin-left: 17px; height:36px; margin-top:-2px" title="Change Bank" type="button">
										<span class="btn-small-color " id="myBank" style="font-size:15px;">
										</span>
									</button>
	                        	</div>
		                        <div class="col-6">
									<button class="btn tActive btn-outline-danger btn-block btn-small-lock-off"  id="myLock" style="font-size:15px; height:36px; margin-top:-2px" title="Lock" type="button">
										Knob Lock
									</button>
		                        </div>
	                    	</div>
	                	</div>
	                </div>
                </div>   	
                <div class="col-sm-4 mx-auto" id="colKnob">
                    <div class="row fixed mx-auto noselect" style="margin-left:-10px;" id="dKnob"></div>
                    <div class="row fixed mx-auto noselect d-none" id="dLED"></div>
                </div>
            </div>
			<div class="row" style="margin-top:10px">
				<div class="col-12" id="bottomVFO">
        		</div>
        	</div>
            <hr>
           <div id="macroDiv">
				<?php require($dRoot.'/includes/macroButtons.php');?>
            </div>
 	        <div class="status">
				<?php require($dRoot.'/includes/footer.php'); ?>
	        </div>
	    </div>
	</body>
   <?php require($dRoot.'/includes/modal.txt'); ?>
    <?php require($dRoot.'/includes/modalAlert.txt'); ?>
	<?php require($dRoot.'/includes/modalCancelAlert.txt'); ?>
	<?php require($dRoot.'/includes/modalCancelReboot.txt'); ?>
	<?php require($dRoot.'/includes/modalUpdate.txt'); ?>
	<?php require($dRoot.'/includes/modalCancelOnly.txt'); ?>
    <link rel="stylesheet" href="/Bootstrap/jquery-ui.css">
    <script src="/Bootstrap/jquery-ui.js"></script>
    <script src="/Bootstrap/bootstrap.min.js"></script>
    <script src="/js/nav-active.js"></script>
	<script src="/Bootstrap/popper.min.js"></script>
</html>
