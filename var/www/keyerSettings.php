<?php
if (!isset($GLOBALS['htmlPath'])){
	$GLOBALS['htmlPath']=$_SERVER['DOCUMENT_ROOT'];
}
$dRoot=$GLOBALS['htmlPath'];
$tCall=$_GET["c"];
$tUserName=$_GET["x"];
require_once($dRoot.'/classes/Membership.php');
$membership = new Membership();
$membership->confirm_Member($tUserName);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<meta charset="utf-8">

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo $tCall;?> RigPi Keyer Settings</title>
	<meta name="RigPi Keyer Settings" content="">
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
	<?php require($dRoot."/includes/styles.php"); ?>
    <script type="text/javascript">
  		var tMyRadio="1";
  		var tMyCall='<?php echo $tCall;?>';
  		var tCall=tMyCall
  		var tMyKeyer="non";
  		var tMyRadioName
        var tUserName='<?php echo $tUserName;?>';
        var tUser='';
  		$(document).ready(function()
  		{
//	  		$("#IPValueAll").hide();
//	  		$("#portValueAll").hide();
	  		$("#set-text").hide();
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

			$.post('/programs/GetSelectedRadio.php', {un:tUserName}, function(response) {
				tMyRadio=response;
				$("#curID").val(response);
		        $.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'Keyer', table: 'MySettings'}, function(response)
		        {
					tMyKeyer=getKeyerID(response);
			        $.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'RadioName', table: 'MySettings'}, function(response)
			        {
				        var tCap=tMyRadio+ " ("+response+")";
						$('#topCaption').text(tCap);
					  	$('.pageLabel').innerHTML="<i>RigPi Keyer not assigned</i>";
						if (tMyKeyer=="rpk")
						{
					  		$('.pageLabel').innerHTML="RigPi Keyer Settings for Radio "+tCap;
					  	}else if (tMyKeyer="wkr")
					  	{
					  		$('.pageLabel').innerHTML="WinKeyer Settings for Radio "+tCap;
						}else{
					  		$('.pageLabel').innerHTML="<i>RigPi Keyer not Assigned to Radio "+tCap+"</i>";
						}
				    });
			    });

                $(document).on('click', '#logoutButton', function() 
                {
					openWindowWithPost("/login.php", {
					    status: "loggedout",
					    username: tUserName});
                });	
                
                $(document).on('click', '#invertKeying', function() 
                {
					setMyKeyerFields();
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

				$(document).on('click', '#testButton', function()
				{
				   	$.post("/programs/SetSettings.php", {field: "CWInitCk", radio: tMyRadio, data: "1", table: "RadioInterface"});
					setTimeout(function(){
					   	$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: "V V", table: "RadioInterface"});
					}, 500);
				});

				$(document).on('click', '#restoreButton', function()
				{
					restoreMyKeyerFields();
					setTimeout(function(){
					   	$.post("/programs/SetSettings.php", {field: "CWIn", radio: tMyRadio, data: "R R", table: "RadioInterface"});
					   	$.post("/programs/SetSettings.php", {field: "CWInCk", radio: tMyRadio, data: "1", table: "RadioInterface"});
					}, 500);
				});

	 			$("input").bind("keydown", function(event) 
				{
	                // track enter key
	                var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
	                if (keycode == 13) { // keycode for enter key
		                event.preventDefault();
		                var $this=$(event.target);
		                var index = parseFloat($this.attr('data-index'));
		                $('[data-index="' + (index+1).toString() + '"]').focus();
/*	                    if ($('#searchText').val()==''){
	                    	return false;
	                    }
		                var tDX=$('#searchText').val().toUpperCase();
		                $('#searchText').val(tDX);
	                    document.getElementById('searchButton').click();
						$.post("/programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tDX, table: "MySettings"});
*/
//	                    return false;
	                } else  {
	                    return true;
	                }
	            });
	            
			   	$(document).on('click', '.myMode', function()
			   	{
					var text = $(this).text();
		  			$("#curMode").val(text);
	  				setMyKeyerFields();
				});
				
			   	$(document).on('click', '.myFunction', function()
			   	{
					var text = $(this).text();
	  				$("#curFunction").val(text);
	  				setMyKeyerFields();
				});
				
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
				{
					$('#searchText').val(response);
		    	});

				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'KeyerInvert', table: 'MySettings'}, function(response)
				{
					if (response==1){
						document.getElementById("invertKeying").checked=true;
					}else{
						document.getElementById("invertKeying").checked=false;
					}
		    	});

			   	getMyKeyerFields();
			});

		    function setMyKeyerFields()
		    {
				var modeVal=$("#curMode").val();
				var speedVal=$("#speedValue").val();
				var sidetoneVal1=parseInt($("#sidetoneValue").val());
				var sidetoneVal=62500/sidetoneVal1;
				sidetoneVal=sidetoneVal.toFixed(4);
				var weightVal=$("#weightValue").val();
				var leadinVal=$("#leadinValue").val();
				var tailVal=$("#tailValue").val();
				var minimumVal=$("#minimumValue").val();
				var rangeVal=$("#rangeValue").val();
				var x2modeVal=0;
				var compVal=$("#compValue").val();
				var farnsworthVal=$("#farnsworthValue").val();
				var functionVal=$("#curFunction").val();
				var paddleVal=$("#paddleValue").val();
				var ditdahVal=$("#ditdahValue").val();
				var remotePort=$("#portValue").val();
				var remoteIP=$("#IPValue").val();
				if (document.getElementById("invertKeying").checked==true){
					doInvert=1;
				}else{
					doInvert=0;
				};
				var tpttOnVal=document.getElementById("pttCheck").checked;
				var tpttRadioOnVal=document.getElementById("pttOnCheck").checked;
				if (tpttRadioOnVal==true){
					tpttRadioOnVal=1;
				}else{
					tpttRadioOnVal=0;
				}
				var pttOnVal;
				if (tpttOnVal){
					pttOnVal=1;
				}else{
					pttOnVal=0;
				}
				var sidetoneOnVal=document.getElementById("sidetoneCheck").checked;
				var ctVal=document.getElementById("ctCheck").checked;
				var autoVal=document.getElementById("autoCheck").checked;
				var swapVal=document.getElementById("swapCheck").checked;
				var watchVal=document.getElementById("disCheck").checked;
				var x1modeVal=0; 
				var pinVal=0;
				if (sidetoneOnVal){
					pinVal=2;
				}
				if (pttOnVal==1){
					pinVal=pinVal|1;
				}
				pinVal=pinVal|4; //keyout1 enable even thiough schematic and docs say this is for keyout2.
				var tModeVal=0;
				if (modeVal=="Iambic B")
			   	{
				   tModeVal=tModeVal;
			   	}else if(modeVal=="Iambic A")
			   	{
				   tModeVal=tModeVal|0x10;
			   	}else if(modeVal=="Ultimatic")
			   	{
				   tModeVal=tModeVal|0x20;
			   	}else if(modeVal=="Vibrobug")
			   	{
				   tModeVal=tModeVal|0x30;
				}
				tModeVal=tModeVal|0x40;	//paddle echo
				tModeVal=tModeVal|0x04;	//keyer echo
				if (ctVal==1)
				{
					tModeVal=tModeVal|0x01;
				}
				if (autoVal==1)
				{
					tModeVal=tModeVal|0x02;
				}
				if (swapVal==1)
				{
					tModeVal=tModeVal|0x08;
				}
				if (watchVal==1)
				{
					tModeVal=tModeVal|0x80;
				}
				var tFunctionVal=0;
				if (functionVal=="Normal")
			   	{
				   tFunctionVal=tFunctionVal;
			   	}else if(functionVal=="Radio Keyer")
			   	{
				   tFunctionVal=1;
			   	}else if(functionVal=="Remote Keyer")
			   	{
				   tFunctionVal=2;
			   	}
				if (functionVal=="Remote Keyer"){
	  				$("#IPValueAll").show();
	  				$("#portValueAll").show();	  				
	  				$("#doInvert").show();
	  				$("#set-text").hide();
				}else if (functionVal=="Radio Keyer"){
	  				$("#IPValueAll").hide();
	  				$("#portValueAll").show();
	  				$("#doInvert").hide();
		  			$("#set-text").show();
				}else{
	  				$("#IPValueAll").hide();
	  				$("#portValueAll").hide();
	  				$("#doInvert").hide();
	  				$("#set-text").hide();
				}
				$.post("/programs/SetSettings.php", {field: "WKMode", radio: tMyRadio, data: tModeVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: speedVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKSidetone", radio: tMyRadio, data: sidetoneVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKWeight", radio: tMyRadio, data: weightVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKLeadin", radio: tMyRadio, data: leadinVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKTail", radio: tMyRadio, data: tailVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKMinWPM", radio: tMyRadio, data: minimumVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKWPMRange", radio: tMyRadio, data: rangeVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKX2Mode", radio: tMyRadio, data: x2modeVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKKeyComp", radio: tMyRadio, data: compVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKFarnsworth", radio: tMyRadio, data: farnsworthVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKPaddleSet", radio: tMyRadio, data: paddleVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKDitDahRatio", radio: tMyRadio, data: ditdahVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKPinConf", radio: tMyRadio, data: pinVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKX1Mode", radio: tMyRadio, data: x1modeVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKPTT", radio: tMyRadio, data: tpttRadioOnVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKFunction", radio: tMyRadio, data: tFunctionVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKRemotePort", radio: tMyRadio, data: remotePort, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKRemoteIP", radio: tMyRadio, data: remoteIP, table: "Keyer"});

			   	$.post("/programs/SetSettings.php", {field: "KeyerInvert", radio: tMyRadio, data: doInvert, table: "MySettings"});
	
			   	$.post("/programs/SetSettings.php", {field: "CWInitCk", radio: tMyRadio, data: "1", table: "RadioInterface"});
			   	$.post("/programs/SetSettings.php", {field: "CWChangeCk", radio: tMyRadio, data: "1", table: "RadioInterface"});
			};
			
			$("#speedValue").focusout(function(){
				setMyKeyerFields();
			});
	
			$("#modeSel").focusout(function(){
				setMyKeyerFields();
			});
			
			$("#sidetoneValue").focusout(function(){
				setMyKeyerFields();
			});
	
			$("#weightValue").focusout(function(){
				setMyKeyerFields();
			});
	
			$("#leadinValue").focusout(function(){
				setMyKeyerFields();
			});
	
			$("#tailValue").focusout(function(){
				setMyKeyerFields();
			});
	
			$("#minimumValue").focusout(function(){
				setMyKeyerFields();
			});
	
			$("#rangeValue").focusout(function(){
				setMyKeyerFields();
			});
	
			$("#compValue").focusout(function(){
				setMyKeyerFields();
			});
	
			$("#farnsworthValue").focusout(function(){
				setMyKeyerFields();
			});
	
			$("#paddleValue").focusout(function(){
				setMyKeyerFields();
			});
	
			$("#ditdahValue").focusout(function(){
				setMyKeyerFields();
			});
	
			$("#sidetoneCheck").click(function(){
				setMyKeyerFields();
			});
	
			$("#pttCheck").click(function(){
				setMyKeyerFields();
			});
	
			$("#swapCheck").click(function(){
				setMyKeyerFields();
			});
	
			$("#autoCheck").click(function(){
				setMyKeyerFields();
			});
	
			$("#ctCheck").click(function(){
				setMyKeyerFields();
			});
	
			$("#disCheck").click(function(){
				setMyKeyerFields();
			});
	
			$("#pttOnCheck").click(function(){
				setMyKeyerFields();
			});
	
			$("#functionSel").focusout(function(){
				setMyKeyerFields();
			});
			
			$("#portValue").focusout(function(){
				setMyKeyerFields();
			});
			
			$("#IPValue").focusout(function(){
				setMyKeyerFields();
			});
			
			$("#sidetoneValue").focusout(function(){
				setMyKeyerFields();
			});
			
			$("#IPValue").focusout(function(){
				setMyKeyerFields();
			});
			
			$("#inverKeying").focusout(function(){
				setMyKeyerFields();
			});
			
			function restoreMyKeyerFields()
			{
			   var modeVal="68";
			   var speedVal="20";
			   var sidetoneVal=(62500/600).toFixed(4);
			   var weightVal="50";
			   var leadinVal="0";
			   var tailVal="0";
			   var minimumVal="5";
			   var rangeVal="30";
			   var x2modeVal="0";
			   var compVal="0";
			   var farnsworthVal="8";
			   var paddleVal="50";
			   var ditdahVal="50";
			   var x1modeVal="0";
			   var pinVal="7";
			   $("#modeValue").val(modeVal);
			   $("#speedValue").val(speedVal);
			   $("#sidetoneValue").val(600);
			   $("#weightValue").val(weightVal);
			   $("#leadinValue").val(leadinVal);
			   $("#tailValue").val(tailVal);
			   $("#minimumValue").val(minimumVal);
			   $("#rangeValue").val(rangeVal);
			   $("#compValue").val(compVal);
			   $("#farnsworthValue").val(farnsworthVal);
			   $("#paddleValue").val(paddleVal);
			   $("#ditdahValue").val(ditdahVal);
			   document.getElementById("pttCheck").checked=true;
			   document.getElementById("sidetoneCheck").checked=true;
			   	$.post("/programs/SetSettings.php", {field: "WKMode", radio: tMyRadio, data: modeVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKSpeed", radio: tMyRadio, data: speedVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKSidetone", radio: tMyRadio, data: sidetoneVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKWeight", radio: tMyRadio, data: weightVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKLeadin", radio: tMyRadio, data: leadinVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKTail", radio: tMyRadio, data: tailVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKMinWPM", radio: tMyRadio, data: minimumVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKWPMRange", radio: tMyRadio, data: rangeVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKX2Mode", radio: tMyRadio, data: x2modeVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKKeyComp", radio: tMyRadio, data: compVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKFarnsworth", radio: tMyRadio, data: farnsworthVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKPaddleSet", radio: tMyRadio, data: paddleVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKDitDahRatio", radio: tMyRadio, data: ditdahVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKPinConf", radio: tMyRadio, data: pinVal, table: "Keyer"});
			   	$.post("/programs/SetSettings.php", {field: "WKX1Mode", radio: tMyRadio, data: x1modeVal, table: "Keyer"});
	
			   	$.post("/programs/SetSettings.php", {field: "CWInitCk", radio: tMyRadio, data: "1", table: "RadioInterface"});
			   	$.post("/programs/SetSettings.php", {field: "CWChangeCk", radio: tMyRadio, data: "1", table: "RadioInterface"});
			   	$.post("/programs/SetSettings.php", {field: "WKPTT", radio: tMyRadio, data: "0", table: "Keyer"});
		   };

			$.getScript("/js/modalLoad.js");
	
		});

	   	function getMyKeyerFields()
	   	{
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKMode'}, function(response) {
				var tMode=parseInt(response);
				if ((tMode & 0x30)==0x30){
					$("#curMode").val('Vibrobug');
				}else if ((tMode & 0x30)==0x10){
					$("#curMode").val('Iambic A');
				}else if ((tMode & 0x30)==0x20){
					$("#curMode").val('Ultimatic');
				}else if ((tMode & 0x30)==0x00){
					$("#curMode").val('Iambic B');
				}
				if ((tMode & 1)==1){
					document.getElementById("ctCheck").checked=true;
				}else{
					document.getElementById("ctCheck").checked=false;
				}
				if ((tMode & 2)==2){
					document.getElementById("autoCheck").checked=true;
				}else{
					document.getElementById("autoCheck").checked=false;
				}
				if ((tMode & 8)==8){
					document.getElementById("swapCheck").checked=true;
				}else{
					document.getElementById("swapCheck").checked=false;
				}
				if ((tMode & 128)==128){
					document.getElementById("disCheck").checked=true;
				}else{
					document.getElementById("disCheck").checked=false;
				}
			});
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKFunction'}, function(response) {
				var tFunc=parseInt(response);
				if (tFunc==0){
					$("#curFunction").val('Normal');
				}else if (tFunc==1){
					$("#curFunction").val('Radio Keyer');
				}else if (tFunc==2){
					$("#curFunction").val('Remote Keyer');
				}
				if (tFunc==2){
	  				$("#IPValueAll").show();
	  				$("#set-text").hide();
				}else{
	  				$("#IPValueAll").hide();
	  				if (tFunc==1){
		  				$("#set-text").show();
	  				}
				}
				if (tFunc==2){
	  				$("#IPValueAll").show();
	  				$("#portValueAll").show();
	  				$("#set-text").hide();
	  				$("#doInvert").show();
				}else if (tFunc==1){
	  				$("#IPValueAll").hide();
	  				$("#portValueAll").show();
		  			$("#set-text").show();
	  				$("#doInvert").hide();
				}else{
	  				$("#IPValueAll").hide();
	  				$("#portValueAll").hide();
	  				$("#set-text").hide();
	  				$("#doInvert").hide();
				}
			});
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKSpeed'}, function(response) {
				$("#speedValue").val(parseInt(response));
			});
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKSidetone'}, function(response) {
				var response1=62500/response;
				response1=Math.round(response1);
				$("#sidetoneValue").val(response1);
			});
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKWeight'}, function(response) {
				$("#weightValue").val(parseInt(response));
			});
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKLeadin'}, function(response) {
				$("#leadinValue").val(parseInt(response));
			});
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKTail'}, function(response) {
				$("#tailValue").val(parseInt(response));
			});
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKMinWPM'}, function(response) {
				$("#minimumValue").val(parseInt(response));
			});
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKWPMRange'}, function(response) {
				$("#rangeValue").val(parseInt(response));
			});
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKKeyComp'}, function(response) {
				$("#compValue").val(parseInt(response));
			});
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKFarnsworth'}, function(response) {
				$("#farnsworthValue").val(parseInt(response));
			});
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKPaddleSet'}, function(response) {
				$("#paddleValue").val(parseInt(response));
			});
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKDitDahRatio'}, function(response) {
				$("#ditdahValue").val(parseInt(response));
			});
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKRemoteIP'}, function(response) {
				$("#IPValue").val(response);
			});
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKRemotePort'}, function(response) {
				$("#portValue").val(response);
			});
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKPinConf'}, function(response) {
				var tCheck=parseInt(response);
				if ((tCheck & 1)==1){
					document.getElementById("pttCheck").checked=true;
				}else{
					document.getElementById("pttCheck").checked=false;
				}
				if ((tCheck & 2)==2){
					document.getElementById("sidetoneCheck").checked=true;
				}else{
					document.getElementById("sidetoneCheck").checked=false;
				}
			});
			$.post('/programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKPTT'}, function(response) {
				var tCheck=parseInt(response);
				if ((tCheck & 1)==1){
					document.getElementById("pttOnCheck").checked=true;
				}else{
					document.getElementById("pttOnCheck").checked=false;
				}
			});
	   	}

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
			}
			return which;
	   	}

		var tUpdate = setInterval(updateTimer,1000);
		function updateTimer(){
	       $.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:<?php echo "'" . $tCall . "'"; ?>}, function(response) 
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

	  	$.getScript("/js/addPeriods.js");

	</script>
	<?php require($dRoot."/programs/ManufacturersDB.php");?>
