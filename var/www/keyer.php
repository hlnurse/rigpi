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
	<link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">
    <script src="/Bootstrap/jquery.min.js" ></script>
	<link href="./awe/css/all.css" rel="stylesheet">
	<link href="./awe/css/fontawesome.css" rel="stylesheet">
	<link href="./awe/css/solid.css" rel="stylesheet">	
	<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">

	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="apple-touch-icon" href="/apple-touch-icon.png">
	<?php require $dRoot . "/includes/styles.php"; ?>
    <script type="text/javascript">
		var holdText="";
		var char="";
		var holdCW=false;
		var speedPot;
		var clearingLeft="0";
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
        var tSpeed=0;
        var tMinSpeed=5;
        var ptt;
        var ptt1;
        var cRX;
        var tMyPTT=1;
        var tAccessLevel="<?php echo $level; ?>";
        var tRadioPort=0; //used to redirect CW keying to actual radio port
        var mBank=1;
        var aMCommands=[];
        var tDisconnected=0;
        var trXmit=false;
        var tMyRotorRadio; //ve9gj
        var tRadioModel="";
        var waitRefresh=0;
		var outputAF, sliderAF, outputPwrOut, sliderPwrOut, sliderMic, outputMic, outputRF, sliderRF, tVal;
		var tSliderVal, tSliderStartVal;
		var sliderSpeedRef, sliderAFRef, sliderRFRef, sliderPwrOutRef, sliderMicRef, tMain, tMax
		var sliderAFGainOride, sliderRFGainOride, sliderPwrOutOride, sliderMicLvlOride;
		var latchBtn1=[],latchBtn2=[],latchBtn3=[],latchBtn4=[],latchBtn=[], btnLatchColor;
		var bEnable=[],aMacros=[];
		tKeyerMode=0;
        $(document).ready(function() {
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
				outputSpeed = document.getElementById("myKeyerSpeedVal");

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
			  			tMyRadioPort=1+(tMyRadioPort-4532)/2;
			  			if (tMyRadioPort>0){
							tMyRadio=tMyRadioPort;
						}else{
							tMyRadio=response;
						}
		  			}else{
		  				tMyRadio=response;
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
									max:sliderAFGainOride,
									range: 'min'
								});
								outputAF.innerHTML = response;
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
							console.log("change: "+tVal);
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
							console.log("slide: "+tVal);
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
					

					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MacroBank', table: 'RadioInterface'}, function(response){
//						mBank=response
//						$('#myBank').text("Macro Bank: "+response);
//						updateBank(mBank);
						mBank=response
						loadMacroBank(mBank);
						var mB='#myBank'+mBank;
						$(mB).removeClass('btn-color');
						$(mB).addClass('btn-info');
					})
					
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
		
			        $.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'RadioModel', table: 'MySettings'}, function(response)
			        {
						tRadioModel=response;
				    });
			        
					$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKPot'}, function(response) {
						speedPot=response;
					});
					
					$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKMinWPM'}, function(response) {
						tMinSpeed=parseInt(response);
						$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKWPMRange'}, function(response) {
							tMinSpeedRange=response;
							$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKSpeed'}, function(response) 
							{
								tMax=tMinSpeed+parseInt(tMinSpeedRange);
//								console.log("min: "+tMinSpeed);

								$( function() {
								    $("#sliderSpeed").slider({
										min: tMinSpeed,
										max: tMax,
										range: 'min',
										animate: true
									});
									$("#sliderSpeed").slider('value',response);
									outputSpeed.innerHTML = response+" WPM";
								});
								$("#sliderSpeed").on("click", function(){
										waitRefresh=2;
										if (tSliderStartVal<tSliderVal){
											tVal=$('#sliderSpeed').slider('value');
											
										}else{
											tVal=$('#sliderSpeed').slider('value');
										}
										if (tVal<tMinSpeed){
//											tVal=tMinSpeed;
										}
										if (tVal>tMax){
//											tVal=tMax;
										}
										outputSpeed.innerHTML=tVal+" WPM";
										$("#mySpeed").text(tVal+' WPM');
										$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tVal, table: "Keyer"}, function(response){
											});
										$.post("/programs/SetSettings.php", {field: "CWChangeCk", radio: tMyRadio, data: "1", table: "RadioInterface"});
										sliderSpeedRef=tVal
//										console.log("click: "+tVal);
//										$("#cwi").focus();
								});
									
								$("#sliderSpeed").on("slide",function(event,ui){
									waitRefresh=2
									tVal=$('#sliderSpeed').slider('value');
									outputSpeed.innerHTML=tVal+" WPM";
									tSliderVal=tVal;
									$("#mySpeed").text(tVal+' WPM');
//									$("#sliderSpeed").slider('value',tVal);
//									console.log("slide: "+tVal);
									
									$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tVal, table: "Keyer"});
									$.post("/programs/SetSettings.php", {field: "CWChangeCk", radio: tMyRadio, data: "1", table: "RadioInterface"});
									sliderSpeedRef=tVal
