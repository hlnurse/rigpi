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
if (!isset($GLOBALS["htmlPath"])) {
  $GLOBALS["htmlPath"] = $_SERVER["DOCUMENT_ROOT"];
}
if (!empty($_SESSION["firstuse"])) {
  $firstUse = $_SESSION["firstUse"];
} else {
  $firstUse = 1;
}
//$firstUse = 1;
$_SESSION["firstUse"] = 0;
if ($firstUse == 1) {
  $tColsExec = "sudo /usr/share/rigpi/col.sh";
  $cols = exec($tColsExec);
}
$dRoot = $GLOBALS["htmlPath"];
$wanIP = "http://rigpi.dyndns.org:8081";
require $dRoot . "/programs/GetMyRadioFunc.php";
if (!empty($_GET["c"]) && !empty($_GET["x"])) {
  $tCall = strtoupper($_GET["c"]);
  $tUserName = $_GET["x"];
} else {
  $tCall = "";
  $tUserName = "";
}
require_once $dRoot . "/classes/Membership.php";
$membership = new Membership();
if (!$membership->confirm_Member($tUserName)) {
  exit();
}
require_once $dRoot . "/programs/GetUserFieldFunc.php";
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
}
$db->where("IsAlive", "1");
$rows = $db->get("RadioInterface");
$online = $db->count;
?>
	
<!--This is the Tuner (PTT only) window -->
	
	<!DOCTYPE html>
	<html lang="en">
		<head>
			<meta charset="utf-8">
			<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
			<title><?php echo $tCall; ?> RigPi PTT</title>
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
//				var pknob;
//				var ppanel;
//				var pmeter;
//				var lowerText;
//				var pled;
//				var swiper;
//				var knobOld;
//				var plus;
//				var ptt;
//				var keypad;
				var tBusy;
				var spacePTT=0;
				var tPTT, tPTTIsOn=0;
//				var tKnobPTT=0;
//				var tSwipeOld=0;
				var xmit=false;
				var trXmit=false;
//				var tuningIncrement=100;
				var windowLoaded=false;
				var tUpdate;
//				var mp1;
//				var mp100;
				var waitRefresh=4;
				var tMyRadio='1';
				var tMyRadioReal='1';
				var tCWPort="/dev/ttyS0";
				var tMyRotorPort="";
				var tUserName="<?php echo $tUserName; ?>";
				var tAccessLevel="<?php echo $level; ?>";
//				var tFirstUse="<?php echo $firstUse; ?>";
				var tMyCall="<?php echo $tCall; ?>";
				var tOnline="<?php echo $online; ?>";
				var tUser='';
				var tSplitOn=0;
//				var tTuneFromTap=0;
				var classTimer;
				var trOn=0; //T/R macro
//				var tLine1="";
//				var tLine2="";
//				var stLine1="";
//				var stLine2="";
//				var curDigit=0;
//				var curSubDigit=0;
				var tMyKeyer='';
				var tMain=0;
				var tSub=0;
				var tMode=0;
				var tNoRadio=true;
//				var tOverPanel=false;
//				var tMouseDelta=0;
//				var tIgnoreRepeating=false;
				var tMyPTT=1;
				var tAliveCount=0;
//				var tMeterCal=1;
//				var ld='ld2';
//				var lu=2;
//				var slider1='';
//				var band1='';
//				var band2='';
				var alreadyDone=0;
//				var tRadioMem="";
				var tInternet=1;
//				var tNoReboot=0;
//				var speedPot=0;
//				var tSplitDisabled=0;
				var notGotPerfectWidgets=1;
//				var tMyKeyerFunction="0";
//				var tMyKeyerPort="0";
//				var tMyKeyerIP="0";
				var ptt;
				var ptt1;
//				var lock;
//				var lockOn, tTuneOn=0;
//				var crx;
				var tTrx=0;
//				var lastSpaceTimer=0;
				var tRadioName="";
				var tRadioModel="";
				var tRadioPort=4532;
//				var si;
				var tDisconnected=1;
//				var tMyRadioCW;
//				var tMyCWPort;
//				var tButtonWait=0;
//				var tMyRotorRadio; //ve9gj
//				var led1,led2,led3,led4,led5,led6,led7,led8;
//				var mBank=1;
//				var showVideo=2;
//				var jsonPanel='';
//				var jsonSMeter='';
				var wanIP;
