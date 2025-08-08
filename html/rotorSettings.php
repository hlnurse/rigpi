<?php
session_start();
$tUserName=$_SESSION['myUsername'];
$tCall=$_SESSION['myCall'];
$dRoot = "/var/www/html";
require_once $dRoot . "/classes/Membership.php";
require_once $dRoot . "/classes/Membership.php";
$membership = new Membership();
$membership->confirm_Member($tUserName);
?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo $tCall; ?> RigPi Rotor Settings</title>
	<meta name="RigPi Settings" content="">
	<meta name="author" content="Howard Nurse">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">
    <script src="/Bootstrap/jquery.min.js" ></script>
	<script defer src="./awe/js/all.js" ></script>
	<link href="./awe/css/all.css" rel="stylesheet">
	<link href="./awe/css/fontawesome.css" rel="stylesheet">
	<link href="./awe/css/solid.css" rel="stylesheet">	
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
							  $('#myModal').modal({show:true});
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

			$.post('/programs/GetSelectedRadio.php', {un:tUserName}, function(response) {
				tMyRadio=response;
				tMyRotorPort=tMyRadio*2+4531;
				$("#curID").val(response);
				getMyRotorFields();
		        $.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
	        	{
					$('#searchText').val(response);
			    });
			});

			$(document).on('click', '.rotorSave', function() {
				setMyRotorFields();
			});

			$(document).on('click', '.manufacturers', function() {
				var text = $(this).text();
  				$('#curManu').val(text);
  				$.post('/programs/RotorDB.php', 'm='+text, function(response) {
			        $('#rotorList').empty(); //remove all child nodes
			        var newOption = response;
			        $('#rotorList').append(newOption);
  				});
			});

			$(document).on('click', '.rotors', function() {
				var text = $(this).text();
  				$('#curRotor').val(text);
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

			$(document).on('click', '.mybaud', function() {
				var text = $(this).text();
  				$("#curBaud").val(text);
			});
			$(document).on('click', '.mystop', function() {
				var text = $(this).text();
  				$("#curStop").val(text);
  			});
			$(document).on('click', '.myport', function() {
				var text = $(this).text();
  				$("#curPort").val(text);
			});

			$(document).on('click', '#connectButton', function() {
				setMyRotorFields();
				//need to add baud, etc, not to mention rotordo.php
  				$.post('/RotorDo.php', {doWhat: '', id: tRotorID, port: tMyRotorPort}, function(response) {
					$("#modalA-body").html(response);			  				
					$("#modalA-title").html("Rotor Connected");
				  	$("#myModalAlert").modal({show:true});//			  				alert(response);
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
  			function setRotorList(){
				$.post('/programs/RotorDB.php', 'm='+cManu, function(response) {
			        $('#rotorList').empty(); //remove all child nodes
			        var newOption = response;
			        $('#rotorList').append(newOption);
				});
  			}

  			function getMyRotorFields(){
				$.get('/programs/GetMyRadio.php', 'f=RotorManufacturer&r='+tMyRadio, function(response) {
		  			$('#curManu').val(response);
		  			cManu=response;
		  			setRotorList();
	  			});
				$.get('/programs/GetMyRadio.php', 'f=RotorModel&r='+tMyRadio, function(response) {
		  			$('#curRotor').val(response);
	  			});
				$.get('/programs/GetMyRadio.php', 'f=Rotor&r='+tMyRadio, function(response) {
		  			$('#curID').val(response);
	  			});
				$.get('/programs/GetMyRadio.php', 'f=RotorPort&r='+tMyRadio, function(response) {
		  			$('#curPort').val(response);
	  			});
				$.get('/programs/GetMyRadio.php', 'f=RotorBaud&r='+tMyRadio, function(response) {
		  			$('#curBaud').val(response);
	  			});
				$.get('/programs/GetMyRadio.php', 'f=RotorStop&r='+tMyRadio, function(response) {
		  			$('#curStop').val(response);
	  			});
  			}

		   function setMyRotorFields() {
			   var rotor;
				$.post('/programs/RotorID.php', 'ro=' + $('#curRotor').val(), function(response) {
		  			rotorID=response;
		  			var manuID=$('#curManu').val();
				  	$.post("/programs/SetMyRotor.php", {rx: tMyRadio, rm: $("#curManu").val(), ro: $("#curRotor").val(), ru: $("#curBaud").val(), rb: "8", rp: $("#curPort").val(), ra: "0", rs: $("#curStop").val(),rd: rotorID},function(response){
							$("#modalA-body").html(response);			  				
							$("#modalA-title").html("Rotor Settings");
						  	$("#myModalAlert").modal({show:true});//			  				alert(response);
				  	});
				});
	  		};



			$.getScript("/js/modalLoad.js");

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
	</script>
	<?php require $dRoot . "/programs/ManufacturersDB.php"; ?>
</head>

<body class="body-black-scroll" >
	<?php require $dRoot . "/includes/header.php"; ?>
	<div class="container-fluid">
		<div class="row"  style="margin-bottom:10px;">
			<div class="col-md-4 text-center">
			<!--NOTE better to use .offset here, but doesn't seem to work in Safari-->
			</div>
			<div class="col-md-4 text-center">
				<span class="label label-success text-white" style="cursor: default; margin-top:10px;">Rotor Settings (User: <?php echo $tUserName; ?>)</span>
			</div>
			<div class="col-md-4 text-center btn-padding">
				<button class='btn btn-color rotorSave' type='button'>
					<i class="fas fa-cloud-upload-alt fa-lg"></i>
				</button>
			</div>
		</div>
		<hr>
		<div class="row">
			<div class="col text-spacer text-center">
				<span class="label label-success text-white" style="cursor: default; margin-top:10px;">Note: If radio is connected now, please reconnect after you make any rotor settings changes.</span>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Man</span>
					</div>
					<input type="text" class="form-control disable-text" id="curManu" readonly="readonly"  title="Selected Radio Manufacturer" aria-lable="manufacturer" aria-describedby="manufacturer-addon">
				    <span class="input-group-btn">
			            <div class="dropdown">
						    <button class="btn btn-primary dropdown-toggle" id="manuSel" data-size="3" type="button" title="Manufacturer List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
						    </button>
						    <ul class="dropdown-menu dropdown-menu-right menu-scroll" id="manufacturerList">
								<?php echo getRotorManufacturers(); ?>
						     </ul>
			            </div>
				    </span>
				</div>
			</div>
			<div class="col-md-4 text-spacer ">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Rotor</span>
					</div>
					<input type="text" class="form-control disable-text" id="curRotor" readonly="readonly"  title="Selected Rotor" aria-lable="rotor" aria-describedby="rotor-addon">
				    <span class="input-group-btn">
			            <div class="dropdown">
						    <button class="btn btn-primary dropdown-toggle" id="rotorSel" data-size="3" type="button"  title="Rotor List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
						    </button>
						    <ul class="dropdown-menu dropdown-menu-right menu-scroll" id="rotorList">
						     </ul>
			            </div>
				    </span>
				</div>
			</div>
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Port</span>
					</div>
					<input type="text" class="form-control" style="cursor: text" id="curPort"  title="Radio Port" aria-lable="port" aria-describedby="port-addon">
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
					<input type="text" class="form-control disable-text" id="curBaud" readonly="readonly"  title="Baud Rate" aria-lable="baud" aria-describedby="baud-addon">
				    <span class="input-group-btn">
			            <div class="dropdown">
						    <button class="btn btn-primary dropdown-toggle" id="radioSel" data-size="3" type="button"  title="Comm Speed List" data-toggle="dropdown">
						    	<i class="fas fa-list-alt fa-lg"></i>
						    </button>
							<div class="dropdown-menu dropdown-menu-right" id='baudList' aria-labelledby="baudSelectButton">
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
					<input type="text" class="form-control disable-text" readonly="readonly"  title="Number of Stop Bits" id="curStop" aria-lable="stop" aria-describedby="stop-addon">
				    <span class="input-group-btn">
			            <div class="dropdown">
						    <button class="btn btn-primary dropdown-toggle" id="stopSel" data-size="3" type="button"  title="Stop Bits List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
						    </button>
							<div class="dropdown-menu dropdown-menu-right" id='stopList' aria-labelledby="stopSelectButton">
								<div class='mystop' id='1'><li><a class='dropdown-item' href='#'>1</a></li></div>
								<div class='mystop' id='2'><li><a class='dropdown-item' href='#'>2</a></li></div>
							</div>
			            </div>
				    </span>
				</div>
			</div>
			<div class="col-md-4 text-spacer">
			</div>
		</div>
		</div>
	</div>
    <?php require $dRoot . "/includes/footer.php"; ?>
    <?php require $dRoot . "/includes/modalAlert.txt"; ?>
    <?php require $dRoot . "/includes/modal.txt"; ?>
<?php require $dRoot . "/includes/modalCancelOnly.txt"; ?>
<script src="./Bootstrap/popper.min.js"</script>
<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
<script src="./Bootstrap/jquery-ui.js"></script>
<script src="./Bootstrap/bootstrap.min.js"></script>
<script src="js/nav-active.js"></script>
</body>
</html>
