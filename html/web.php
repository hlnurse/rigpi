<?php
/*
 * RigPi Web window
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
$dRoot = "/var/www/html";
require_once $dRoot . "/classes/Membership.php";
$membership = new Membership();
$membership->confirm_Member($tUserName);
?>
<!DOCTYPE html>
<html lang="en">
<!DOCTYPE html>
<html>
<head>
	
	<title>RigPi Web</title>

	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
	<link rel="shortcut icon" type="image/x-icon" href="docs/images/favicon.ico" />

	<link rel="stylesheet" href="/includes/leaflet.css" />
	<script src="/js/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
	<meta charset="utf-8">

	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
	Remove this if you use the .htaccess -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title><?php echo $tCall; ?> RigPi Web</title>
	<meta name="description" content="RigPi Web">
	<meta name="author" content="Howard Nurse, W6HN">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="apple-touch-icon" href="/favicon.ico">
	<link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">
	<script src="/Bootstrap/jquery.min.js" ></script>
	<script defer src="./awe/js/all.js" ></script>
	<link href="./awe/css/all.css" rel="stylesheet">
	<link href="./awe/css/fontawesome.css" rel="stylesheet">
	<link href="./awe/css/solid.css" rel="stylesheet">	

	<?php require $dRoot . "/includes/styles.php"; ?>

	 <script type="text/javascript">
		 var tMyRadio=1;
		 var dxLat='';
		 var dxLon='';
		 var tDX='';
		 var tUserName="<?php echo $tUserName; ?>";
		 var tUser='';
		 var tMyCall="<?php echo $tCall; ?>";
		 var tCall=tMyCall;
		 var speedPot=0;
		 var mymap;

		$(document).ready(function() {
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


			$.post('/programs/GetSelectedRadio.php', {un: tUserName}, function(response) 
			{
				tMyRadio=response;
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
				{
					var response1=response;
					if (response=="NG"){
						response="";
						response1="Unknown Call"
					}
					$('#searchText').val(response);
					var tDX=response;
					updateInfo(response1);
				});
				$(document).on('click', '#searchButton', function() 
				{
					tDX=$('#searchText').val().toUpperCase();
					$('#searchText').val(tDX);
					updateInfo(tDX);
					$.post("/programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tDX, table: "MySettings"});
				});
			});	
		});

		$.post('/programs/GetUserField.php',{un: tUserName, field: 'uID'}, function(response) {
		   tUser=response;
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
				updateInfo(tDX);
			};                
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
			
			function logOut(){
				openWindowWithPost("/login.php", {
				status: "loggedout",
				username: tUserName});
			}; 
			

		$(window).keydown(function(e){
			var t=e.key;
			e.multiple
			var w=e.which;
			if (w==191)
			{
				if (e.shiftKey){
					<?php require $dRoot . "/includes/shortcutsWeb.php"; ?>
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
			if (e.ctrlKey){
				var t=event.key;
				event.multiple
				var w=event.which;
				switch(w){
					case 88: //exit to login
					logOut();
				};
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
			}};
			if(e.keyCode == 13) {
				if ($('#searchText').val()==''){
					return false;
				}
				tDX=$('#searchText').val().toUpperCase();
				$('#searchText').val(tDX);
				document.getElementById('searchButton').click();
				$.post("/programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tDX, table: "MySettings"});
				updateInfo(tDX);
				return false;
			}
		});

		function updateInfo(dx){
			var dxLat;
			var dxLon;
			if (dx=="Unknown Call"){
				$('.web-title').html('<h1>'+dx+'</h1><h3>');
			}else{
				$('.web-title').html('<h1>'+dx+' Web</h1><h3>');
			}
			$.post("./programs/GetCallbook.php", {call: dx, what: 'QRZData', user: tUser, un: tUserName },function(response){
				$('.web-body').html(response);
				$.post("./programs/GetCallbook.php", {call: dx, what: 'QRZbio', user: tUser, un: tUserName},function(response){
					$('.web-bio').html('<p>'+response);
					$.post("./programs/GetCallbook.php", {call: dx, what: 'QRZpix', user: tUser, un: tUserName},function(response){
						  var aPix=response.split('|');
						  var h=aPix[1];
						  var w=aPix[2];
						  if (h>0){
							  var wP=(aPix[2]/350);
							  var tW=w/wP;
							  var tH=h/wP;
							  $(".webPix").attr("height",tH+"px");
							  $(".webPix").attr("width",tW+"px");
							  $(".webPix").attr("src",aPix[0]);
						  }else{
							  $(".webPix").attr("height","0px");
							  $(".webPix").attr("width","0px");
							  $(".webPix").attr("src",'');
						  }
						  $.post("./programs/GetCallbook.php", {call: dx, what: 'His_Latitude', user: tUser, un: tUserName},function(response){
							 dxLat=parseFloat(response);
							 $.post("./programs/GetCallbook.php", {call: dx, what: 'His_Longitude', user: tUser, un: tUserName},function(response){
								 dxLon=parseFloat(response);

//dxLat='41.714775';
//dxLon='-72.727260';
								doMap(dxLat,dxLon);
								$.post("./programs/GetCallbook.php", {call: dx, what: 'Abbreviation', user: tUser, un: tUserName},function(response){
								   var abbr=response;
								   if (abbr.length>0){
									   $(".flag").attr("src","./flags/"+abbr+"-flag.gif");
								   }
								});
							});
						});
					});
				});
			});
		}
		
		function doMap(lat,lon){
			if (mymap != undefined) { mymap.remove(); };
			mymap = L.map('mapid').setView([lat, lon], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
	maxZoom: 19,
	attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(mymap);
/*L.tileLayer('https://stamen-tiles-{s}.a.ssl.fastly.net/toner-lines/{z}/{x}/{y}{r}.{ext}', {
	attribution: 'Map tiles by <a href="http://stamen.com">Stamen Design</a>, <a href="http://creativecommons.org/licenses/by/3.0">CC BY 3.0</a> &mdash; Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
	subdomains: 'abcd',
	minZoom: 0,
	maxZoom: 20,
	ext: 'png'
}).addTo(mymap);
*/
/* L.tileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png', {
	maxZoom: 19,
	attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Tiles style by <a href="https://www.hotosm.org/" target="_blank">Humanitarian OpenStreetMap Team</a> hosted by <a href="https://openstreetmap.fr/" target="_blank">OpenStreetMap France</a>'
}).addTo(mymap);
*/			
			L.marker([lat, lon]).addTo(mymap);
			