//									$("#cwi").focus();
								});

							});
						});
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
						        if (response1==1){
							        tKeyerOut="Remote Radio CW not shown"
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
								var tLabel = aMacros[i];
								tLabel=tLabel.split('|');
								var btn =document.getElementById(mID);
								btn.innerHTML=tLabel[0];
								if (tLabel[1]=="!BANK"){
									mBtn=btn;
									mBtn.innerHTML="BANK "+which;
								}
								aMCommands.push(tLabel[1]);
							}
						})
		    		}
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MacroBank', table: 'RadioInterface'}, function(response){
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
	                    return false;
	                } else  {
	                    return true;
	                }
	            });
	        });
			
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
				if (w>111 && w<121){
					x=w-111;
					x="F"+x+":";
					doFCheck(x);
				};
			});
			
			$(document).keyup(function(e) 
			{
				if (e.keyCode == 27) 
			    { // escape key maps to keycode `27`
					holdText='';
					var tNum=String.fromCharCode(10);
					$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: tNum, table: "RadioInterface"});
					setPTT(0);
					document.getElementById("cwi").focus();
			    }
			});	

			$(function(){
				var cwIText=$('#cwi').val();

				$('#cwi').keydown(function(e){
					var tWhich=e.keyCode;
					tC1=e.key.toUpperCase();
					if ((tWhich>47 && tWhich<91) || (tWhich>187 && tWhich < 192) || tWhich==32 || tWhich==8){ //javascript keycodes!
						if (tWhich==8){
							sendCWMessage("!"); //backspace is ! so chars in output buffer can be replaced
							return;
						}else{
							sendCWMessage(tC1);
						}
					}
				});
			});

			$(document).on('click', '#clearRightButton', function() {
				var input = document.getElementById("cwi");
				input.value='';
				holdText='';
				var tNum=String.fromCharCode(10);
				$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: tNum, table: "RadioInterface"});
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
				sendCWMessage("<");
				document.getElementById("cwi").focus();
			});	
			$(document).on('click', '#skButton', function() {
				sendCWMessage(">");
				document.getElementById("cwi").focus();
			});	
			$(document).on('click', '#knButton', function() {
				sendCWMessage("(");
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
				$('#myBank').text("Macro Bank: "+mBank);
				loadMacroBank(mBank);
				
				$.post("/programs/SetSettings.php", {field: "MacroBank", radio: tMyRadio, data: mBank, table: "RadioInterface"});
				$("#cwi").focus();
			});	

			$.post("/programs/GetUserField.php", {un:tUserName, field:'BandEnable'}, function(response)
			{
				bEnable=response.split(",");
				updateButtons();
			});
			
						//bands
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
			
			function updateButtons(){
				var bName="";
				for (i=0;i<12;i++){
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

			$(document).on('click', '#cwButton', function() {
				$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'CW', table: "RadioInterface"});
			});	
			$(document).on('click', '#fmButton', function() {
				$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'FM', table: "RadioInterface"});
			});	
			$(document).on('click', '#lsbButton', function() {
				$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: 'LSB', table: "RadioInterface"});
			});	
			$(document).on('click', '#usbButton', function() {
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
 			$.getScript("/js/modalLoad.js");
        }); //end ready
       
		function setPTT(state){
			if (tAccessLevel<4){
		    	if (state==1){
		            xmit=true;
		            trXmit=true;
		            $.post("/programs/SetSettings.php", {field: "PTTOut", radio: tMyRadio, data: "1", table: "RadioInterface"});
		    		if (tMyPTT==1){
		    			$.post('/programs/doGPIOPTT.php', {PTTControl: "on"});
		    		}
		    	}else{
		            xmit=false;
		            trXmit=false;
		            $.post("/programs/SetSettings.php", {field: "PTTOut", radio: tMyRadio, data: "0", table: "RadioInterface"});
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
				clearingLeft="0";
			});
			var input = document.getElementById("cwo");
			input.value='';
			$("#cwi").focus();
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
				btnLatchColor="btn-danger";
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
			$.post('/programs/isSupported.php',{radio: tMyRadio, getSet: 'set', text:'AF('}, function(response){
				if (response==1 && sliderAFGainOride>0){
						$("#AF").removeClass('d-none');
				}
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
	    function processCommand(which, btn)
	    {
			var tMe=tCall;
			var tWhat = which.replace(/'\*/g, tMe);
			which = tWhat.replace(/'X/g,$('#searchText').val());
	        var tPre=which.substring(0, 1);
			if (tPre=="F"){
				tPre=which.substr(3,1);
				which=which.substr(3);
			}
	        var tPost=which.substring(1);
			if (which.indexOf("+")>0){
				btnLatchColor=which.substr(which.indexOf("+")+1);
				which=which.substr(0,which.indexOf("+"));
			}else{
				btnLatchColor="btn-danger";
			}
			if (tPre=="F"){
				doFKey(tPost,btn);
				return false;
			}
			if (tPre=="<"){
				var lbtn=btn.substring(2,btn.indexOf("B"));
				var arlbtn=latchBtn[lbtn];
				if (arlbtn==null || arlbtn==""){
					arlbtn="?";
				}
				var lt1;
			//				alert(arlbtn);
				if (arlbtn !== "?"){
					tPost=latchBtn[lbtn];
					latchBtn[lbtn]="?";
					tPre=tPost.substr(0,1);
					tPost=tPost.substring(1);
					$(btn).removeClass(btnLatchColor);
					$(btn).addClass("btn-color");
					lt1=latchBtn.join(",");
				}else{
					tPre=tPost.substring(0,1);
					var tPost1=tPost.substring(tPost.indexOf(">")+1);
					tPost=tPost.substring(1,tPost.indexOf(">"));
					latchBtn[lbtn]=tPost1;
					$(btn).removeClass("btn-color");
					$(btn).addClass(btnLatchColor);
					lt1 = latchBtn.join(",");
				}
				var tLat="latchBtn"+mBank;
				$.post("/programs/SetSettings.php", {field: tLat, radio: tMyRadio, data: lt1, table: "RadioInterface"}, function(response){
					});
			}
			if (tPre=="/"){
				var tDX=$('#searchText').val().toUpperCase();
				tPost=tPost.replace('<dxcall>',tDX);
				if (tPost.indexOf("<band>")>0){
					tPost=tPost.replace('<band>',tBandMHz);
				}
				window.open(tPost, '_blank');
			}
			if (tPre=='$'){
	            sendCWMessage(tPost);
				$("#cwi").focus();
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
	        		$.post("/programs/SetMyRotorBearing.php", {w: "stop", i: tMyRotorRadio, a: ''});
	        }else if (which=="!BANK"){
	            mBank=parseInt(mBank)+1;
	        	if (mBank==5){mBank=1};
	        	$('#myBank').text("Macro Bank: "+mBank);
				loadMacroBank(mBank);
				$.post("/programs/SetSettings.php", {field: "MacroBank", radio: tMyRadio, data: mBank, table: "RadioInterface"});				
	        }else{
	//	        var tPre=which.substring(0, 1);
	//	        var tPost=which.substring(1);
		        switch(tPre){
		        case '$':	//send CW
					sendCWMessage(tPost);
					break;
				case '*':	//direct radio command using hamlib format
					$.post("/programs/SetSettings.php", {field: "CommandOut", radio: tMyRadio, data: "*"+tPost, table: "RadioInterface"});
					break;
	            case '#':	//direct system command
					$.post("/programs/systemExec.php", {command: tPost});
					break;
				case '!':	//special command
					if (tPost=='ESC'){
						setPTT(0);
						var tNum=String.fromCharCode(10);
						$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: tNum, table: "RadioInterface"});
					}else if (tPost=='TUNE'){
						var tNum=String.fromCharCode(11)+String.fromCharCode(1);
						$.post("./programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: tNum, table: "RadioInterface"});
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
							x.value=(response);
						});
					
						$.post("./programs/getRigCaps.php", {myRadio: tMyRadio, cap: "Mode list:"}, function(response){
								var tL="";
								var tList=response.split(" ");
								for (i=0;i<tList.length-1;i++)
								{
									tL=tL+"<div class='mymode' id=i<li><a class='dropdown-item' href='#'>"+tList[i]+"</a></li></div>";
								}
								var caps=response;
								x = document.getElementById("modeList"); 
								x.innerHTML=tL;
						});
						
						$.post("./programs/getRigBandwidths.php", {myRadio: tMyRadio, mode: tMode}, function(response){
							var tB="";
							var tList=response.split("\n");
							for (i=0;i<tList.length-1;i++)
							{
								tB=tB+"<div class='mypassband' id=i<li><a class='dropdown-item' href='#'>"+tList[i]+"</a></li></div>";
							};
							var caps=response;
							x = document.getElementById("passbandList"); 
							x.innerHTML=tB;
						
						});
					};

	            	if (tPost=="T/R" && tDisconnected==0){
	            		if (trXmit==true){
	            			trXmit=false;
	            			$.post("/programs/SetSettings.php", {field: "PTTOut", radio: tMyRadio, data: "0", table: "RadioInterface"});
							setPTT(0);
	            		}else{
	            			trXmit=true;
	            			$.post("/programs/SetSettings.php", {field: "PTTOut", radio: tMyRadio, data: "1", table: "RadioInterface"});
							setPTT(1);
	                	}
	            	}
					break;
		        }
	        }
			document.getElementById("cwi").focus();
	    }
				
		function sendCWMessage(what){
			$.post("/programs/ConcatSettings.php", {field: "CWIn", radio: tMyRadio, data: what, table: "RadioInterface"},function(response){
				var tQ=response;
			});
		}
	
		$.getScript("/js/addPeriods.js");
	
		var tUpdate = setInterval(bearingTimer,1000)

		function bearingTimer()
		{
			$.post("/programs/GetRotorIn.php", {rotor: tMyRotorRadio},function(response){
				var tAData=response.split('`');
				if (tAData[0]=="+"){
					tAData[0]="--";
				}
				var tAz=Math.round(tAData[0])+"&#176;";
				$(".angle").html(tAz);
			});	
		}
		
		var tFUpdate = setInterval(freqTimer, 1000);
		function freqTimer(){
			updateFooter();
			if (waitRefresh>0){
				waitRefresh=waitRefresh-1;
				return;
			}
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKSpeed'}, function(response) {
					var tS=response;
					if (tS!=sliderSpeedRef){
						sliderSpeedRef=tS;
						outputSpeed.innerHTML=tS+" WPM";
						$("#mySpeed").text(tS+' WPM');
						$('#sliderSpeed').slider('value',response);
					}
			});
			$.post('./programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKPot'}, function(response) {
				var tSpeed=response;
				if (tSpeed!=speedPot){
					speedPot=tSpeed;
					$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKMinWPM'}, function(response) {
						var tMin=response;
						tSpeed=parseInt(tSpeed)+parseInt(tMin);
						$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: tSpeed, table: "Keyer"});
							$("#mySpeed").text(tSpeed+' WPM');
							$("#cwi").focus();
					});
				}				
			});				
	        var now = new Date();
	        var now_hours=now.getUTCHours();
	        now_hours=("00" + now_hours).slice(-2);
	        var now_minutes=now.getUTCMinutes();
	        now_minutes=("00" + now_minutes).slice(-2);
	        $("#fPanel5").text(now_hours+":"+now_minutes+'z');
	        var tT=$("#fPanel1").text().trim();
			if (tT==="No Radio"){
				tDisconnected=1;
			}else{
				tDisconnected=0;
			}
	        $.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:tCall }, function(response) 
	        {
	            var tAData=response.split('`');
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
			});
		}
	
		var tUpdate = setInterval(updateTimer,100);
		function updateTimer(){
			if (clearingLeft=="0" && tKeyerMode!=1){
				var output = document.getElementById("cwo");
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'CWOut', table: "RadioInterface"}, function(response) {
					var tCW=response;
					if (tCW.length>0){
						tCW=tCW.replace(/\u001E/g, "")
						tCW=tCW.replace(new RegExp( String(RegExp.$1),"g"),"");
						if (tCW!=output.value){
							output.focus();
							output.value="";
							output.value=tCW;
						}
					}
					output.scrollTop=output.scrollHeight;
				});
			};				
	  	}
	
	    function getBandMemory(nBand){
	        var qBand=nBand+'L';
	        var tF='0';
			$.post('/programs/GetFrequencyMem.php',{band: qBand, radio: tMyRadio}, function(response) {
				var obj = JSON.parse(response);
				var tF=obj[0];
				var tM=obj[1];
				$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: tF, table: "RadioInterface"});
				$.post("/programs/SetSettings.php", {field: "MainOutCk", radio: tMyRadio, data: "1", table: "RadioInterface"});
				$.post("/programs/SetSettings.php", {field: "SubOut", radio: tMyRadio, data: tF, table: "RadioInterface"});
				$.post("/programs/SetSettings.php", {field: "SubOutCk", radio: tMyRadio, data: "1", table: "RadioInterface"});
				$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: tM, table: "RadioInterface"});
				$.post("/programs/SetSettings.php", {field: "ModeOutCk", radio: tMyRadio, data: "1", table: "RadioInterface"});
				$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: tF, table: "RadioInterface"});
				$.post("/programs/SetSettings.php", {field: "SubIn", radio: tMyRadio, data: tF, table: "RadioInterface"});
				$.post("/programs/SetSettings.php", {field: "ModeIn", radio: tMyRadio, data: tM, table: "RadioInterface"});
				$("#cwi").focus();
			});				
	    }
	
    </script>
</head>
<body class="body-black" id="keyer">
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
						<span class="btn-small-color" id="myBank" style="font-size:18px;">
							Macro Bank
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
				<?php require $dRoot . "/includes/keyerButtons.php"; ?>
			</div>
		</div>
		<hr>
		<?php require $dRoot . "/includes/macroButtons.php"; ?>
	</div>
    <?php require $dRoot . "/includes/footer.php"; ?>
</div>
    <?php require $dRoot . "/includes/modal.txt"; ?>
    <?php require $dRoot . "/includes/modalAlert.txt"; ?>
	<?php require $dRoot . "/includes/modalCancelOnly.txt"; ?>
	<script src="/Bootstrap/popper.min.js"></script>
    <script src="/Bootstrap/jquery-ui.js"></script>
	<script src="/js/jquery.ui.touch-punch.min.js"></script>   
	<script src="/Bootstrap/bootstrap.min.js"></script>
    <script src="/js/nav-active.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
	<script src="/js/summernote-case-converter.js"></script>
</html>