</head>

<body class="body-black" id="keyersettings" >
	<?php require($dRoot.'/includes/header.php');?>
	<div class="container-fluid">
		<div class="row" style="margin-bottom:10px;">
			<div class="col-sm-12 text-center">
				<div class="label label-success text-white pageLabel" style="cursor: default; margin-top:10px;">CW Keyer Settings (User: <?php echo $tUserName; ?>)</div>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text keyer-group-addon" >Speed</span>
					</div>
					<input type="text" class="form-control" onfocus="this.select();" data-index="2" placeholder="25" id="speedValue" aria-lable="speed" aria-describedby="speed-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text keyer-group-addon">Paddle Md</span>
					</div>
					<input type="text" class="form-control disable-text noselect" tabindex="-1" readonly="readonly" id="curMode" aria-lable="mode" aria-describedby="mode-addon">
				    <span class="input-group-btn">
			            <div class="dropdown">
						    <button class="btn btn-primary dropdown-toggle" data-index="3" id="modeSel" data-size="3" type="button"  title="Select Paddle Mode" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
						    </button>
						    <ul class="dropdown-menu dropdown-menu-right menu-scroll" id="modeList">
								<div class='myMode' id='iamA'><li><a class='dropdown-item' href='#'>Iambic A</a></li></div>
								<div class='myMode' id='iamB'><li><a class='dropdown-item' href='#'>Iambic B</a></li></div>
								<div class='myMode' id='ulti'><li><a class='dropdown-item' href='#'>Ultimatic</a></li></div>
								<div class='myMode' id='bug'><li><a class='dropdown-item' href='#'>Vibrobug</a></li></div>
						     </ul>
			            </div>
				    </span>
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text keyer-group-addon">Sidetone</span>
					</div>
					<input type="text" class="form-control" placeholder="600" onfocus="this.select();"  data-index="4" id="sidetoneValue" aria-lable="speed" aria-describedby="speed-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text keyer-group-addon">Weight</span>
					</div>
					<input type="text" class="form-control" placeholder="50" onfocus="this.select();" data-index="5" id="weightValue" aria-lable="weight" aria-describedby="weight-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text keyer-group-addon">Leadin</span>
					</div>
					<input type="text" class="form-control" placeholder="25" onfocus="this.select();" data-index="6" id="leadinValue" aria-lable="speed" aria-describedby="speed-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text keyer-group-addon">Tail</span>
					</div>
					<input type="text" class="form-control" placeholder="25" onfocus="this.select();" data-index="7" id="tailValue" aria-lable="mode" aria-describedby="mode-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text keyer-group-addon">Min WPM</span>
					</div>
					<input type="text" class="form-control" placeholder="5" onfocus="this.select();" data-index="8" id="minimumValue" aria-lable="speed" aria-describedby="min-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text keyer-group-addon">WPM Rng</span>
					</div>
					<input type="text" class="form-control" placeholder="30" onfocus="this.select();" data-index="9" id="rangeValue" aria-lable="speed" aria-describedby="range-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text keyer-group-addon">Comp</span>
					</div>
					<input type="text" class="form-control" placeholder="50" onfocus="this.select();" data-index="10" id="compValue" aria-lable="speed" aria-describedby="speed-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<span class="input-group-text keyer-group-addon">Farns</span>
					<input type="text" class="form-control" placeholder="8" onfocus="this.select();" data-index="11" id="farnsworthValue" aria-lable="mode" aria-describedby="mode-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text keyer-group-addon">Paddle</span>
					</div>
					<input type="text" class="form-control" placeholder="50" onfocus="this.select();" data-index="12" id="paddleValue" aria-lable="speed" aria-describedby="speed-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text keyer-group-addon">Ratio</span>
					</div>
					<input type="text" class="form-control" placeholder="50" onfocus="this.select();" data-index="13" id="ditdahValue" aria-lable="mode" aria-describedby="mode-addon">
				</div>
			</div>
		</div>
		<div class="row">
			 <div class="col-md-4 text-center text-spacer">
			 	<div class="form-check form-check-inline">
				 	<label class="form-check-label text-white  text-center">
				 	<input type="checkbox" data-index="14"  id="sidetoneCheck" class="form-check-input">
				 		Enable Sidetone
				 	</input>
				 	</label>
			 	</div>
			 </div>
			 <div class="col-md-4 text-center text-spacer">
			 	<div class="form-check form-check-inline">
			 	<label class="form-check-label  text-white text-center">
			 	<input type="checkbox" data-index="15" id="pttCheck" class="form-check-input">
			 		Enable Keyer PTT
			 	</input>
			 	</label>
			 	</div>
			 </div>
			 <div class="col-md-4 text-center text-spacer">
			 	<div class="form-check form-check-inline">
			 	<label class="form-check-label  text-white text-center">
			 	<input type="checkbox" data-index="16" id="swapCheck" class="form-check-input">
			 		Swap Paddles
			 	</input>
			 	</label>
			 	</div>
			 </div>
		</div>
		<div class="row">
			 <div class="col-md-4 text-center text-spacer">
			 	<div class="form-check form-check-inline">
			 	<label class="form-check-label  text-white text-center">
			 	<input type="checkbox" data-index="17" id="autoCheck" class="form-check-input">
			 		AutoSpace
			 	</input>
			 	</label>
			 	</div>
			 </div>
			 <div class="col-md-4 text-center text-spacer">
			 	<div class="form-check form-check-inline">
			 	<label class="form-check-label  text-white text-center">
			 	<input type="checkbox" data-index="18" id="ctCheck" class="form-check-input">
			 		CT Space
			 	</input>
			 	</label>
			 	</div>
			 </div>
			 <div class="col-md-4 text-center text-spacer">
			 	<div class="form-check form-check-inline">
			 	<label class="form-check-label  text-white text-center">
			 	<input type="checkbox" data-index="19" id="disCheck" class="form-check-input">
			 		Disable Watchdog
			 	</input>
			 	</label>
			 	</div>
			 </div>
		</div>
		<div class="row">
			 <div class="col-md-4 text-center text-spacer">
			 	<div class="form-check form-check-inline">
			 	<label class="form-check-label  text-white text-center">
			 	<input type="checkbox" data-index="20" id="pttOnCheck" class="form-check-input">
			 		PTT ON when Radio Connected
			 	</input>
			 	</label>
			 	</div>
			 </div>
			<div class="col-md-4 text-spacer">
				<button class="btn btn-outline-success btn-sm my-2 my-sm-0 text-white  d-block mx-auto" data-index="21" id="restoreButton" type="button">
					<i class="fas fa-redo"></i>
					Reset Defaults
				</button>
			</div>
			<div class="col-md-4 text-spacer">
				<button class="btn btn-outline-success btn-sm my-2 my-sm-0 text-white  d-block mx-auto" data-index="22" id="testButton" type="button">
					<i class="fas fa-check"></i>
					Test Keyer
				</button>
			</div>
		</div>
		<p>
		<div class="row" style="margin-bottom:10px;">
			<div class="col-sm-12 text-center">
				<div class="label label-success text-white pageLabel" style="cursor: default; margin-top:10px;">Remote Keyer Settings</div>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text keyer-group-addon">Keyer Fn</span>
					</div>
					<input type="text" class="form-control disable-text" readonly="readonly" id="curFunction" aria-lable="func" aria-describedby="func-addon">
				    <span class="input-group-btn">
			            <div class="dropdown">
						    <button class="btn btn-primary dropdown-toggle" data-index="23" id="functionSel" data-size="3" type="button"  title="Select Remote Mode" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
						    </button>
						    <ul class="dropdown-menu dropdown-menu-right menu-scroll" data-index="24" id="functionList">
								<div class='myFunction' id='iamA'><li><a class='dropdown-item' href='#'>Normal</a></li></div>
								<div class='myFunction' id='iamB'><li><a class='dropdown-item' href='#'>Radio Keyer</a></li></div>
								<div class='myFunction' id='ulti'><li><a class='dropdown-item' href='#'>Remote Keyer</a></li></div>
						     </ul>
			            </div>
				    </span>
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group" id="portValueAll">
					<div class="input-group-prepend">
						<span class="input-group-text keyer-group-addon" >Port</span>
					</div>
					<input type="text" class="form-control" placeholder="Keyer data port (30040)" onfocus="this.select();" data-index="25" id="portValue" aria-lable="port" aria-describedby="port-addon">
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group" id="IPValueAll">
					<div class="input-group-prepend">
						<span class="input-group-text keyer-group-addon">IP</span>
					</div>
					<input type="text" class="form-control" placeholder="Radio RigPi IP" onfocus="this.select();" data-index="26" id="IPValue" aria-lable="IP" aria-describedby="IP-addon">
				</div>
			</div>
		</div>
		<div class="row" style="margin-bottom:10px;">
			<div class="col-sm-4 text-center">
				<div class="label label-success text-white-small pageLabel" id="set-text" style="cursor: default;  margin-top:10px;">Set Paddle Mode (Paddle Md) to Vibrobug for Radio Keyer</div>
			</div>
			<div class="col-sm-4 text-center text-spacer">
			 	<div class="form-check form-check-inline" id="doInvert">
			 	<label class="form-check-label  text-white text-center">
			 	<input type="checkbox" data-index="26" id="invertKeying" class="form-check-input">
			 		Invert Keying (only for direct keying)
			 	</input>
			 	</label>
			 	</div>
			</div>
			<div class="col-sm-4 text-center">
			</div>
		</div>
	</div>
    <?php require($dRoot.'/includes/footer.php'); ?>
    <?php require($dRoot.'/includes/modal.txt'); ?>
<script src="./Bootstrap/popper.min.js"</script>
<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
<script src="./Bootstrap/jquery-ui.js"></script>
<script src="./Bootstrap/bootstrap.min.js"></script>
<script src="js/nav-active.js"></script>
</body>
</html>
