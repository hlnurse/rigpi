<?php
session_start();
 $tUserName=$_SESSION['myUsername'];
 $tCall=$_SESSION['myCall'];
 $instance=$_SESSION['myInstance'];
$tRadio=1;
 $dRoot = "/var/www/html";
require_once $dRoot . "/classes/Membership.php";
$membership = new Membership();
$membership->confirm_Member($tUserName);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<title>RigPi Radio Settings (Advanced)</title>
	<meta charset="utf-8">

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo $tCall; ?> RigPi Radio Settings</title>
	<meta name="RigPi Settings" content="">
	<meta name="author" content="Howard Nurse, W6HN">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">
	<script defer src="./awe/js/all.js" ></script>
	<link href="/awe/css/all.css" rel="stylesheet">
	<link href="/awe/css/fontawesome.css" rel="stylesheet">
	<link href="/awe/css/solid.css" rel="stylesheet">	
	<script src="/Bootstrap/jquery.min.js" ></script>
	<link rel="shortcut icon" href="/favicon.ico">
	<?php require $dRoot . "/includes/styles.php"; ?>
	<script type="text/javascript">
		var cManu='';
		var radioID='', tID="";
		var tMyRadio='0';
		var tMyRadioReal='0';
		var tMyPort=4532;
		var tMyCall="<?php echo $tCall; ?>";
		var tCall=tMyCall;
		var tMyCWPort="/dev/ttyS0";  //later found from db
		var tMyRotorPort="/dev/ttyUSB1";  //later found from db
		var tMyKeyer="non";
		var tMyPTT=1;
		var tMyPTTCAT=1;
		var tMyPTTLatch=1;
		var tUserName=<?php echo "'" . $tUserName . "'"; ?>;
		var tUser='';
		var tMyRadioName='';
		var tMyRadioID=0;
		var el;
		var elt;
		var tMyKeyerFunction=0;
		var tMyKeyerPort;
		var tMyKeyerIP;
		var tCWPort;
		var tUDPPort=2333;
		var tDisconnected=0;
		var tShowVideo=0;
		var tMeterCal="1";
		var tMeterField="";
		var tPC;
		var supportsUSB_AF=0, MyTCPPort=30001;
		var k='1', nBypassAlert=0, tID;
		var transRadioID=1, transRadioName="";
		
		  $(document).ready(function(){
			  		
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

			el=$('#spinner');
			el.hide();
			elt=$('#spinnerTest');
			elt.hide();
			$.post('/programs/GetUserField.php', {un:tUserName,field:'WSJTXPort'}, function(response){
			  tUDPPort=response;
			});

			  $.post('/programs/GetUserField.php', {un:tUserName,field:'uID'}, function(response){
				  tMyRadio=response;
				  tMyTCPPort=30000 + parseInt(response);
				tMyRadioReal=response;
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'ID', table: 'MySettings'}, function(response)
				{
					tID=response;
				});
				$.get('/programs/GetMyRadio.php', 'f=Port&r='+response, function(response1) {
					var tRadioPort=response1;
					if (tRadioPort>4530 && tRadioPort<5000){
						tRadioReal=1+(tRadioPort-4532)/2;
					}
					tMyPort=tMyRadio*2+4530;
					$("#curID").val(response);
					getMyRadioFields();
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'Keyer', table: 'MySettings'}, function(response)
					{
						tMyKeyer=getKeyerID(response);
					});
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'PowerControl', table: 'MySettings'}, function(response)
					{
						$('#curPowerCtrl').val(response);
					});
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'Model', table: 'MySettings'}, function(response)
					{
						tMyRadioName=response;
					});
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
					{
						$('#searchText').val(response);
					});
		
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'ShowVideo', table: 'MySettings'}, function(response)
					{
						tShowVideo=response;
						if (tShowVideo==1){
							$('#curVideo').val('No Video');
						}else if (tShowVideo==2){
							$('#curVideo').val('Video -> S-meter');
						}else if (tShowVideo==3){
							$('#curVideo').val('Video -> Frequency Panel');
						}else{
							$('#curVideo').val('Video -> Tuning Knob');
						}
					});
		
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKFunction', table: 'Keyer'}, function(response)
					{
						tMyKeyerFunction=response;
					});
		
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKRemotePort', table: 'Keyer'}, function(response)
					{
						tMyKeyerPort=response;
					});
		
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKRemoteIP', table: 'Keyer'}, function(response)
					{
						tMyKeyerIP=response;
						tCWPort=response;

					});
	
					});
					
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'PowerControl', table: 'MySettings'}, function(response)
					{
						tPC=response;
					});
					
					$.getScript("/js/modalLoad.js");
				});
			});
			
			$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKFunction', table: 'Keyer'}, function(response)
			{
				tMyKeyerFunction=response;
			});

			$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKRemotePort', table: 'Keyer'}, function(response)
			{
				tMyKeyerPort=response;
			});

			$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'WKRemoteIP', table: 'Keyer'}, function(response)
			{
				tMyKeyerIP=response;
			});

			$(document).on('click', '.logSave', function() {
				setMyRadioFields(0);
			});

			$(document).on('click', '.manufacturers', function() {
				var text = $(this).text();
				  $('#curManu').val(text);
				  $.post('/programs/RadioDB.php', 'm='+text, function(response) {
					$('#radioList').empty(); //remove all child nodes
					var newOption = response;
					$('#radioList').append(newOption);
				  });
			});

			$(document).on('click', '.myid', function() {
				var text = $(this).text();
				  $('#curID').val(text);
				  tMyRadio=text;
				  getMyRadioFields();
				  $.post('/programs/SetSelectedRadio.php', {user: tUserName, radio: tMyRadio}, function(response){
				  });
			 });

			$(document).on('click', '.radios', function() {
				var text = $(this).text();
				  $('#curRadio').val(text);
				  tMyRadioName=text;
				  $('#curName').val(text);
				  if (text.toUpperCase()=="NET RIGCTL"){
					  var tPort=$('#curPort').val();
						if (tPort.indexOf(":")>0){
							var tP=tPort.split(":");
							var tIP=tP[0];
						}else{
							var tIP='0.0.0.0';
						}

				  $.post('/programs/getTransfer.php', {ip: tIP}, function(response) {
				  if (response.length>1){
					  tID=response.split("`");
					  transRadioName=tID[1].trim();
					  transRadioID=tID[0];
					  }
					  var tOK=setMyRadioFields(3);

				  })
				 }
			  });
			  
			  $(document).on('click', '.mypower', function() {
				var text = $(this).text();
				$("#curPowerCtrl").val(text);
				  var cPwr = text;
				  var cPwr1=cPwr;
				  tPowerField="PowerControl";
					if (cPwr=="Man"){
						cPwr="l RFPOWER_METER";
					}else if (cPwr=="Auto Power On"){
						cPwr="l RFPOWER";
					}else if (cPwr=="Auto Power Off"){
						cPwr="l SWR";
					}else if (cPwr=="Auto Power On and Off"){
						cPwr="l MICGAIN";
					}

				  $.post('/programs/GetSetting.php',{radio: tMyRadio, field: tPowerField, table: 'MySettings'}, function(response)
				  {
						$('#curPowerCtl').val(response);
					});
				});

			
			$(document).on('click', '#meterList', function() {
				var cXmit = $("#curXmit").val();
				var cXmit1=cXmit;
				  if (cXmit=="Output Power Meter"){
					  cXmit="l RFPOWER_METER";
					  tMeterField="PowerMeterCal";
				  }else if (cXmit=="Output Power Default"){
					  cXmit="l RFPOWER";
					  tMeterField="PowerDefaultCal";
				  }else if (cXmit=="SWR"){
					  cXmit="l SWR";
					  tMeterField="SWRCal";	  			
				  }else if (cXmit=="Mic Gain"){
					  cXmit="l MICGAIN";
					  tMeterField="MicGainCal";
				  }else if (cXmit=="ALC"){
					  cXmit="l ALC";
					  tMeterField="ALCCal";
				  }else if (cXmit=="Voltage"){
					  cXmit="l VD_METER";
					  tMeterField="VoltageCal";
				  }else if (cXmit=="Current"){
					  cXmit="l ID_METER";
					  tMeterField="CurrentCal";
				  }else if (cXmit=="Meter"){
					  cXmit="l METER";
					  tMeterField="MeterCal";
				  }
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: tMeterField, table: 'RadioInterface'}, function(response)
				{
					  $('#curMtr').val(response);
				  });
			  });
			
			$(document).on('click', '#logoutButton', function() 
			{
				openWindowWithPost("/login.php", {
					status: "loggedout",
					username: tUserName});
			});	
			
			$.post('/programs/isSupported.php',{radio: tMyRadio, getSet: 'set', text:'USB_AF'}, function(response){
				supportsUSB_AF=response;
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

			$(document).on('click', '.mybaud', function() {
				var text = $(this).text();
				  $("#curBaud").val(text);
			});

			$(document).on('click', '.myslavebaud', function() {
				var text = $(this).text();
				  $("#curSlaveBaud").val(text);
			});

			$(document).on('click', '.myvideo', function() {
				var text = $(this).text();
				  $("#curVideo").val(text);
				  tShowVideo=$(this).attr('id');
			});

			$(document).on('click', '.myslavecommand', function() {
				var text = $(this).text();
				  $("#curSlaveCommand").val(text);
			});

			$(document).on('click', '.myMeter', function() {
				var text = $(this).text();
				  $("#curXmit").val(text);
			});
			
			$(document).on('click', '.myLatch', function() {
				var text = $(this).text();
				  $("#curPTTLat").val(text);
				  if (text=="PTT Latches"){
					  tMyPTTLatch=1;
				  }else{
					  tMyPTTLatch=2;
				  };
			});
			
			$(document).on('click', '.myPTTCAT', function() {
				var text = $(this).text();
				  $("#curPTTCAT").val(text);
				if (text=="None"){
					tMyPTTCAT=0;
				}else if (text=="ON when transmitting"){
					tMyPTTCAT=1;
				}else {
					tMyPTTCAT=2;
				}
			});

			$(document).on('click', '.myPTTCmd', function() {
				var text = $(this).text();
				if (text==""){
					text = "default";
				}
				  $("#curPTTCmd").val(text);
			});

			$(document).on('click', '.mykeyer', function() {
				var text = $(this).text();
				  $("#curKeyer").val(text);
				  tMyKeyer=getKeyerID(text);
				  tMyCWPort=text;
//				if ($('#curKeyer').val()=="RigPi Keyer"){
//					$("#curKeyerPort").val("/dev/ttyS0");
//					tMyCWPort="/dev/ttyS0";
//				}
				if ($('#curKeyer').val()=="via CAT"||$('#curKeyer').val()=='None'){
					$("#curKeyerPort").val(text);
					tMyCWPort=text;
				}
			});

			$(document).on('click', '.myPTT', function() {
				var text = $(this).text();
				  $("#curPTT").val(text);
				if (text=="None"){
					tMyPTT=0;
				}else if (text=="ON when transmitting"){
					tMyPTT=1;
				}else if (text=="Hamlib GPIO"){
					tMyPTT=2;
				}else {
					tMyPTT=3;
				}
			});
			$(document).on('click', '.mystop', function() {
				var text = $(this).text();
				  $("#curStop").val(text);
			  });
			$(document).on('click', '.myDTR', function() {
				var text = $(this).text();
				  $("#curDTR").val(text);
			  });
			$(document).on('click', '.myRTS', function() {
				var text = $(this).text();
				  $("#curRTS").val(text);
			  });
			$(document).on('click', '.myCIV', function() {
				var text = $(this).text();
				if (text==""){
					text="default";
				}
				  $("#curCIV").val(text);
			  });
			$(document).on('click', '.myport', function() {
				var text = $(this).text();
				  $("#curPort").val(text);
				  tRadioPort=text;
			});
			$(document).on('click', '.myslaveport', function() {
				var text = $(this).text();
				  $("#curSlavePort").val(text);
			});
			$(document).on('click', '.myCWPort', function() {
				var text = $(this).text();
				  $("#curKeyerPort").val(text);
				  tMyCWPort=text;
			});
			$(document).on('click', '#modalAlertOK', function() {
			  $("#myModalCAlert").modal('hide');
			  $('#curName').val(transRadioName);
			  saveSettings(3);
			});
			$(document).on('click', '#modalAlertClose', function() {
			  $("#myModalCAlert").modal('hide');
			  saveSettings(3);
			});

			function connnectDialog(which){
				var caps="";
				window.setTimeout(  
				function() {  
					$.post('/programs/GetRadioCaps.php', {a: transRadioName, r: transRadioID, q: which}, function(response) {
						$("#modalA-body").html(transRadioName+ tText + ":<p>" + response.trim());			  				
						$(".modalA-title").html("Radio Capabilities");
						if (which=="serial"){
							$(".modalA-title").html("Serial Capabilities");	
						}
						$("#myModalAlert").modal('show');
					});
					},  
					1000
				);
			}

			$(document).on('click', '#capsButton', function() {
				updateTransfer('radio');
/*				var tOK=setMyRadioFields(3);
				var caps="";
				var tR="";
				window.setTimeout(
					function() {  
						  $.post('/programs/GetRadioCaps.php', {r: tMyRadio, q:'radio'}, function(response) {
							  caps=response;
							$("#modalA-body").html(response);			  				
							$(".modalA-title").html("Radio Capabilities");
							  $("#myModalAlert").modal('show');
						});
					},  
					1000
				);
*/			}); 
			function updateTransfer(which){
				var tText="";
				if (which == 'radio'){
					transRadioName=$('#curName').val();
					if (transRadioName.indexOf("Net rigctl")>-1){
						window.setTimeout(
							function() {  
									$("#modalA-body").html('Please enter the radio Name used at the station, not Net rigctl, to get rig caps.');	
									$(".modalA-title").html("Invalid Radio Name");
								  	$("#myModalAlert").modal('show');
							},  
							2000
						);
						return;
					}else{
						var caps="";
						var tR="";
						window.setTimeout(
							function() {  
								  $.post('/programs/GetRadioCaps.php', {a: transRadioName, r: transRadioID, q:'radio'}, function(response) {
									  caps=response;
									$("#modalA-body").html(response);			  				
									$(".modalA-title").html("Radio Capabilities");
									  $("#myModalAlert").modal('show');
								});
							},  
							1000
						);

/*						window.setTimeout(
						function() {  
								$("#modalA-body").html('Any changes have been saved.');			  				
								$(".modalA-title").html("Advanced Settings Saved");
								  $("#myModalAlert").modal('show');
						},  
						2000
						);
						return;
*/					}
					$.post('/programs/RadioID.php', {tRadio: transRadioName}, function(response) {
						if (response.length>1){
						  	transRadioID=response;
						  	}
					})
				}else if (which== "serial"){
						if (tMyRadioName=="Net rigctl"){
							transRadioName=$('#curName').val();
						}else{
							transRadioName=tMyRadioName;
						}
						$.post('/programs/RadioID.php', {tRadio: transRadioName}, function(response) {
							if (response.length>1){
								  transRadioID=response;
								  }

						if (transRadioName.indexOf("Net rigctl")>-1){
							window.setTimeout(
								function() {  
										$("#modalA-body").html('Please enter the radio Name used at the station, not Net rigctl, to get serial info.');			  				
										$(".modalA-title").html("Invalid Radio Name");
										  $("#myModalAlert").modal('show');
								},  
								2000
							);
							return;

						}else{
							$.post('/programs/GetRadioCaps.php', {a: transRadioName, r: transRadioID, q:'serial'}, function(response) {
									  caps=response;
									$("#modalA-body").html("Serial capabilities for " + transRadioName + ":<p>" + response);			  				
									$(".modalA-title").html("Radio Serial Capabilities");
									  $("#myModalAlert").modal('show');
								});
						}
						})

					}else{
					  tText=" connected locally."
					  tID[0]=tMyRadioID;
					  transRadioID=tID[0];
					  transRadioName=tMyRadioName;
				}

			}
			
			$(document).on('click', '#serButton', function() {
				updateTransfer('serial');
			}); 
			
			function setMyKeyerConf()
			{
				var pinVal=0;
				var tMyKeyer1='';
				if (tMyKeyer=='rpk1'){
					tMyKeyer1='rpk';
//					if (sidetoneOnVal){
						pinVal=2;
//					}
//					if (pttOnVal==1){
						pinVal=pinVal|1;
//					}
					pinVal=pinVal|8; //keyout1 enable even thiough schematic and docs say this is for keyout2.
				}else if(tMyKeyer=='rpk2'){
					tMyKeyer1='rpk';
//					if (sidetoneOnVal){
						pinVal=2;
//					}
//					if (pttOnVal==1){
						pinVal=pinVal|1;
//					}
					pinVal=pinVal|4; //keyout2 enable 	
				}
				$.post("/programs/SetSettings.php", {field: "WKPinConf", radio: tMyRadio, data: pinVal, table: "Keyer"});
			}
			
			function disconnectRadio(){
				tSplitOn=0;
				var el=$('#spinner');
				el.hide();
				el=$('#spinnerTest');
				el.hide();
					tDisconnected=1;
				   $.post("/programs/SetSettings.php", {field: "SplitOut", radio: tMyRadio, data: tSplitOn, table: "RadioInterface"});
				  $.post('./programs/disconnectRadio.php', {id: tID, radio: tMyRadio, port: tRadioPort,  user: tUserName, rotor: '', instance:<?php echo $instance;?>}, function (response) {
					  if (tPC.indexOf('Off')>0){
						  $("#modalA-body").html('Radio is disconnected and power is off.');			  				
					  }else{
						  $("#modalA-body").html('Radio is disconnected.');			  				
					  }
						$(".modalA-title").html("Radio Connection");
						  $("#myModalAlert").modal('show');
						  setTimeout(function(){ 
							  $("#myModalAlert").modal('hide');
						 },
						  2000);
						   $.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: "OFF", table: "RadioInterface"});
						   $.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadioReal, data: "OFF", table: "RadioInterface"});
						  if (tMyPTT==3){
							  $.post('./programs/doGPIOPTT.php', {PTTControl: "off"}); 			  				
						  }
					});
					waitRefresh=4;
				}
