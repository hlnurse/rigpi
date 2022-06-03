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
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo $tCall; ?> RigPi Default Settings</title>
	<meta name="RigPi Default Settings" content="">
	<meta name="author" content="Howard Nurse">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="/Bootstrap/bootstrap.min.css">
	 <script src="/Bootstrap/jquery.min.js" ></script>
<!--
<script src="/js/jogDial.min.js"></script>
<script src="/js/gauge.min.js"></script>
-->
	<link rel="shortcut icon" href="./favicon.ico">
	<link rel="apple-touch-icon" href="./favicon.ico">
	<?php require $dRoot . "/includes/styles.php"; ?>
	<link href="./awe/css/all.css" rel="stylesheet">
	<link href="./awe/css/fontawesome.css" rel="stylesheet">
	<link href="./awe/css/solid.css" rel="stylesheet">	
	<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">

	<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="apple-touch-icon" href="/apple-touch-icon.png">
	<?php require $dRoot . "/includes/styles.php"; ?>
	<script>
		var tManu='';
		var tRotorID='1';
		var tMyRadio='1';
		var tMyRotorPort=4531;
		var tMyCall="<?php echo $tCall; ?>";
		var tCall=tMyCall;
        var tUserName=<?php echo "'" . $tUserName . "'"; ?>;
        var tUser='';
		var bEnable=[],bName;
		var outputAF, sliderAF, outputPwrOut, sliderPwrOut, sliderMic, outputMic, outputRF, sliderRF, tVal;
		var sliderAFRef, sliderRFRef, sliderPwrOutRef, sliderMicRef, sliderHandle
		var Aon,Bon,Con,Don,Eon,Ron
  		$(document).ready(function(){
			  Aon=0;Bon=0;Con=0;Don=0;Eon=0;Ron=0;
			outputAF = document.getElementById("myAFVal");
			sliderAF = document.getElementById("sliderAF");
			outputRF = document.getElementById("myRFVal");
			sliderRF = document.getElementById("sliderRF");
			outputPwrOut = document.getElementById("myOutputPwrVal");
			sliderPwrOut = document.getElementById("sliderPwrOut");
			sliderHandle="";
			outputMic = document.getElementById("myMicVal");
			sliderMic = document.getElementById("sliderMic");

			$.post('/programs/GetSelectedRadio.php', {un:tUserName}, function(response) {
				tMyRadio=response;
				tMyRotorPort=tMyRadio*2+4531;
				$("#curID").val(response);
		        $.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
	        	{
					$('#searchText').val(response);
			    });
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'RFGain', table: 'RadioInterface'}, function(response){
					$( function() {
						$("#sliderRF").slider({
							min: 0,
							max: 100,
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
					$.post("/programs/SetSettings.php", {field: "RFGain", radio: tMyRadio, data: tVal, table: "RadioInterface"}, function(response){
					});
				});
				
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'AFGain', table: 'RadioInterface'}, function(response){
					$( function() {
						$("#sliderAF").slider({
							min: 0,
							max: 100,
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
				});
				
				$("#sliderAF").on("slide",function(event,ui){
					waitRefresh=8;
					tVal=$("#sliderAF").slider('value');
					var tV=tVal;
					if (tV>0 && tV<100) tV=tV-1;
					outputAF.innerHTML =tV;
					sliderHandle=ui.handle;
					$.post("/programs/SetSettings.php", {field: "AFGain", radio: tMyRadio, data: tVal, table: "RadioInterface"}, function(response){
					});
				});
				
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'PwrOut', table: 'RadioInterface'}, function(response){
					$( function() {
						$("#sliderPwrOut").slider({
							min: 0,
							max: 100,
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
					$.post("/programs/SetSettings.php", {field: "PwrOut", radio: tMyRadio, data: tVal, table: "RadioInterface"}, function(response){
					});
				});
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'MicLvl', table: 'RadioInterface'}, function(response){
					$( function() {
						$("#sliderMic").slider({
							min: 0,
							max: 100,
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
					$.post("/programs/SetSettings.php", {field: "MicLvl", radio: tMyRadio, data: tVal, table: "RadioInterface"}, function(response){
					});
				});
				
				$.post("/programs/GetUserField.php", {un:tUserName, field:'BandEnable'}, function(response)
				{
					bEnable=response.split(",");
					updateButtons();
				});
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
		
$(document).on('click', '#RButton', function() 
 {
var on=(Ron == 1 ? 0 :1);
	 Ron=on;
doEnableBF(0, '#AButton', on);
doEnableBF(1, '#AButton', on);
doEnableBF(2, '#AButton', on);
doEnableBF(3, '#AButton', on);
doEnableBF(4, '#AButton', on);
doEnableBF(5, '#AButton', on);
doEnableBF(6, '#AButton', on);
doEnableBF(7, '#AButton', on);
doEnableBF(8, '#AButton', on);
doEnableBF(9, '#AButton', on);
doEnableBF(10, '#AButton', on);
doEnableBF(11, '#AButton', on);
	 updateButtons();
var bS=bEnable.join();
$.post("/programs/SetUserFieldByName.php", {field: "BandEnable", username: tUserName, data: bS, table: "Users"}, function(response){
	});
 });	
 
$(document).on('click', '#AButton', function() 
 {
var on=(Aon == 1 ? 0 :1);
 Aon=on;
doEnableBF(0, '#AButton', on);
doEnableBF(1, '#AButton', on);
doEnableBF(2, '#AButton', on);
doEnableBF(3, '#AButton', on);
doEnableBF(4, '#AButton', on);
doEnableBF(5, '#AButton', on);
doEnableBF(6, '#AButton', on);
doEnableBF(7, '#AButton', on);
doEnableBF(8, '#AButton', on);
doEnableBF(9, '#AButton', on);
	 updateButtons();
var bS=bEnable.join();
$.post("/programs/SetUserFieldByName.php", {field: "BandEnable", username: tUserName, data: bS, table: "Users"}, function(response){
	});
 });	
 
$(document).on('click', '#BButton', function() 
 {
var on=(Bon == 1 ? 0 :1);
 Bon=on;
	 doEnableBF(4, '#BButton', on);
	doEnableBF(6, '#BButton', on);
	doEnableBF(8, '#BButton', on);
	 updateButtons();
 var bS=bEnable.join();
 $.post("/programs/SetUserFieldByName.php", {field: "BandEnable", username: tUserName, data: bS, table: "Users"}, function(response){
	 });
 });	
 
$(document).on('click', '#CButton', function() 
 {
var on=(Con == 1 ? 0 :1);
 Con=on;
doEnableBF(0, '#CButton', on);
doEnableBF(1, '#CButton', on);
doEnableBF(2, '#CButton', on);
doEnableBF(3, '#CButton', on);
	 updateButtons();
var bS=bEnable.join();
$.post("/programs/SetUserFieldByName.php", {field: "BandEnable", username: tUserName, data: bS, table: "Users"}, function(response){
	});
 });	
 
$(document).on('click', '#DButton', function() 
 {
var on=(Don == 1 ? 0 :1);
 Don=on;
doEnableBF(4, '#DButton', on);
doEnableBF(5, '#DButton', on);
doEnableBF(6, '#DButton', on);
doEnableBF(7, '#DButton', on);
doEnableBF(8, '#DButton', on);
doEnableBF(9, '#DButton', on);
updateButtons();
var bS=bEnable.join();
$.post("/programs/SetUserFieldByName.php", {field: "BandEnable", username: tUserName, data: bS, table: "Users"}, function(response){
	});
 });	
 
$(document).on('click', '#EButton', function() 
 {
var on=(Eon == 1 ? 0 :1);
 Eon=on;
doEnableBF(10, '#EButton', on);
doEnableBF(11, '#EButton', on);
updateButtons();
var bS=bEnable.join();
$.post("/programs/SetUserFieldByName.php", {field: "BandEnable", username: tUserName, data: bS, table: "Users"}, function(response){
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
			    window.open("/login.php","_self");
			    form.submit();
			};                

			$.getScript("/js/modalLoad.js");

	  	});

		var tUpdate = setInterval(updateTimer,1000);
		function updateTimer(){
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
	  	}

$(document).on('click', '.bBtn0', function() 
{
	doEnable(0,"#"+this.id);
});

$(document).on('click', '.bBtn1', function() 
{
	doEnable(1,"#"+this.id);
});

$(document).on('click', '.bBtn2', function() 
{
	doEnable(2,"#"+this.id);
});

$(document).on('click', '.bBtn3', function() 
{
	doEnable(3,"#"+this.id);
});

$(document).on('click', '.bBtn4', function() 
{
	doEnable(4,"#"+this.id);
});

$(document).on('click', '.bBtn5', function() 
{
	doEnable(5,"#"+this.id);
});

$(document).on('click', '.bBtn6', function() 
{
	doEnable(6,"#"+this.id);
});

$(document).on('click', '.bBtn7', function() 
{
	doEnable(7,"#"+this.id);
});

$(document).on('click', '.bBtn8', function() 
{
	doEnable(8,"#"+this.id);
});

$(document).on('click', '.bBtn9', function() 
{
	doEnable(9,"#"+this.id);
});

$(document).on('click', '.bBtn10', function() 
{
	doEnable(10,"#"+this.id);
});

$(document).on('click', '.bBtn11', function() 
{
	doEnable(11,"#"+this.id);
});


		function doEnable(which, name){
			
			if (bEnable[which]=="1"){
				$(name).removeClass("btn-success");
				$(name).addClass("btn-secondary");
				bEnable[which]="0";
				var bS=bEnable.join();
				$.post("/programs/SetUserFieldByName.php", {field: "BandEnable", username: tUserName, data: bS, table: "Users"}, function(response){
					});
			}else{
				$(name).removeClass("btn-secondary");
				$(name).addClass("btn-success");
				bEnable[which]="1";
				var bS=bEnable.join();
				$.post("/programs/SetUserFieldByName.php", {field: "BandEnable", username: tUserName, data: bS, table: "Users"}, function(response){
					});
			};
		};
		
function doEnableBF(which, name, onOff){
		bEnable[which]=onOff;
};

			

	  	$.getScript("/js/addPeriods.js");

	</script>
</head>

<body class="body-black" >
	<?php require $dRoot . "/includes/header.php"; ?>
	<div class="container-fluid">
		<div class="row"  style="margin-bottom:10px;">
		</div>
		<div class="row" style="margin-left:20px;margin-right:20px">
			<?php require $dRoot . "/includes/bands.php"; ?>
		</div>
	</div>
    <?php require $dRoot . "/includes/footer.php"; ?>
    <?php require $dRoot . "/includes/modalAlert.txt"; ?>
    <?php require $dRoot . "/includes/modal.txt"; ?>
<script src="/js/mscorlib.js" type="text/javascript"></script> 
<script src="/js/PerfectWidgets.js" type="text/javascript"></script>
<script src="/Bootstrap/jquery-ui.js"></script>
<script src="/js/jquery.ui.touch-punch.min.js"></script>   
<script src="/Bootstrap/bootstrap.min.js"></script>
<script src="/js/nav-active.js"></script>
<script src="/Bootstrap/popper.min.js"></script>
</body>
</html>