//				var tBand=20;
//				var tBandMHz="14MHz";
//				var tKnobLock=0;
//				var mtrLabel="S-Meter";
				$(document).ready(function()
				{
					btnLatchColor="btn-warning";
					updateFreqDisp()

					$.post('./programs/GetSelectedRadio.php', {un:tUserName}, function(response) 
					{
						$.get('/programs/GetMyRadio.php', 'f=Port&r='+response, function(response1) {
							tMyRadio=response;
							tMyRadioReal=response;
							  var tMyRadioPort=response1;
							  if (tMyRadioPort>4530 && tMyRadioPort<5000){
								  tMyRadioReal=1+(tMyRadioPort-4532)/2;
							  }
						});
						
			//Main buttons
								
								
						});

						 ///end ready
							$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'pttLatch', table: 'MySettings'}, function(response)
							{
								tPTTLatch=response;
							});
							$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'RadioName', table: 'MySettings'}, function(response)
							{
								tRadioName=response;
							});
							
							tRadioModel="NET rigctl";
							
							$.get('/programs/GetMyRadio.php', 'f=Port&r='+tMyRadio, function(response1) {
								  var tMyRadioPort=response1;
								  if (tMyRadioPort>4530 && tMyRadioPort<5000){
									  tMyRadioPort=1+(tMyRadioPort-4532)/2;
									  tMyRadioCW=tMyRadioPort;
								  }else{
									  tMyRadioCW=tMyRadio;
								  }
								$.post('./programs/GetSetting.php',{radio: tMyRadioCW, field: 'Port', table: 'MySettings'}, function(response)
								{
									tRadioPort=response;
								});
							  });
							  tUpdate = setInterval(updateTimer,500);
							
							$.post('./programs/GetSetting.php',{radio: tMyRadioReal, field: 'PTTMode', table: 'MySettings'}, function(response)
							{
								tMyPTT=response;
							});
	
					var jsonKnob='';
					var jsonLED='';
			});	
	
			function disconnect() 
			{
	  			tDisconnected=1;
 	  			$.post('./programs/disconnectRadio.php', {radio: tMyRadio, user: tUserName, rotor: ""}, function(response) {
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

			function setPTT(state, pttBypass){
				if (notGotPerfectWidgets==0 && tAccessLevel < 4 || tAccessLevel==10){
					var ttPTT=tMyRadioReal;
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
							ttPTT=tMyRadioReal;
						}
					}
					waitRefresh=4;
					if (state==1  && tPTTIsOn==0){
	                    spacePTT=0;
						trXmit=true;
						if (pttBypass==0){
							$.post("/programs/SetSettings.php", {field: "PTTOut", radio: ttPTT, data: "1", table: "RadioInterface"});			
						}
	
						if (tMyPTT==1){
							$.post('/programs/doGPIOPTT.php', {PTTControl: "on"});
						}
						$("#ptt").removeClass("PTTButton");
						$("#ptt").addClass("PTTButton-red");
					}else{
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
					}
				}
			}
			
			var aMCommands=[];
			//processCommand ties into /includes/buildMacros.php
			
			function connectRadio(){
				tDisconnected=1;
	  			$.post('./programs/hamlibDo_PTT.php', {test: 0, keyer: tMyKeyer, radio: tMyRadio, user: tUserName, port: tCWPort, tcpPort: "30001", rotorPort: "", keyerPort:"", keyerIP:"", keyerFunc:""}, function(response) {
		  			if (tDisconnected==1){
			  			return;
		  			}
				});
			}
			
			function updateFreqDisp()
			{
				$.post('./programs/GetSelectedRadio.php', {un:tUserName}, function(response) 
				{
					tMyRadio=response;
					$("#fPanel1").text("Connecting...");
					$("#fPanel2").text("");
					var now = new Date();
					var now_hours=now.getUTCHours();
					now_hours=("00" + now_hours).slice(-2);
					var now_minutes=now.getUTCMinutes();
					now_minutes=("00" + now_minutes).slice(-2);
					$("#fPanel5").text(now_hours+":"+now_minutes+'z');
					$("#fPanel4").text("User: "+tMyCall+" (" +tUserName+")");
				}
			)};
	
			function updateTimer()
			{
				if (autoConnect==true ){
					disconnect();
					autoConnect=false;
					console.log("running connect");
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
					var tAlive=tAData[8];  //to watch for no connection to radio
					tTrx=tAData[9];
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
				});
			}else{
				$.post('/programs/GetInterfaceIn_PTT.php',{radio: tMyRadio, un: tUserName, myCall:tMyCall }, function(response) 
				{
					var tAData=response.split('`');
					tTrx=tAData[9];
				});
				if (waitRefresh==0){
					$("#ptt").removeClass("PTTButton");
					$("#ptt").addClass("PTTButton-red");
				}	
		};
		var now = new Date();
		var now_hours=now.getUTCHours();
		now_hours=("00" + now_hours).slice(-2);
		var now_minutes=now.getUTCMinutes();
		now_minutes=("00" + now_minutes).slice(-2);
		$("#fPanel5").text(now_hours+":"+now_minutes+'z');
	};
	
	$(document).on('click', '#ptt', function() 
	{
		if (tDisconnected==1){
			return;
		}
		if (xmit==true){
			xmit=false;
			$("#ptt").removeClass("PTTButton-red");
			$("#ptt").addClass("PTTButton");
			setPTT(0,0);
			$("#ptt").selected=false;
		}else{
			xmit=true;
			$("#ptt").removeClass("PTTButton");
			$("#ptt").addClass("PTTButton-red");
			setPTT(1,0);
			tKnobPTT=1;
			$("#ptt").selected=false;
		};
	});	

	$.getScript("/js/addPeriods.js");
	
	</script>
	
	</head>
	<body class="body-black" id="tuner">
		 <div class="container-fluid">
			<p>
			<div class="row style="height: 300px">
				<div class="col-2 col-sm-4"></div>
				<div class="col-8 col-sm-4" id="colKnob">
					<button class='btn btn-color block PTTButton' data-toggle="button" aria-pressed="true" id='ptt' type='button'>
						<span style="font-size:30px; background: rgba(0,0,0,0); color: gray">RigPi</span><br><span style="font-size:40px; background: rgba(0,0,0,0); color: #ffffff;">PTT</span>
					</button>
				</div>
				<div class="col-2 col-sm-4"></div>
			</div>
			 <div class="status">
				<?php require $dRoot . "/includes/footer.php"; ?>
			</div>
		</div>
	</body>
	
	<script src="/js/mscorlib.js" type="text/javascript"></script> 
	<script src="/Bootstrap/jquery-ui.js"></script>
	<script src="/js/jquery.ui.touch-punch.min.js"></script>   
	<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
	<script src="./Bootstrap/jquery-ui.js"></script>
	<script src="./Bootstrap/bootstrap.min.js"></script>
	<script src="/js/nav-active.js"></script>
</html>
	