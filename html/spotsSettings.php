<?php
session_start();
$tUserName=$_SESSION['myUsername'];
$tCall=$_SESSION['myCall'];
$dRoot = "/var/www/html";
require_once $dRoot . "/classes/Membership.php";
$membership = new Membership();
$membership->confirm_Member($tUserName);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
	<head>
		<meta charset="utf-8">

		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title><?php echo $tCall; ?> RigPi Spots Settings</title>
		<meta name="description" content="">
		<meta name="author" content="Howard Nurse">

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<!-- Bootstrap CSS -->
		<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
		<link rel="shortcut icon" href="/favicon.ico">
		<link rel="apple-touch-icon" href="/favicon.ico">
		<link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">
		<script src="/Bootstrap/jquery.min.js" ></script>
		<script defer src="./awe/js/all.js" ></script>
		<link href="./awe/css/all.css" rel="stylesheet">
		<link href="./awe/css/fontawesome.css" rel="stylesheet">
		<link href="./awe/css/solid.css" rel="stylesheet">	
		<?php
  require $dRoot . "/includes/styles.php";
  require_once $dRoot . "/programs/GetSettingsFunc.php";
  ?>
		 <script type="text/javascript">
			   var tMyCall="<?php echo $tCall; ?>";
			   var tCall=tMyCall
			   var tMyRadio='';
			var tUserName=<?php echo "'" . $tUserName . "'"; ?>;
			var tUser='';
			var tSelectedRow='';
			var tFilter='', cTable;
			var tNoInternet=0;
			$(document).ready(function() {
						
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

				window.pageCount=1;
				$.post('/programs/GetSelectedRadio.php', {un:tUserName}, function(response) 
				{
					tMyRadio=response;
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'ClusterConnected', table: 'MySettings'}, function(response){
						if (response==1){
							$("#connectButton").addClass('btn-small-success-on');
						}else{
							$("#connectButton").addClass('btn-small-lock-on');
						};
					});

				$(document).keydown(function(e){
				var t=e.key;
				e.multiple
				var w=e.which;
				cTable = document.getElementById('clusterTable');
								  const firstRow = document.querySelector("#clusterTable tr");
								  const rowHeight = firstRow.offsetHeight;
				if (e.key === "ArrowDown") {
					cTable.scrollTop += rowHeight;
					e.preventDefault();
				  } else if (e.key === "ArrowUp") {
					cTable.scrollTop -= rowHeight;
					e.preventDefault();
				  } else if (e.key === "PageDown") {
					  cTable.scrollTop += cTable.clientHeight;
					  e.preventDefault();
					} else if (e.key === "PageUp") {
					  cTable.scrollTop -= cTable.clientHeight;
					  e.preventDefault();
					}
		if (e.ctrlKey){
			scrollLock=0;
			if (t=="Home" || t=='h'){  //lock to top
				scrollLock=2;
			}
			if (t=="End" || t=='e'){	//lock to bottom
				scrollLock=1;
			}
			if (t=="Delete" || t=='y'){  //lock to yellow
				scrollLock=4;
			}
			if (t=="Help" || t=='r'){ //lock to red
				scrollLock=0;
				if (tSelectedRow){
						clusterTable.scrollTop = tSelectedRow.offsetTop - (clusterTable.clientHeight / 2) + (tSelectedRow.clientHeight / 2);
					  	$(tSelectedRow).addClass('highlight').siblings().removeClass('highlight');
					}
				}
			scrollLockOld=scrollLock;
			if (scrollLock==1){
				cTable.scrollTop = cTable.scrollHeight;
			}
			if (scrollLock==2){
				cTable.scrollTop = 0;
			}
			if (tSelectedRow){
				clusterTable.scrollTop = tSelectedRow.offsetTop - (clusterTable.clientHeight / 2) + (tSelectedRow.clientHeight / 2);
				  $(tSelectedRow).addClass('highlight').siblings().removeClass('highlight');
			}
		}


			  if (scrollLock==1){
				  cTable.focus();
				  cTable.scrollTop = cTable.scrollHeight;
				  const rows = document.querySelectorAll("#logt tr");
				  const endIndex = Math.floor(rows.length);
//				  const endRow =getEndVisibleRow();
//						  rowID = endRow.getAttribute("id");
//				  rowdx = endRow.getAttribute("call");
//				  $('#searchText').val(rowdx);

			  }
			  if (scrollLock==2){
					cTable.focus();
					cTable.scrollTop = 0;
				}
				cTable.focus();

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
	
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
					{
						$('#searchText').val(response);
					});

					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'RetainTime', table: 'MySettings'}, function(response)
					{
						  $('#curRet').val(response+' Minutes');
					});

					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'ClusterFilter', table: 'MySettings'}, function(response)
					{
						  tFilter=response;;
						  getClusters(tFilter);
					});

					$(document).on('click', '#connectButton', function() {
						if (tNoInternet==1){
							$("#modalA-body").html("<br>RSS cannot reach the Internet so can't connect to the Telnet site for spots.<br><br>");			  				
							$("#modalA-title").html("No Internet");
							  $("#myModalAlert").modal({show:true});
						}else{
							if (tMyCall.toLowerCase()=='admin'){
								$("#modalA-body").html("Please enter your call in SETTINGS>Accounts>Account Editor. Spots NOT started.");			  				
								$("#modalA-title").html("Spots Status");
								  $("#myModalAlert").modal({show:true});
							}else{
								  $.post('./programs/SpotsStart.php', {action: 'start', radio: tMyRadio, call: tMyCall}, function(response) {
										if (response.indexOf("error")>0){
											$.post("/programs/SetSettings.php", {field: "ClusterConnected", radio: tMyRadio, data: 0, table: "MySettings"});
										  $("#connectButton").addClass('btn-small-lock-on');
										  $("#modalA-body").html("Connection failed.  Try Another spot server.");	
										  $("#modalA-title").html("Spots Status");
										  $("#myModalAlert").modal({show:true});
									  }else{
										  $("#connectButton").addClass('btn-small-success-on');
										  $("#modalA-body").html("Connection OK.");	
										  $("#modalA-title").html("Spots Status");
										  $("#myModalAlert").modal({show:true});
								  
									  };
								  });
							};
						};
					 });	
	
					$(document).on('click', '#disconnectButton', function() {
						   $.post('/programs/SpotsStart.php', {action: 'stop', radio: tMyRadio, call: tCall}, function(response) {
							 if (response.indexOf('Not conn')>0)
							 {
								 $("#connectButton").removeClass('btn-small-success-on');
								 $("#connectButton").addClass('btn-small-lock-on');
							 };
								 $("#modalA-body").html(response);			  				
								 $("#modalA-title").html("Spots Status");
								   $("#myModalAlert").modal({show:true});//			  				alert(response);
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

					$("#menuPopup").bind('mouseout',function(){
						$('#menuPopup').css('display','none');
					});
					
					$(document).on('click', '.clickRow', function(event) {
						$(this).addClass('highlight').siblings().removeClass('highlight');
						tSelectedRow=$(this).attr('id');
						$.post("/programs/SetSettings.php", {field: "ClusterID", radio: tMyRadio, data: tSelectedRow, table: "MySettings"}, function(response){
							$.post('/programs/GetCluster.php',{id: tSelectedRow, field: 'NodeCall', table: 'Clusters'},function(response) {
								var descr="Current node: "+response;
								$('.nodeSel').text(descr);
							});
							
						});
					});
					
				   $.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'ClusterID', table: 'MySettings'}, function(response){
						var clusterID=response;
						$.post('/programs/GetCluster.php',{id: clusterID, field: 'NodeCall', table: 'Clusters'},function(response) {
							 descr="Current node: "+response;
							$('.nodeSel').text(descr);
						});
					});
				
					$.getScript("/js/modalLoad.js");

				})
			})
			
			function getClusters(tFil){
				  $.post('/programs/getClusters.php',{filter:tFil,username:tUserName},function(response) {
					  getSubClusters(response);
				  })
			}
		
		function checkScrollability(element) {
		  const hasVerticalScroll = element.scrollHeight > element.clientHeight;
		  const hasHorizontalScroll = element.scrollWidth > element.clientWidth;
		
		  if (hasVerticalScroll && hasHorizontalScroll) {
			console.log(element.id + " is scrollable both vertically and horizontally.");
		  } else if (hasVerticalScroll) {
			console.log(element.id + " is scrollable vertically.");
		  } else if (hasHorizontalScroll) {
			console.log(element.id + " is scrollable horizontally.");
		  } else {
			console.log(element.id + " is not scrollable.");
		  }
		}

			function getSubClusters(response){
				var arrayList=new Array();
				var tL="";
				$('#clusterTable tbody').empty();
				$('#clusterTable tbody').append(response);
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'ClusterID', table: 'MySettings'}, function(response)
				{
					$('tr').removeClass('highlight'); // remove class from other rows
					$('#'+response).addClass('highlight');
					tSelectedRow=document.getElementById(response)
//					let cTable = document.getElementById("clusterTable");
					
//					checkScrollability(cTable);

//					var x=cTable.getAttribute('id');
//					var tSelectedRow = document.getElementById(response);
					if (tSelectedRow) {
//						selectedRow=document.getElementById(tSelectedRow);
					  // Scroll to selected row
					  clusterTable.scrollTop = tSelectedRow.offsetTop - (clusterTable.clientHeight / 2) + (tSelectedRow.clientHeight / 2);
					  $(tSelectedRow).addClass('highlight').siblings().removeClass('highlight');
				  }

//					cTable.scrollTop = cTable.offsetTop - (cTable.clientHeight / 2) + (cTable.clientHeight / 2);

//					cTable.scrollTop = cTable.offsetTop - 400 +cTable.clientHeight / 2;

				});
			}	

			$(document).on('click', '.clusterFilter', function(event) {
				var tFil = $(this).attr('id');
				getClusters(tFil);
				$.post("/programs/SetSettings.php", {field: "ClusterFilter", radio: tMyRadio, data: tFil, table: "MySettings"});
			});

			$(document).on('click', '.myRet', function(event) {
				var tRet = $(this).attr('id');
				  $('#curRet').val(tRet+' Minutes');
				$.post("/programs/SetSettings.php", {field: "RetainTime", radio: tMyRadio, data: tRet, table: "MySettings"});
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
	</head>
<body class="body-black-scroll">
	<?php require $dRoot . "/includes/header.php"; ?>
	<div class="container-fluid">
		<div class="row" style="margin-top:10px;">
			<div class="col-sm-12 text-center">
				<span class="label label-success text-white "style='cursor: default; ' >Spot Settings (User: <?php echo $tUserName; ?>)</span>
				<hr>
			</div>
		</div>
		<div class="row">
			<div class="col-md-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Retain Time</span>
					</div>
					<input type="text" class="form-control  disable-text" readonly id="curRet"  title="Minutes to retain spots" aria-lable="id" aria-describedby="id-addon">
					<span class="input-group-btn">
						<div class="dropdown">
							<button class="btn btn-primary dropdown-toggle" id="idSel" data-size="1" type="button" title="Minutes to retain spots" data-toggle="dropdown">
								<i class="fas fa-list-alt fa-lg"></i>
							</button>
							<div class="dropdown-menu dropdown-menu-right" id='retainList' aria-labelledby="idSelectButton">
								<div class='myRet' id='1'><li><a class='dropdown-item' href='#'>1</a></li></div>
								<div class='myRet' id='5'><li><a class='dropdown-item' href='#'>5</a></li></div>
								<div class='myRet' id='10'><li><a class='dropdown-item' href='#'>10</a></li></div>
								<div class='myRet' id='30'><li><a class='dropdown-item' href='#'>30</a></li></div>
								<div class='myRet' id='60'><li><a class='dropdown-item' href='#'>60</a></li></div>
							</div>
						</div>
					</span>
				</div>
			</div>
			<div class="col-md-4 text-spacer">
			</div>
			<div class="col-md-2 text-center text-spacer">
				<button class="btn btn-outline-success btn-sm my-2 my-sm-0 text-white" id="connectButton"  title="Click to connect to Cluster" type="button">
					<i class="fas fa-play"></i>
					Connect
				</button>
			</div>
			<div class="col-md-2 text-center text-spacer">
				<button class="btn btn-outline-danger btn-sm my-2 my-sm-0 text-white" id="disconnectButton"  title="Click to disconnect from Cluster" type="button">
					<i class="fas fa-stop"></i>
					Disconnect
				</button>
			</div>
		</div>
		<hr>
		<div class="row" style="margin-top:10px;">
			<div class="col-sm-4 text-center"> 
				<span class="label nodeSel label-success text-white " style='cursor: default;'></span>				
			</div>
			<div class="col-sm-4 text-center">
				<span class="label label-success text-white " style='cursor: default;'>Spot Clusters</span>
			</div>
			<div class="col-sm-4 text-center">
				<div class="dropdown">
					<button class="btn btn-primary btn-color dropdown-toggle hButton" id="selStyle" data-size="3" type="button"  title="Filter spots by location" data-toggle="dropdown"><i class="fas fa-filter fa-lg"></i>
					</button>
					<ul class="dropdown-menu dropdown-menu-right menu-scroll" id="fnList">
						<li class="clusterFilter" id='all'><a class='dropdown-item' id='fn' href='#'>All Clusters</a></li>
						<li class="clusterFilter" id='grid'><a class='dropdown-item' id='fn' href='#'>Closest to Me</a></li>
						<li class="clusterFilter" id='state'><a class='dropdown-item' id='fn' href='#'>My US State</a></li>
					</ul>
				</div>
			</div>
		</div>
		<div id='clusterTable' tabindex='0'>
			<table class='table table-sm table-striped' onselectstart='return false' id='logt'>
				<thead><tr>
					<tr class='sortable' id='logt'>
						  <th  style='cursor: default; text-align:center;' id='node'>Node</th>
						  <th  style='cursor: default; text-align:center;' id='ip' name='tCall'>IP</th>
						  <th  style='cursor: default; text-align:center;' id='port'>Port</th>
						  <th  style='cursor: default; text-align:center;' id='location'>Location</th>
						  <th  style='cursor: default; text-align:center;' id='notes'>Notes</th>
					</tr>
				</thead>
				<tbody class="clusterTable">
				</tbody>
			</table>
		</div>
	</div>
	<br />
	<?php require $dRoot . "/includes/footer.php"; ?>
	<?php require $dRoot . "/includes/modal.txt"; ?>
	<?php require $dRoot . "/includes/modalAlert.txt"; ?>
<?php require $dRoot . "/includes/modalCancelOnly.txt"; ?>
<script src="./Bootstrap/popper.min.js"</script>
<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
<script src="./Bootstrap/jquery-ui.js"></script>
<script src="./Bootstrap/bootstrap.min.js"></script>
</body>
</html>
