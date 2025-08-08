<?php
session_start();
$tUserName=$_SESSION['myUsername'];
$instance=$_SESSION['myInstance'];
$tCall=$_SESSION['myCall'];
$dRoot = "/var/www/html";
require_once $dRoot . "/classes/Membership.php";
$tRadioNum = require_once $dRoot . "/programs/GetSelectedRadioInc.php";
require_once $dRoot . "/programs/GetMyRadioFunc.php";
require_once $dRoot . "/classes/Membership.php";
$membership = new Membership();
$membership->confirm_Member($tUserName);
$tMyRadioModel=myRadio($tRadioNum, 'Model');
$tMyRadioName=myRadio($tRadioNum, 'RadioName');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<title>RigPi Radio Settings (Basic))</title>
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
		var cManu='';
		var radioID='';
		var tMyRadio='0';
		var tMyPort=4532;
		var tMyCall="<?php echo $tCall; ?>";
		var tCall=tMyCall;
		var tMyCWPort="/dev/ttyS0";  //later found from db
		var tMyRotorPort="/dev/ttyUSB1";  //later found from db
		var tMyKeyer="non";
		var tMyPTT=1;
		var tUserName=<?php echo "'" . $tUserName . "'"; ?>;
		var tUser='';
		var tUDPPort=2333;
		var tMyRadioName='';
		var tRadioPort="/dev/ttyUSB0";
		var tMyKeyerFunction=0;
		var tMyKeyerPort;
		var tMyKeyerIP;
		var tCWPort;
		var tMyTCPPort=30001;
		var tDisconnected=0;
		var tMyRadioModel=<?php echo "'" . $tMyRadioModel . "'"; ?>;
		var ipOK;
		var test=0;
		  $(document).ready(function(){
			  $.getScript("/js/modalLoad.js");

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

			var el=$('#spinner');
			el.hide();

			   function getKeyerID(which)
			   {
				if(which=="None")
				{
					which='non';
				}else if (which=="RigPi Keyer")
				{
					which='rpk';
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

			  $.post('/programs/GetUserField.php', {un:tUserName,field:'uID'}, function(response){
				  tMyRadio=response;
				tMyPort=tMyRadio*2+4530;
				tMyTCPPort=30000 + parseInt(response);
				$("#curID").val(response);
				getMyRadioFields();
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'Keyer', table: 'MySettings'}, function(response)
				{
					tMyKeyer=getKeyerID(response);
				});
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'Model', table: 'MySettings'}, function(response)
				{
					tMyRadioName=response;
				});
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
				{
					$('#searchText').val(response);
				});
				doScan();

				$.get('/programs/GetMyRadio.php', 'f=RotorPort&r='+tMyRadio, function(response) {
					  tMyRotorPort=response;
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
				
				$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'KeyerPort', table: 'MySettings'}, function(response)
				{
					tCWPort=response;
				});
	
			});

			$(document).on('click', '.radioSave', function() {
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

			$(document).on('click', '.radios', function() {
				var text = $(this).text();
				  $('#curRadio').val(text);
				  tMyRadioModel=text;
				  $('#curName').val(text);
				  doScan();
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
				window.open("/login.php","_self");
				form.submit();
			};                

			$(document).on('click', '.mykeyer', function() {
				var text = $(this).text();
				  $("#curKeyer").val(text);
				  tMyKeyer=getKeyerID(text);
				if ($('#curKeyer').val()=="RigPi Keyer"){
					$("#curKeyerPort").val("/dev/ttyS0");
					tMyCWPort="/dev/ttyS0";
				}
				if ($('#curKeyer').val()=="via CAT"||$('#curKeyer').val()=='None'){
					$("#curKeyerPort").val("None");
					tMyCWPort="None";
				}
			});

			$(document).on('click', '.myport', function() {
				var text = $(this).text();
				  $("#curPort").val(text);
				  tRadioPort=text;
			});
			$(document).on('click', '.myCWPort', function() {
				var text = $(this).text();
				  $("#curKeyerPort").val(text);
			});
			
			function disconnectRadio(){
			tSplitOn=0;
				var el=$('#spinner');
				  tDisconnected=1;
			   $.post("/programs/SetSettings.php", {field: "SplitOut", radio: tMyRadio, data: tSplitOn, table: "RadioInterface"});
			  $.post('./programs/disconnectRadio.php', {radio: tMyRadio, id: tMyRadioModel, user: tUserName, rotor: ''}, function (response) {
					$("#modalA-body").html('&nbsp;&nbsp;'+response);			  				
					$(".modalA-title").html("Radio Connection");
					  $("#myModalAlert").modal('show');
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

			$(document).on('click', '#connectButton', function() {
				var el=$('#spinner');
				el.show();
				el.addClass('fa-spin');
					if (tDisconnected==0){
					disconnectRadio();
				};
				waitRefresh=10;
				tDisconnected=0;
				var tOK=setMyRadioFields(1);
//				return;
				if (tOK==0){
					return;
				}
				if (tMyKeyer=='rpk'){
					tMyCWPort='/dev/ttyS0';
				}
	
//				setTimeout(function(){
//	  				$.post('./programs/hamlibDo.php', {test: 0, keyer: tMyKeyer, radio: tMyRadio, user: tUserName, port: tCWPort, tcpPort: "30001", rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction}, function(response) {
				$.post('./programs/h.php',{test: 0, keyer: tMyKeyer, radio: tMyRadio, user: tUserName, radioPort:tRadioPort, port: tCWPort, tcpPort: tMyTCPPort, rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction, UDPPort: tUDPPort, startUpDelay: 0},function(response){
					  $.post('./programs/hamlibDo.php', {test: 0, keyer: tMyKeyer, radio: tMyRadio, user: tUserName, radioPort:tRadioPort, port: tCWPort, tcpPort: tMyTCPPort, rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction, UDPPort:tUDPPort}, function(response) {
						  if (response.length>20){
							$("#modalA-body").html('&nbsp;&nbsp;'+response);			  				
							$(".modalA-title").html("Radio Connection");
							  $("#myModalAlert").modal('show');
							  if (response.indexOf("Now starting RigPi Radio")>0){
								  $('#connect').text("Radio connected");
							  }
							  if (tMyPTT==2){
								  $.post('/programs/doGPIOPTT.php', {PTTControl: "on"}); 			  				
							  }
						  }else{
//			  				alert(response);
						  }
						  el.removeClass('fa-spin');
						  el.hide();
					});
//				},2000);
			})
		});

			$(document).on('click', '#disconnectButton', function() {
				tDisconnected=1;
				  $.post('./programs/disconnectRadio.php', {radio: tMyRadio, id: tMyRadioModel, user: tUserName, rotor: tMyRadio}, function(response) {
					$("#modalA-body").html(response);			  				
					$(".modalA-title").html("Radio Connection");
					  $("#myModalAlert").modal('show');
					$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: "OFF", table: "RadioInterface"});
					$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: "OFF", table: "RadioInterface"});
					  if (tMyPTT==2){
						  $.post('/programs/doGPIOPTT.php', {PTTControl: "off"}); 			  				
					  }
				});
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
			$(".modal-pix").attr("src",'');
		  }
		  $('.modal-title').html(dx);
		  $('#myModal').modal({show:true});
	  });
	});
	$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: dx, table: "MySettings"});
  })

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
				$.post('/programs/RadioDB.php', 'm='+cManu, function(response) {
					$('#radioList').empty(); //remove all child nodes
					var newOption = response;
					$('#radioList').append(newOption);
				});
			  }

			$.get('/programs/GetMyRadio.php', 'f=Port&r='+tMyRadio, function(response) {
				$('#curPort').val(response);
			  var ipformat = /^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
			  ipOK=false;
			  if (response.indexOf(":")>0){
				  var resp=response.split(":");
				  if (ipformat.test(resp[0])){
					  ipOK=true;
				  }
			  }
			  
			});
		  
		  
						
			function doScan(){
			$.post('/programs/SetPHPVariable.php', {model:tMyRadioModel}, function(response1){
				$('#portList').html(response1);
				if (tMyRadioModel=="Net rigctl"){
//					$('#curPort').val('4532');
				}else if(tMyRadioModel=="Dummy"){
					$('#curPort').val("None");
				}else{
						if (response1.length<2 && 
						response.indexOf('localhost')==-1 &&
						!ipOK)
						{
						  $('#curPort').val("None");
						$("#modalA-body").html("&nbsp;&nbsp;Radio port has been set to 'None.' The specified port was not found.");			  				
						$(".modalA-title").html("Radio Port Error");
						  $("#myModalAlert").modal({show:true});
					  }
				 };
			});
		 };


			  function getMyRadioFields(){
				$.get('/programs/GetMyRadio.php', 'f=Manufacturer&r='+tMyRadio, function(response) {
					  $('#curManu').val(response);
					  cManu=response;
					  setRadioList();
				  });

				$.get('/programs/GetMyRadio.php', 'f=Model&r='+tMyRadio, function(response) {
					  $('#curRadio').val(response);
				  });

				$.get('/programs/GetMyRadio.php', 'f=Port&r='+tMyRadio, function(response) {
					  $('#curPort').val(response);
/*		  			$.post('programs/portScan.php', function(response1){
						  if (response1.indexOf(response)==-1 && response.length>4){
							  $('#curPort').val("None");
							$("#modalA-body").html("Radio port has been set to 'None.' The specified port was not found.");			  				
							$("#modalA-title").html("Radio Port Error");
							  $("#myModalAlert").modal({show:true});
						  }
					  });
*/	  			});

				$.get('/programs/GetMyRadio.php', 'f=Keyer&r='+tMyRadio, function(response) {
					  $('#curKeyer').val(response);
					  tMyKeyer=getKeyerID(response);
				  });	  			

				$.get('/programs/GetMyRadio.php', 'f=PTTMode&r='+tMyRadio, function(response) {
					  tMyPTT=response;
				  });	  			

				$.get('/programs/GetMyRadio.php', 'f=KeyerPort&r='+tMyRadio, function(response) {
					if ($('#curKeyer').val()=="RigPi Keyer"){
						  $("#curKeyerPort").val("/dev/ttyS0");
						  tMyCWPort="/dev/ttyS0";
					  }else{
						  $('#curKeyerPort').val(response);
						  tMyCWPort=response;
					  }
				  });

				$.get('/programs/GetMyRadio.php', 'f=RadioName&r='+tMyRadio, function(response) {
					  $('#curName').val(response);
				  });

				$.post('/programs/GetInterface.php', {field:'powerOut',radio:tMyRadio}, function(response) {
					  $('#curPwr').val(response);
				  });
			  };
			  $(document).on('click', '#modalAlertOK', function() {
				$("#myModalCAlert").modal('hide');
				$('#curName').val(transRadioName);
				saveRadio(3);
			  });
			  $(document).on('click', '#modalAlertClose', function() {
				$("#myModalCAlert").modal('hide');
				saveRadio(3);
			  });
			  
			  function saveRadio(nBypassAlert){
				  $.post('/programs/RadioID.php', 'tRadio=' + $('#curRadio').val(), function(response) {
					  radioID=response;
					  var manuID=$('#curManu').val();
					  tRadioPort=	$("#curPort").val();
					var tPort=$('#curPort').val();
					if (tPort.indexOf(":")>0){
						var tP=tPort.split(":");
						var tIP=tP[0];
					}else{
						var tIP='0.0.0.0';
					}
				  $.post("/programs/SetMyRadioBasic.php", {m: $("#curManu").val(), 
				  o: $("#curRadio").val(), p: $("#curPort").val(), n: $("#curName").val(), 
				  i: tMyRadio, k: $("#curKeyer").val(), kp: $("#curKeyerPort").val(), 
				  d: radioID}, function(response){
				  if (nBypassAlert==0){
					$("#modalA-body").html(response+"<p><p>Disconnect Radio and Reconnect from the Tuner window if you have made changes.");			  				
					$(".modalA-title").html("Settings Saved");
					  $("#myModalAlert").modal({show:true});
					if (tMyKeyer=='rpk'){
							  tMyCWPort='/dev/ttyS0';
						  }
						  
					  $.post('./programs/h.php',{test: 0, keyer: tMyKeyer, radio: tMyRadio, user: tUserName, radioPort:tRadioPort, port: tCWPort, tcpPort: tMyTCPPort, rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction, UDPPort: tUDPPort, startUpDelay: 0,instance:<?php echo $instance;?>},function(response){
							$.post('./programs/hamlibDo.php', {test: 0, keyer: tMyKeyer, radio: tMyRadio, user: tUserName, radioPort:tRadioPort, port: tCWPort, tcpPort: tMyTCPPort, rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction, UDPPort:tUDPPort}, function(response) {
								if (response.length>20){
								  $("#modalA-body").html('&nbsp;&nbsp;'+response);			  				
								  $(".modalA-title").html("Radio Connection");
									$("#myModalAlert").modal({show:true});
									if (response.indexOf("Now starting RigPi Radio")>0){
										$('#connect').text("Radio connected");
									}
									if (tMyPTT==2){
										$.post('/programs/doGPIOPTT.php', {PTTControl: "on"}); 			  				
									}
								}else{
	//												  alert(response);
								}
								el.removeClass('fa-spin');
								el.hide();
						  });
					  });
					  };
						tMyKeyer=$("#curKeyer").val()
						tMyKeyer=getKeyerID(tMyKeyer);
						tMyCWPort=$("#curKeyerPort").val();
						$.post("/programs/SetSettings.php", {field:"powerOut", data:$("#curPwr").val(), radio:tMyRadio, table:"RadioInterface"}, function(response){
					  });
				  });
			});
		};
			function setMyRadioFields(nBypassAlert) {
				 var radioID;
				 tMyRadioName=$('#curName').val();
				  $.post('/programs/RadioID.php', 'tRadio=' + tMyRadioModel, function(response) {
						radioID=response;
						var manuID=$('#curManu').val();
						tRadioPort=	$("#curPort").val();
						if (radioID==2){
						  var tPort=$('#curPort').val();
						  if (tPort.indexOf(":")>0){
							  var tP=tPort.split(":");
							  var tIP=tP[0];
						  }else{
							  var tIP='0.0.0.0';
						  }
						  $.post('/programs/getTransfer.php', {ip: tIP}, function(response) {
							  var tID=response.split("`");
							  transRadioName=tID[1].trim();
							  transRadioID=tID[0];
							  if ($('#curName').val() !== transRadioName){
								  $("#modalC-body").html("Do you want to use the Station radio name, "+tID[1]+", for this connection?");			  				
								  $("#modalC-title").html("Replace Radio Name?");
									$("#myModalCAlert").modal({show:true});
							  }
							})

						}else{  //connect to radio
							$.post("/programs/SetMyRadioBasic.php", {m: $("#curManu").val(), 
								o: $("#curRadio").val(), p: $("#curPort").val(), n: $("#curName").val(), 
								i: tMyRadio, k: $("#curKeyer").val(), kp: $("#curKeyerPort").val(), 
								d: radioID}, function(response){
								if (nBypassAlert==0){
									$("#modalA-body").html(response+"<p><p>Disconnect Radio and Reconnect from the Tuner window if you have made changes.");			  				
								  $(".modalA-title").html("Settings Saved");
									$("#myModalAlert").modal({show:true});
								  if (tMyKeyer=='rpk'){
									  tMyCWPort='/dev/ttyS0';
								  }
								  $.post('./programs/h.php',{test: 0, keyer: tMyKeyer, radio: tMyRadio, user: tUserName, radioPort:tRadioPort, port: tCWPort, tcpPort: tMyTCPPort, rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction, UDPPort: tUDPPort, startUpDelay: 0},function(response){
									$.post('./programs/hamlibDo.php', {test: 0, keyer: tMyKeyer, radio: tMyRadio, user: tUserName, radioPort:tRadioPort, port: tCWPort, tcpPort: tMyTCPPort, rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction, UDPPort:tUDPPort}, function(response) {
										if (response.length>20){
										  $("#modalA-body").html('&nbsp;&nbsp;'+response);			  				
										  $(".modalA-title").html("Radio Connection");
											$("#myModalAlert").modal({show:true});
											if (response.indexOf("Now starting RigPi Radio")>0){
												$('#connect').text("Radio connected");
											}
											if (tMyPTT==2){
												$.post('/programs/doGPIOPTT.php', {PTTControl: "on"}); 			  				
											}
										}else{
//											alert(response);
										}
										el.removeClass('fa-spin');
										el.hide();
								  });
							  });
							  };
								tMyKeyer=$("#curKeyer").val()
								tMyKeyer=getKeyerID(tMyKeyer);
								tMyCWPort=$("#curKeyerPort").val();
								$.post("/programs/SetSettings.php", {field:"powerOut", data:$("#curPwr").val(), radio:tMyRadio, table:"RadioInterface"}, function(response){
							  });
						  });
					};
						});
					};
		  });
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
						$("#modalA-body").html('&nbsp;&nbsp;'+tRadioUpdate);
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
		<div class="row" style="margin-bottom:10px;" >
			<div class="col-12  col-lg-4 btn-padding">
			</div>
			<div class="col-6 col-lg-4 text-center">
				<span class="label label-success text-white" style="cursor: default; margin-top:10px;">Basic Radio Settings (User: <?php echo $tUserName; ?>)</span>
			</div>
			<div class="col-6 col-lg-4 btn-padding">
				<button class='btn btn-color radioSave' type='button'>
					<i class="fas fa-cloud-upload-alt fa-lg"></i>
				</button>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-lg-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Manuf</span>
					</div>
					<input type="text" class="form-control disable-text" id="curManu" readonly="readonly" title="Selected Radio Manufacturer" aria-lable="manufacturer" aria-describedby="manufacturer-addon">
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
			<div class="col-lg-4 text-spacer ">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Model</span>
					</div>
					<input type="text" class="form-control disable-text" id="curRadio" readonly="readonly"  title="Selected Radio" aria-lable="radio" aria-describedby="radio-addon">
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
			<div class="col-lg-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Name</span>
					</div>
					<input type="text" class="form-control"  title="Radio Name" id="curName" aria-lable="name" aria-describedby="name-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-4 text-spacer">
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
							 </ul>
						</div>
					</span>
				</div>
			</div>
			<div class="col-lg-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Keyer</span>
					</div>
					<input type="text" class="form-control disable-text" id="curKeyer" readonly="readonly"  title="Keyer" aria-lable="keyer" aria-describedby="keyer-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="keyerSel" data-size="3" type="button" title="Keyer List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right menu-scroll" id="keyerList">
								<div class='mykeyer' id='non'><li><a class='dropdown-item' href='#'>None</a></li></div>
								<div class='mykeyer' id='rpk'><li><a class='dropdown-item' href='#'>RigPi Keyer</a></li></div>
								<div class='mykeyer' id='cat'><li><a class='dropdown-item' href='#'>via CAT</a></li></div>
								<div class='mykeyer' id='wkr'><li><a class='dropdown-item' href='#'>WinKeyer</a></li></div>
								<div class='mykeyer' id='ext'><li><a class='dropdown-item' href='#'>External CTS</a></li></div>
							 </ul>
						</div>
					</span>
				</div>
			</div>
			<div class="col-lg-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">K Port</span>
					</div>
					<input type="text" class="form-control" id="curKeyerPort" readonly="readonly"  title="Keyer Port" aria-lable="keyerport" aria-describedby="keyerport-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="cwPortSel" data-size="3" type="button" title="Port List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right menu-scroll" id="cwPortList">
								<?php require $dRoot . "/programs/cwPortScan.php"; ?>
							 </ul>
						</div>
					</span>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Tx Pwr</span>
					</div>
					<input type="text" class="form-control"  title="Transmit Meter Calibration" id="curPwr" aria-lable="pwr" aria-describedby="pwr-addon">
				</div>
			</div>
			<div class="col-6 col-md-2 text-center text-spacer">
				<button class="btn btn-outline-success btn-sm my-2 my-sm-0 text-white" id="connectButton"  title="Click to Connect to Radio" type="button">
							<i class="fas fa-play fa-fw"></i>
					Connect Radio
				</button>
			</div>
			<div class="col-6 col-md-2 text-center text-spacer">
				<div class="text-spacer" id="spinner"><i class="fas fa-sync" style="color:white;font-size:18px"></i></div>
			 </div>
			<div class="col-3 col-md-4 text-center text-spacer">
				<button class="btn btn-outline-danger btn-sm my-2 my-sm-0 text-white" id="disconnectButton"  title="Click to disconnect from Radio" type="button">
							<i class="fas fa-stop fa-fw"></i>
					Disconnect Radio
				</button>
			</div>
			</div>
		</div>
		<div class="row">
		</div>
		<div class="row">
		</div>
	</div>
	<?php require $dRoot . "/includes/footer.php"; ?>
	<?php require $dRoot . "/includes/modal.txt"; ?>
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