/*			L.circle([lat, lon], {
				color: 'red',
				fillColor: '#f03',
				fillOpacity: 0.2,
				radius: 500
			}).addTo(mymap);
*/
/*			 L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
				attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
			}).addTo(mymap);
*/		}
		
		var tUpdate = setInterval(bearingTimer,1000)
		function bearingTimer()
		{
			$.post("./programs/GetRotorIn.php", {rotor: tMyRadio},function(response){
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
			  $("#modalA-title").html("RigPi Report");
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
		function updateTimer(){
			updateFooter();
			var now = new Date();
			var now_hours=now.getUTCHours();
			now_hours=("00" + now_hours).slice(-2);
			var now_minutes=now.getUTCMinutes();
			now_minutes=("00" + now_minutes).slice(-2);
			$("#fPanel5").text(now_hours+":"+now_minutes+'z');
		  }
	</script>
</head>
<body class="body-white-scroll margin-left:auto margin-right:auto" id="web">
<?php require $dRoot . "/includes/header.php"; ?>
<div class="container-fluid">
	<div class="row" style="background-color: #f3ffff;">
		
		&nbsp;
		<div  class="col-12 web-title" style="text-indent: 40px; text-align:left"></div>
	</div>
</div>
	<div class="row" >
		<!-- Web body -->
		<div class="col-sm-12 col-xl-5 web-body" style="background-color: #f3ffff; margin-left:20px">
			Loading...
		</div>
		<div class="col-sm-12 col-xl-2 flag" style="background-color: #f3ffff; ">
			<img class='flag'  style="background-color: #f3ffff; align-text:center;height:100px;width:150px;"></img><br>
		</div>
		<div class="col-sm-12 col-xl-4" style="background-color: #f3ffff;" id="pixFrame">
			<img class='webPix modal-pix' src=''></img>
		</div>
	</div>
	<div class="row">
		<!-- Web bio -->
		<div class="col-sm-12 col-xl-12 web-bio" style="background-color: #f3ffff; margin-left:20px">
		</div>
	</div>
	<div class="row" >
		<!-- Web map -->
		<div class="col-md-12 col-xl-12 web-pix" id="mapFrame">
		<div id="mapid" style="background-color: #f3ffff; width: 400px; height: 400px;"></div>
		</div>
	</div>
<?php require $dRoot . "/includes/footer.php"; ?>
<?php require $dRoot . "/includes/modalCancelOnly.txt"; ?>
<script src="Bootstrap/popper.min.js"</script>
<link rel="stylesheet" href="Bootstrap/jquery-ui.css">
<script src="Bootstrap/jquery-ui.js"></script>
<script src="Bootstrap/bootstrap.min.js"></script>
<script src="js/nav-active.js"></script>


</body>
</html>