//			};


			$(document).on('click', '#connectButton', function() {
//				if (tDisconnected==0){
					disconnectRadio();
//				};
			waitRefresh=10;
			tDisconnected=0;
				
//				disconnectRadio();
//	            var tDisconnected=0;
				var el=$('#spinner');
				el.show();
				el.addClass('fa-spin');
				var tOK=setMyRadioFields(1);
				$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: "", table: "RadioInterface"});
			});

			$(document).on('click', '#testButton', function() {
				if (tDisconnected==0){
					disconnectRadio();
				};
				waitRefresh=10;
				tDisconnected=0;
				
				elt=$('#spinnerTest');
				elt.addClass('fa-spin');
				elt.show();
				tDisconnected=0;
				var tOK=setMyRadioFields(3);
				$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: "", table: "RadioInterface"});
				setTimeout(function(){
				$.post('./programs/h.php',{test: 1, keyer: tMyKeyer, radio: tMyRadio, user: tUserName, radioPort:tRadioPort, port: tCWPort, tcpPort: tMyTCPPort, rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction, UDPPort: tUDPPort, startUpDelay: 0, instance:<?php echo $instance;?>},function(response){
					$.post('./programs/hamlibDo.php', {test: 1, keyer: tMyKeyer, radio: tMyRadio, user: tUserName, radioPort:tRadioPort, CWPort: tCWPort, tcpPort: tMyTCPPort, rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction, UDPPort:tUDPPort, supportsUSB: supportsUSB_AF}, function(response) {
						  if (tDisconnected==1){
							  /////////////////////
							  $.post('/programs/disconnectRadio.php', {id: tID, radio: tMyRadio, port: tRadioPort, user: tUserName, rotor: tMyRadio}, function(response1) {
								$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: "OFF", table: "RadioInterface"});
								  if (tMyPTT==3){
									  $.post('./programs/doGPIOPTT.php', {PTTControl: "off"}); 			  				
								  }
								  return;
							  });
							  elt.hide();
						  }else{
							  if (response.length>30){
								  response="<br>&nbsp;&nbsp" + response + "<br><br>"
								$("#modalZ-body").html(response);			  				
								$(".modalZ-title").html("<p>&nbsp;&nbsp;RigPi Report");
								  $("#myModalCopy").modal('show');//			  				alert(response);
								  if (response.indexOf("Now starting RigPi Radio")>0){
									  $('#connect').text("Radio connected");
								  }
								  if (tMyPTT==3){
									  $.post('/programs/doGPIOPTT.php', {PTTControl: "on"}); 			  				
								  }
							  }
							  elt.removeClass('fa-spin');
							  elt.hide();
						  }
					});
					});
				},2000);
			});

			$(document).on('click', '.copyModal', function() {
				var text = document.getElementById('modalZ-body');
				var selection = window.getSelection();
				var range = document.createRange();
				range.selectNodeContents(text);
				selection.removeAllRanges();
				selection.addRange(range);
				//add to clipboard.
				document.execCommand('copy');
				$("#myModalCopy").modal('hide');
			});

			$(document).on('click', '#disconnectButton', function() {
				disconnectRadio();
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
		  $("#myModalCancelOnly").modal('show');
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
			  function setRadioList(){
				$.post('./programs/RadioDB.php', 'm='+cManu, function(response) {
					$('#radioList').empty(); //remove all child nodes
					var newOption = response;
					$('#radioList').append(newOption);
				});
			  }

			  function getMyRadioFields(){
				$.get('/programs/GetMyRadio.php', 'f=Manufacturer&r='+tMyRadio, function(response) {
					  $('#curManu').val(response);
					  cManu=response;
					  setRadioList();
				  });

				$.get('/programs/GetMyRadio.php', 'f=Model&r='+tMyRadio, function(response) {
					  $('#curRadio').val(response);
				  });

				$.get('/programs/GetMyRadio.php', 'f=ID&r='+tMyRadio, function(response) {
					  $('#curID').val(tMyRadio);
					  tMyRadioID=response;
				  });
				  
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'TransmitLevel', table: 'MySettings'}, function(response)
				{
					getCal(response);
				  });
				  
				  function getCal(response){
					if(response=="l RFPOWER_METER")
					{
						response='PowerMeterCal';
					}else if (response=="l SWR")
					{
						response='SWRCal';
					}else if (response=="l RFPOWER")
					{
						response='PowerDefaultCal';
					}else if (response=="l MICGAIN")
					{
						response="MicGainCal";
					}else if (response=="l ALC")
					{
						response="ALCCal";
					}else if (response=="l VD_METER")
					{
						response="VoltageCal";
					}else if (response=="l ID_METER")
					{
						response="CurrentCal";
					}else if (response=="l METER")
					{
						response="MeterCal";
					}
					
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: response, table: 'RadioInterface'}, function(response)
					{
						  $('#curMtr').val(response);
					  });
				  }

				$.get('/programs/GetMyRadio.php', 'f=Port&r='+tMyRadio, function(response) {
					  $('#curPort').val(response);
					  tRadioPort=response;
//					var ipformat = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
					ipOK=false;
					if (response.indexOf(":")){
						var resp=response.split(":");
  if (/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(resp[0]) || resp[0].indexOf(".")>0) {  
							ipOK=true;
							}
//					if (typeof(response)=== 'string'){
//						ipOK=true;
//					}else{
//						ipOK=response.value.match(ipformat);
//					}
					  $.post('programs/portScan.php', function(response1){
						  if ($('#curPort').val().indexOf('45')==-1){
						  	if (response1.indexOf(response)==-1 && 
							  	response.length>3 && !ipOK)
								{
							  	$('#curPort').val("None");
								$("#modalA-body").html("Radio port has been set to 'None.' The specified port was not found.");			  				
								$(".modalA-title").html("Radio Port Error");
							  	$("#myModalAlert").modal('show');
						  	}
						  };
					  });
				  }});

				$.get('/programs/GetMyRadio.php', 'f=SlavePort&r='+tMyRadio, function(response) {
					  $('#curSlavePort').val(response);
					  $.post('programs/slavePortScan.php', function(response1){
					  });
				  });

				$.get('/programs/GetMyRadio.php', 'f=Keyer&r='+tMyRadio, function(response) {
					  $('#curKeyer').val(response);
					  tMyKeyer=getKeyerID(response);
				  });	  			

				$.get('/programs/GetMyRadio.php', 'f=PTTMode&r='+tMyRadio, function(response) {
					  tMyPTT=response;
					  var text="";
					  if (tMyPTT=="0"){
						  text="None";
					  }else if (tMyPTT=="1"){
						  text="ON when transmitting";
					  }else if (tMyPTT=="2"){
						  text="Hamlib GPIO";
					  }else {
						  text="ON when radio connected";
					  }
					  $('#curPTT').val(text);
				  });	  			

				$.get('/programs/GetMyRadio.php', 'f=KeyerPort&r='+tMyRadio, function(response) {
//					if ($('#curKeyer').val()=="RigPi Keyer"){
//						  $("#curKeyerPort").val("/dev/ttyS0");
//						  tMyCWPort="/dev/ttyS0";
//					  }else{
						  $('#curKeyerPort').val(response);
						  tMyCWPort=response;
//					  }
				  });

				$.get('/programs/GetMyRadio.php', 'f=Baud&r='+tMyRadio, function(response) {
					if (response==""){
						response='default';
					}
					  $('#curBaud').val(response);
				  });

				$.get('/programs/GetMyRadio.php', 'f=SlaveBaud&r='+tMyRadio, function(response) {
					if (response==""){
						response='default';
					}
					  $('#curSlaveBaud').val(response);
				  });

				$.get('/programs/GetMyRadio.php', 'f=SlaveCommand&r='+tMyRadio, function(response) {
					if (response==""){
						response='None';
					}

					  $('#curSlaveCommand').val(response);
				  });

				$.get('/programs/GetMyRadio.php', 'f=Stop&r='+tMyRadio, function(response) {
					if (response==""){
						response='default';
					}
					  $('#curStop').val(response);
				  });

				$.get('/programs/GetMyRadio.php', 'f=CIV_Code&r='+tMyRadio, function(response) {
					if (response==""){
						response="default"
					}
					  $('#curCIV').val(response);
				  });

				$.get('/programs/GetMyRadio.php', 'f=RadioName&r='+tMyRadio, function(response) {
					  $('#curName').val(response);
					  tMyRadioName=response;
				  });

				$.post('/programs/GetInterface.php', {field:'powerOut',radio:tMyRadio}, function(response) {
						  $('#curPwr').val(response);
					  });
					
				$.get('/programs/GetMyRadio.php', 'f=DTR&r='+tMyRadio, function(response) {
					$('#curDTR').val(response);
				  });

				$.get('/programs/GetMyRadio.php', 'f=RTS&r='+tMyRadio, function(response) {
					$('#curRTS').val(response);
				  });

				$.get('/programs/GetMyRadio.php', 'f=DisableSplitPolling&r='+tMyRadio, function(response) {
					if (response==0){
						document.getElementById("splitCheck").checked=false;
					}else{
						document.getElementById("splitCheck").checked=true;
					}
				  });
				$.get('/programs/GetMyRadio.php', 'f=RotorPort&r='+tMyRadio, function(response) {
					  tMyRotorPort=response;
				  });
				$.get('/programs/GetMyRadio.php', 'f=TransmitLevel&r='+tMyRadio, function(response) {
					if(response=="l RFPOWER_METER")
					{
						response='Output Power Meter';
					}else if (response=="l SWR")
					{
						response='SWR';
					}else if (response=="l RFPOWER")
					{
						response='Output Power Default';
					}else if (response=="l MICGAIN")
					{
						response="Mic Gain";
					}else if (response=="l ALC")
					{
						response="ALC";
					}else if (response=="l VD_METER")
					{
						response="Voltage";
					}else if (response=="l ID_METER")
					{
						response="Current";
					}else if (response=="l METER")
					{
						response="Meter";
					}
					  $('#curXmit').val(response);
				  });

				$.get('/programs/GetMyRadio.php', 'f=PTTCmd&r='+tMyRadio, function(response) {
					if (response==""){
						response="default";
					}
					  $('#curPTTCmd').val(response);
				  });
				$.get('/programs/GetMyRadio.php', 'f=PTTDelay&r='+tMyRadio, function(response) {
					  $('#pttDelay').val(response);
				  });
				$.get('/programs/GetMyRadio.php', 'f=PTTCAT&r='+tMyRadio, function(response) {
					  tMyPTTCAT=response;
					  var text="";
					  if (tMyPTTCAT=="0"){
						  text="None";
					  }else if (tMyPTTCAT=="1"){
						  text="ON when transmitting";
					  }else {
						  text="ON when radio connected";
					  }
					  $('#curPTTCAT').val(text);
				  });
				$.get('/programs/GetMyRadio.php', 'f=PTTLatch&r='+tMyRadio, function(response) {
					  tMyPTTLatch=response;
					  var text="";
					  if (tMyPTTLatch=="1"){
						  text="PTT Latches";
					  }else if (tMyPTTLatch=="2"){
						  text="PTT Momentary";
					  }
					  $('#curPTTLat').val(text);
				  });
			  }

			   function getKeyerID(which)
			   {

				if(which=="None")
				{
					which='non';
				}else if (which=="RigPi Keyer")
					{
						which='rpk1';
				}else if (which=="via CAT")
				{
					which="cat";
				}else if (which=="WinKeyer")
				{
					which="wkr";
				}else if (which=="External CTS")
				{
					which="ext";
				}
				return which;
			   }
				function saveSettings(nBypassAlert){
				  var manuID=$('#curManu').val();
				  tRadioPort=$('#curPort').val();
				  if (radioID == "1" && tRadioPort.indexOf("45")==-1){//} && radioID !== "2" && tRadioPort.indexOf("45")>-1){
					  $tRadioPort=(tMyRadio * 2) + 4530;
					  $('#curPort').val($tRadioPort);
				  };
//					$("#modalA-body").html("To connect to another radio, Hamlib/NET rigctl must be used for Manuf/Model.");			  				
//				  $(".modalA-title").html("Radio Port Problem");
//					$("#myModalAlert").modal('show');
//					el.removeClass('fa-spin');
///					el.hide();
//					return;
//				  }
				  if (radioID=="2" && tRadioPort.indexOf("45")==-1){
					$("#modalA-body").html("For Hamlib NET rigctl, port must include a port number: 4532, or 4534, or ... (see Help).");			  				
					$(".modalA-title").html("Radio Port Problem");
					  $("#myModalAlert").modal('show');
					  el.removeClass('fa-spin');
					  el.hide();
				  }else{
					  var rtsVal=$("#curRTS").val();
					  var dtrVal=$("#curDTR").val();
					  var cMtr=$("#curMtr").val();
					  var cXmit=$("#curXmit").val();
					  if (cXmit=="Output Power Meter"){
						  cXmit="l RFPOWER_METER";
						  tMeterField="PowerMeterCal";
					  }else if (cXmit=="Output Power Default"){
						  cXmit="l RFPOWER";
						  tMeterField="PowerDefaultCal";
					  }else if (cXmit=="SWR"){
						  cXmit="l SWR";
						  tMeterField="SWRCal";	  			
					  }else if (cXmit=="Mic Gain"){
						  cXmit="l MICGAIN";
						  tMeterField="MicGainCal";
					  }else if (cXmit=="ALC"){
						  cXmit="l ALC";
						  tMeterField="ALCCal";
					  }else if (cXmit=="Voltage"){
						  cXmit="l VD_METER";
						  tMeterField="VoltageCal";
					  }else if (cXmit=="Current"){
						  cXmit="l ID_METER";
						  tMeterField="CurrentCal";
					  }else if (cXmit=="Meter"){
						  cXmit="l METER";
						  tMeterField="MeterCal";
					  }
					var noPoll=document.getElementById("splitCheck").checked;
					  if (noPoll==true){
						  noPoll=1;
					  }else{
						  noPoll=0;
					  }
					  setMyKeyerConf();
					  var t = $("#curKeyerPort").val();
					  if ($("#curPTTCmd").val()==""){
							$("#curPTTCmd").val("default");
						} 
					  if ($("#curCIV").val()==""){
							  $("#curCIV").val("default");
						  } 
					  $.post("/programs/SetMyRadio.php", {
						m: $("#curManu").val(), 
						o: $("#curRadio").val(), 
						u: $("#curBaud").val(), 
						b: "8", 
						p: tRadioPort, 
						a: "0", 
						s: $("#curStop").val(), 
						c: $("#curCIV").val(), 
						n: $("#curName").val(), 
						i: tMyRadio, 
						k: $("#curKeyer").val(), 
						kp: $("#curKeyerPort").val(),
						sp: noPoll, 
						xd: cXmit, 
						pt: tMyPTT, 
						dl:$("#pttDelay").val(),
						r: rtsVal, 
						t: dtrVal, 
						d: radioID, 
						pM:$("#curPTTCmd").val(), 
						pA:tMyPTTCAT,
						slp:$("#curSlavePort").val(), 
						slb:$("#curSlaveBaud").val(), 
						slc:$("#curSlaveCommand").val(), 
						sv:tShowVideo, 
						pl:tMyPTTLatch },
						  function(response){
						  if (nBypassAlert==3){ //ignore
							  el.hide();
						  } else if (nBypassAlert==0){
							$("#modalA-body").html(response+"<p><p>Disconnect Radio and Reconnect from the Tuner window if you have made changes.");			  				
							$(".modalA-title").html("Settings Saved");
							  $("#myModalAlert").modal('show');
						}else{  //connect to radio
							$.post('./programs/h.php',{test: 0, keyer: tMyKeyer, radio: tMyRadio, user: tUserName, radioPort:tRadioPort, port: tCWPort, tcpPort: tMyTCPPort, rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction, UDPPort: tUDPPort, startUpDelay: 3, instance:<?php echo $instance;?>},function(response){
								$.post('./programs/hamlibDo.php', {test: 0, keyer: tMyKeyer, radio: tMyRadio, user: tUserName, radioPort:tRadioPort, port: tCWPort, tcpPort: tMyTCPPort, rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction, UDPPort:tUDPPort, supportsUSB: supportsUSB_AF}, function(response) {
								if (tDisconnected==1){
									/////////////////////////////
									$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: "OFF", table: "RadioInterface"});
									  if (tMyPTT==3){
										  $.post('./programs/doGPIOPTT.php', {PTTControl: "off"}); 			  				
									  }
									return;
								}else{
									  if (response.length>6){
										$("#modalA-body").html("Radio connected: <br><h2c> " + addPeriods(response) + " MHz");
										$(".modalA-title").html("Radio Connection");
										  $("#myModalAlert").modal('show');
//											  if (response.indexOf("Now starting RigPi Radio")>0){
											  $('#connect').text("Radio connected");
//											  }
										  if (tMyPTT==3){
											  $.post('/programs/doGPIOPTT.php', {PTTControl: "on"}); 			  				
										  }
									  }
								  }
								  el.removeClass('fa-spin');
								  el.hide();
							  });
							});
						};
						  tMyKeyer=$("#curKeyer").val()
						  cMtr=$("#curMtr").val()
						  tMyKeyer=getKeyerID(tMyKeyer);
						  tMyCWPort=$("#curKeyerPort").val();
						  $.post("/programs/SetSettings.php", {field:"PowerControl", data:$("#curPowerCtrl").val(), radio:tMyRadio, 
							table:"MySettings"}, function(response){});
						  $.post("/programs/SetSettings.php", {field:"powerOut", data:$("#curPwr").val(), radio:tMyRadio, 
							  table:"RadioInterface"}, function(response){
							  $.post("/programs/SetSettings.php", {field:tMeterField, data:cMtr, radio:tMyRadio, 
								  table:"RadioInterface"}, function(response){ });
						});
					  });
				  };
			  }
			function setMyRadioFields(nBypassAlert) {
				//nBypassAlert==0, give "settings saved" message, no connect
				//nBypassAlert==1, normal connect
				//nBypassAlert==3, save settings, no connect, no message

			   tDisconnected=0;
				$.post('/programs/RadioID.php', 'tRadio=' + $('#curRadio').val(), function(response) {
				  radioID=response;
				  var tPort=$('#curPort').val();
				  if (tPort.indexOf(":")>0){
					  var tP=tPort.split(":");
					  var tIP=tP[0];
				  }else{
					  var tIP='0.0.0.0';
				  }
				  if (radioID==2){
					  if (tID.length>1){
						transRadioName=tID[1].trim();
						transRadioID=tID[0];
						if ($('#curName').val() !== transRadioName){
							$("#modalC-body").html("Do you want to use the Station radio name, "+transRadioName+", for this connection?");			  				
							$("#modalC-title").html("Replace Radio Name?");
							$("#myModalCAlert").modal('show');
						  }else{
							  $.post('/programs/setTransfer.php', {id: radioID, rn: tMyRadioName }, function(response) {
								  $r=response;
								  saveSettings(0);
							  })
						  };
					  }else{
						saveSettings(nBypassAlert);  
					  };
				  }else{
					saveSettings(nBypassAlert);  					  
				  };
			  });
		  };
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
			  
			  function updateFooter() {
				  $.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:tMyCall }, function(response) 
				  {
					  var tAData=response.split('`');
		  
					var tBW=tAData[17];
					 
					if (tAData[8] !== "NG") {
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
				   });
		  };

		var tUpdate = setInterval(updateTimer,1000);
		function updateTimer()
		{
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
	</script>
	<?php require $dRoot . "/programs/ManufacturersDB.php"; ?>
</head>

<body class="body-black-scroll">
	<?php require $dRoot . "/includes/header.php"; ?>
	<div class="container-fluid">
		<div class="row" style="margin-bottom:10px;">
			<div class="col-12  col-md-4 btn-padding">
			</div>
			<div class="col-6 col-sm-5 text-center">
				<span class="label label-success text-white" style="cursor: default; margin-top:10px;">Advanced Radio Settings (User: <?php echo $tUserName; ?>)</span>
			</div>
			<div class="col-6 col-sm-3 btn-padding">
				<button class='btn btn-color logSave' type='button'>
					<i class="fas fa-cloud-upload-alt fa-lg" id="logSave"></i>
				</button>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Manuf</span>
					</div>
					<input type="text" class="form-control disable-text" readonly id="curManu" title="Selected Radio Manufacturer" aria-lable="manufacturer" aria-describedby="manufacturer-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="manuSel" data-size="3" type="button" title="Manufacturer List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right menu-scroll" id="manufacturerList">
								<?php echo getRadioManufacturers(); ?>
							 </ul>
						</div>
					</span>
				</div>
			</div>
			<div class="col-md-4 text-spacer ">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Radio</span>
					</div>
					<input type="text" class="form-control disable-text" readonly="readonly"  id="curRadio"  title="Selected Radio" aria-lable="radio" aria-describedby="radio-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="radioSel" data-size="3" type="button"  title="Radio List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right menu-scroll" id="radioList">
							 </ul>
						</div>
					</span>
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Name</span>
					</div>
					<input type="text" class="form-control"  title="Radio Name" id="curName" aria-lable="name" aria-describedby="name-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">R Port</span>
					</div>
					<input type="text" class="form-control" id="curPort"  title="Radio Port" aria-lable="manufacturer" aria-describedby="manufacturer-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="portSel" data-size="3" type="button" title="Port List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right menu-scroll" id="portList">
								<?php require $dRoot . "/programs/portScan.php"; ?>
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
						<span class="input-group-text radio-group-addon">Baud</span>
					</div>
					<input type="text" class="form-control disable-text" readonly id="curBaud"  title="Baud Rate" aria-lable="baud" aria-describedby="baud-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="radioSel" data-size="3" type="button"  title="Comm Speed List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<div class="dropdown-menu dropdown-menu-right menu-scroll" id='baudList' aria-labelledby="baudSelectButton">
								<div class='mybaud' id='0000'><li><a class='dropdown-item' href='#'>default</a></li></div>
								<div class='mybaud' id='1200'><li><a class='dropdown-item' href='#'>1200</a></li></div>
								<div class='mybaud' id='2400'><li><a class='dropdown-item' href='#'>2400</a></li></div>
								<div class='mybaud' id='4800'><li><a class='dropdown-item' href='#'>4800</a></li></div>
								<div class='mybaud' id='9600'><li><a class='dropdown-item' href='#'>9600</a></li></div>
								<div class='mybaud' id='19200'><li><a class='dropdown-item' href='#'>19200</a></li></div>
								<div class='mybaud' id='38400'><li><a class='dropdown-item' href='#'>38400</a></li></div>
								<div class='mybaud' id='57600'><li><a class='dropdown-item' href='#'>57600</a></li></div>
								<div class='mybaud' id='15200'><li><a class='dropdown-item' href='#'>115200</a></li></div>
							</div>
						</div>
					</span>
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Stop</span>
					</div>
					<input type="text" class="form-control disable-text"  readonly title="Number of Stop Bits" id="curStop" aria-lable="stop" aria-describedby="stop-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="curStop" data-size="3" type="button"  title="Stop Bits List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<div class="dropdown-menu dropdown-menu-right" id='stopList' aria-labelledby="stopSelectButton">
								<div class='mystop' id='0'><li><a class='dropdown-item' href='#'>default</a></li></div>
								<div class='mystop' id='1'><li><a class='dropdown-item' href='#'>1</a></li></div>
								<div class='mystop' id='2'><li><a class='dropdown-item' href='#'>2</a></li></div>
							</div>
						</div>
					</span>
				</div>
			</div>
			 <div class="col-md-4 text-center text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">CI-V</span>
					</div>
					<input type="text" class="form-control"  title="Icom Radios Only" id="curCIV" aria-lable="civ" value="default" aria-describedby="civ-addon">
				</div>
			 </div>
		</div>	
		<div class="row">
			 <div class="col-md-4 text-center text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">R DTR</span>
					</div>
					<input type="text" class="form-control disable-text"  readonly title="DTR" id="curDTR" aria-lable="dtr" aria-describedby="stop-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="curDTR" data-size="3" type="button"  title="DTR List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<div class="dropdown-menu dropdown-menu-right" id='dtrList' aria-labelledby="stopSelectButton">
								<div class='myDTR' id='0'><li><a class='dropdown-item' href='#'>default</a></li></div>
								<div class='myDTR' id='1'><li><a class='dropdown-item' href='#'>high</a></li></div>
								<div class='myDTR' id='2'><li><a class='dropdown-item' href='#'>low</a></li></div>
							</div>
						</div>
					</span>
				</div>
			 </div>
			 <div class="col-md-4 text-center text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">R RTS</span>
					</div>
					<input type="text" class="form-control disable-text"  readonly title="RTS" id="curRTS" aria-lable="rts" aria-describedby="stop-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="curRTS" data-size="3" type="button"  title="RTS List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<div class="dropdown-menu dropdown-menu-right" id='rtsList' aria-labelledby="stopSelectButton">
								<div class='myRTS' id='0'><li><a class='dropdown-item' href='#'>default</a></li></div>
								<div class='myRTS' id='1'><li><a class='dropdown-item' href='#'>high</a></li></div>
								<div class='myRTS' id='2'><li><a class='dropdown-item' href='#'>low</a></li></div>
							</div>
						</div>
					</span>
				</div>
			 </div>
			 <div class="col-md-4 text-center text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">PTT Cmd</span>
					</div>
					<input type="text" class="form-control" id="curPTTCmd"  title="PTT Command" placeholder="default" aria-lable="CATPTTCmd" aria-describedby="CATPTTCmd-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="catPTTCmd" data-size="3" type="button" title="PTT Command" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right menu-scroll" id="cmdPTTList">
								<div class='myPTTCmd' id='def'><li><a class='dropdown-item' href='#'>default</a></li></div>
								<div class='myPTTCmd' id='tx1'><li><a class='dropdown-item' href='#'>w TX1;</a></li></div>
								<div class='myPTTCmd' id='cust'><li><a class='dropdown-item' href='#'>Custom</a></li></div>
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
						<span class="input-group-text radio-group-addon">H/W PTT</span>
					</div>
					<input type="text" class="form-control  disable-text" readonly id="curPTT"  title="Hardware PTT (RigPi Keyer must be assigned to this account to enable hardware PTT)" placeholder="Hardware PTT" aria-lable="PTT" aria-describedby="PTT-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="pttSel" data-size="3" type="button" title="PTT List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right menu-scroll" id="pttList">
								<div class='myPTT' id='none'><li><a class='dropdown-item' href='#'>None</a></li></div>
								<div class='myPTT' id='xmit'><li><a class='dropdown-item' href='#'>ON when transmitting</a></li></div>
								<div class='myPTT' id='gpio'><li><a class='dropdown-item' href='#'>Hamlib GPIO</a></li></div>
								<div class='myPTT' id='conn'><li><a class='dropdown-item' href='#'>ON when radio connected</a></li></div>
							 </ul>
						</div>
					</span>
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">CAT PTT</span>
					</div>
					<input type="text" class="form-control disable-text" readonly id="curPTTCAT"  title="CAT PTT" placeholder="CAT PTT" aria-lable="CATPTT" aria-describedby="CATPTT-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="catPTTSel" data-size="3" type="button" title="CAT PTT Options" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right menu-scroll" id="pttCATList">
								<div class='myPTTCAT' id='none'><li><a class='dropdown-item' href='#'>None</a></li></div>
								<div class='myPTTCAT' id='xmitOn'><li><a class='dropdown-item' href='#'>ON when transmitting</a></li></div>
								<div class='myPTTCAT' id='connRad'><li><a class='dropdown-item' href='#'>ON when radio connected</a></li></div>
							 </ul>
						</div>
					</span>
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">PTT Delay</span>
					</div>
					<input type="text" class="form-control"  title="PTT Delay (milliseconds)" id="pttDelay" aria-lable="dly" placeholder="delay in milliseconds (0 or > 10)" aria-describedby="dly-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">PTT Ltch</span>
					</div>
					<input type="text" class="form-control disable-text"  readonly title="Transmit PTT" id="curPTTLat" aria-lable="ptt" aria-describedby="ptt-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="latSel" data-size="3" type="button" title="Latch List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right menu-scroll" id="latList">
								<div class='myLatch' id='pttLat'><li><a class='dropdown-item' href='#'>PTT Latches</a></li></div>
								<div class='myLatch' id='pttMom'><li><a class='dropdown-item' href='#'>PTT Momentary</a></li></div>
							 </ul>
						</div>
					</span>
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Tr Mtr</span>
					</div>
					<input type="text" class="form-control disable-text"  readonly title="Transmitter parameter read" id="curXmit" aria-lable="xmit" aria-describedby="xmit-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="xmitSel" data-size="3" type="button" title="Meter List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right menu-scroll" id="meterList">
								<div class='myMeter' id='mtrPwr'><li><a class='dropdown-item' href='#'>Output Power Meter</a></li></div>
								<div class='myMeter' id='setPwr'><li><a class='dropdown-item' href='#'>Output Power Default</a></li></div>
								<div class='myMeter' id='swr'><li><a class='dropdown-item' href='#'>SWR</a></li></div>
								<div class='myMeter' id='alc'><li><a class='dropdown-item' href='#'>ALC</a></li></div>
								<div class='myMeter' id='alc'><li><a class='dropdown-item' href='#'>Voltage</a></li></div>
								<div class='myMeter' id='alc'><li><a class='dropdown-item' href='#'>Current</a></li></div>
								<div class='myMeter' id='mic'><li><a class='dropdown-item' href='#'>Mic Gain</a></li></div>
								<div class='myMeter' id='meter'><li><a class='dropdown-item' href='#'>Meter</a></li></div>
							 </ul>
						</div>
					</span>
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Mtr Cal</span>
					</div>
					<input type="text" class="form-control"  title="Transmit Meter Calibration" id="curMtr" aria-lable="mtr" aria-describedby="mtr-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text radio-group-addon">Pwr Ctrl</span>
				</div>
				<input type="text" class="form-control disable-text" readonly id="curPowerCtrl"  title="PowerCtrl" aria-lable="power" aria-describedby="power-addon">
				<span class="input-group-btn">
					<div class="dropdown">
						<button class="btn btn-primary dropdown-toggle" id="powerSel" data-size="3" type="button" title="Power List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
						</button>
						<ul class="dropdown-menu dropdown-menu-right menu-scroll" id="powerList">
							<div class='mypower' id='man'><li><a class='dropdown-item' href='#'>Manual</a></li></div>
							<div class='mypower' id='on'><li><a class='dropdown-item' href='#'>Auto Power On</a></li></div>
							<div class='mypower' id='off'><li><a class='dropdown-item' href='#'>Auto Power Off</a></li></div>
							<div class='mypower' id='on&off'><li><a class='dropdown-item' href='#'>Auto Power On and Off</a></li></div>
						 </ul>
					</div>
				</span>
			</div>
		</div>
		</div>

		<div class="row">
			<div class="col-md-4 text-spacer text-center">
				<button class="btn btn-outline-success btn-sm my-2 my-sm-0 text-white" id="serButton"  title="Click to show radio serial port defaults" type="button">
					<i class="fas fa-cogs fa-fw"></i>
					Serial Defaults
				</button>
			</div>
			<div class="col-md-4 text-center text-spacer">
				<button class="btn btn-outline-success btn-sm my-2 my-sm-0 text-white" id="capsButton"  title="Click to show supported radio capabilities" type="button">
							<i class="fas fa-list fa-fw"></i>
					Radio Capabilities
				</button>
			</div>
			 <div class="col-md-4 text-center text-spacer">
				 <div class="form-check form-check-inline">
					 <label class="form-check-label text-white  text-center">
					 <input type="checkbox" id="splitCheck" title="Turn off for radios that don't support reading split" class="form-check-input">
						 Disable Split Polling
					 </input>
					 </label>
				 </div>
			 </div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Keyer</span>
					</div>
					<input type="text" class="form-control disable-text" readonly id="curKeyer"  title="Keyer" aria-lable="keyer" aria-describedby="keyer-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="keyerSel" data-size="3" type="button" title="Keyer List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right menu-scroll" id="keyerList">
								<div class='mykeyer' id='non'><li><a class='dropdown-item' href='#'>None</a></li></div>
								<div class='mykeyer' id='rpk1'><li><a class='dropdown-item' href='#'>RigPi Keyer</a></li></div>
								<div class='mykeyer' id='cat'><li><a class='dropdown-item' href='#'>via CAT</a></li></div>
								<div class='mykeyer' id='wkr'><li><a class='dropdown-item' href='#'>WinKeyer</a></li></div>
								<div class='mykeyer' id='ext'><li><a class='dropdown-item' href='#'>External CTS</a></li></div>
							 </ul>
						</div>
					</span>
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Tx Pwr</span>
					</div>
					<input type="text" class="form-control"  title="Transmitter Power (Watts)" id="curPwr" aria-lable="pwr" aria-describedby="pwr-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Video</span>
					</div>
					<input type="text" class="form-control disable-text" readonly id="curVideo"  title="Video Display" aria-lable="video" aria-describedby="video-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="videoSel" data-size="3" type="button"  title="Video Display List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<div class="dropdown-menu dropdown-menu-right menu-scroll" id='videoList' aria-labelledby="videoSelectButton">
								<div class='myvideo' id='1'><li><a class='dropdown-item' href='#'>No Video</a></li></div>
								<div class='myvideo' id='2'><li><a class='dropdown-item' href='#'>Video -> S-meter</a></li></div>
								<div class='myvideo' id='3'><li><a class='dropdown-item' href='#'>Video -> Frequency Panel</a></li></div>
								<div class='myvideo' id='4'><li><a class='dropdown-item' href='#'>Video -> Tuning Knob</a></li></div>
							</div>
						</div>
					</span>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Keyer Port</span>
					</div>
					<input type="text" class="form-control" id="curKeyerPort"  title="Keyer Port" aria-lable="keyerport" aria-describedby="keyerport-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="cwPortSel" data-size="3" type="button" title="Port List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right menu-scroll" id="portList">
								<?php require $dRoot . "/programs/cwPortScan.php"; ?>
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
						<span class="input-group-text radio-group-addon">Slave Port</span>
					</div>
					<input type="text" class="form-control disable-text" readonly id="curSlavePort"  title="Slave Port" aria-lable="port" aria-describedby="manufacturer-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="slavePortSel" data-size="3" type="button" title="Slave Port List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right menu-scroll" id="slavePortList">
								<?php require $dRoot . "/programs/slavePortScan.php"; ?>
							 </ul>
						</div>
					</span>
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Slv Baud</span>
					</div>
					<input type="text" class="form-control disable-text" readonly id="curSlaveBaud"  title="Slave Baud Rate" aria-lable="baud" aria-describedby="baud-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="slaveBaudSel" data-size="3" type="button"  title="Comm Speed List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<div class="dropdown-menu dropdown-menu-right menu-scroll" id='baudList' aria-labelledby="baudSelectButton">
								<div class='myslavebaud' id='0000'><li><a class='dropdown-item' href='#'>default</a></li></div>
								<div class='myslavebaud' id='1200'><li><a class='dropdown-item' href='#'>1200</a></li></div>
								<div class='myslavebaud' id='2400'><li><a class='dropdown-item' href='#'>2400</a></li></div>
								<div class='myslavebaud' id='4800'><li><a class='dropdown-item' href='#'>4800</a></li></div>
								<div class='myslavebaud' id='9600'><li><a class='dropdown-item' href='#'>9600</a></li></div>
								<div class='myslavebaud' id='19200'><li><a class='dropdown-item' href='#'>19200</a></li></div>
								<div class='myslavebaud' id='38400'><li><a class='dropdown-item' href='#'>38400</a></li></div>
								<div class='myslavebaud' id='57600'><li><a class='dropdown-item' href='#'>57600</a></li></div>
								<div class='myslavebaud' id='15200'><li><a class='dropdown-item' href='#'>115200</a></li></div>
							</div>
						</div>
					</span>
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Slv Cmnd</span>
					</div>
					<input type="text" class="form-control disable-text" readonly id="curSlaveCommand"  title="Slave Baud Rate" aria-lable="baud" aria-describedby="baud-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="slaveCommandSel" data-size="3" type="button"  title="Commmand List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<div class="dropdown-menu dropdown-menu-right menu-scroll" id='commandList' aria-labelledby="baudSelectButton">
								<div class='myslavecommand' id='knon'><li><a class='dropdown-item' href='#'>None</a></li></div>
								<div class='myslavecommand' id='kif'><li><a class='dropdown-item' href='#'>Kenwood IF</a></li></div>
								<div class='myslavecommand' id='kfa'><li><a class='dropdown-item' href='#'>Kenwood FA</a></li></div>
								<div class='myslavecommand' id='kbcd'><li><a class='dropdown-item' href='#'>Band BCD</a></li></div>
								<div class='myslavecommand' id='kdec'><li><a class='dropdown-item' href='#'>Macro Decimal</a></li></div>
							</div>
						</div>
					</span>
				</div>
			</div>
		</div>	
		<div class="row">
			<div class="col-md-4 text-spacer">
			</div>
			<div class="col-md-4 text-spacer">
			</div>
		</div>	
		<div class="row">
		</div>
		<div class="row">
			<div class="col-md-3 text-spacer text-center">
				<button class="btn btn-outline-success btn-sm my-2 my-sm-0 text-white" id="connectButton"  title="Click to Connect to Radio" type="button">
					<i class="fas fa-play fa-fw"></i>
					Connect Radio
				</button>
			</div>
			<div class="col-md-1 text-center text-spacer">
				<div class="text-spacer" id="spinner"><i class="fas fa-sync" style="color:white;font-size:18px"></i></div>
			 </div>
			<div class="col-md-3 text-center text-spacer">
				<button class="btn btn-outline-success btn-sm my-2 my-sm-0 text-white" id="testButton"  title="Click to diagnose Radio connection" type="button">
					<i class="fas fa-play fa-fw"></i>
					Test Radio
				</button>
			</div>
			<div class="col-md-1 text-center text-spacer">
				<div class="text-spacer" id="spinnerTest"><i class="fas fa-sync" style="color:white;font-size:18px"></i></div>
			 </div>
			<div class="col-md-4 text-center text-spacer">
				<button class="btn btn-outline-danger btn-sm my-2 my-sm-0 text-white" id="disconnectButton"  title="Click to disconnect from Radio" type="button">
							<i class="fas fa-stop fa-fw"></i>
					Disconnect Radio
				</button>
			</div>
		</div>
	</div>
		</div>
	<?php require $dRoot . "/includes/footer.php"; ?>
	<?php require $dRoot . "/includes/modal.txt"; ?>
	<?php require $dRoot . "/includes/modalCopy.txt"; ?>
	<?php require $dRoot . "/includes/modalAlert.txt"; ?>
	<?php require $dRoot . "/includes/modalCancelAlert.txt"; ?>
	<?php require $dRoot . "/includes/modalCancelOnly.txt"; ?>
	<script src="./Bootstrap/popper.min.js"</script>
	<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
	<script src="./Bootstrap/jquery-ui.js"></script>
	<script src="./Bootstrap/bootstrap.min.js"></script>
	<script src="js/nav-active.js"></script>
</body>
</html>
