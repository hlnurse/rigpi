<?php
/**
 * @author Howard Nurse, W6HN
 *
 * This is the spots window
 *
 * It must live in the html folder
 */
session_start();
// $_SESSION['user'] = 'alice';
// $_SESSION['role'] = 'admin';
$tUserName = $_SESSION["myUsername"];
$tCall = $_SESSION["myCall"];
$tMyRadio = $_SESSION["myRadio"];
$dRoot = "/var/www/html";
require_once $dRoot . "/classes/Membership.php";
$membership = new Membership();
$membership->confirm_Member($tUserName);
?>
<!DOCTYPE html>
<html lang="en">
  <meta name="viewport" content="width=device-width, initial-scale=1">

<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<title><?php echo $tCall; ?> RigPi Spots</title>
	<meta name="description" content="RigPi Spots" />
	<meta name="author" content="Howard Nurse, W6HN" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

	<!-- Bootstrap CSS -->
	<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
	<link rel="shortcut icon" href="/favicon.ico" />
	<link rel="apple-touch-icon" href="/favicon.ico" />
	<link rel="stylesheet" href="/Bootstrap/bootstrap.min.css" type="text/css" />
	<link rel="stylesheet" href="/Bootstrap/jquery-ui.css" type="text/css" />
	<script src="/Bootstrap/jquery.min.js" ></script>
	<script defer src="./awe/js/all.js" ></script>
	<link href="/awe/css/all.css" rel="stylesheet">
	<link href="/awe/css/fontawesome.css" rel="stylesheet">
	<?php require $dRoot . "/includes/styles.php"; ?>
	<link href="/awe/css/solid.css" rel="stylesheet">

<script type="module">
	  import ScrollCenterPreserver from '/js/ScrollCenterPreserver1.js';
			var searchCall='';
			var tMyRadio="<?php echo $tMyRadio; ?>";
			var bsCenterFreq=0;
			var waitRefresh=0;
			var tBand='20';
			var tBandOld='20';
			var tHiFreq='';
			var pCount=0;
			var selectedRow=0;
			var tUserName="<?php echo $tUserName; ?>";
			var tCall="<?php echo $tCall; ?>";
			var tNote='';
			var tUser='';
			var tMain=0;
			var tNeed='';
			var tFil='20';
			var tFilMode='All';
			var tFollowMe=0;
			var tCurFreq='';
			var tCurMode='';
			var tSort='', rowdx;
			var tSortDir='', disconnected=0;
			var waitSpots=4;
			var tNoRadio=0;
			var tNoInternet=0;
			var speedPot=0;
			var deleteSpot=0;
			var tMyCall=<?php echo "'" . $tCall . "'"; ?>;
			let cTable;
			let scrollLock = 4;
			let scrollTimer;
			let scrollLockOld=scrollLock;
			let telnetConnected=0;
			let tClusterNode='', tMode='';
			let userIsScrolling = false;
			let suppressPointerEvents = false;
			let modeList, tRadioName, tSh;
			let tM, tBW;
			let tClusterPort=0, tFreq=0,tFreqHi=0,tSplit=0, row, closestRow;
			var speedPot=0, spotCountOld=0,clickedRow, rowdx='W6HN', updatedRow, clickTimeout, allowScrollLock;
			var deleteSpot=0, debouncedCallback, container, sp, rowIDSelected=0, rowID=0, rowID4=0, offsetBefore, spotCount=0;

			$(document).ready(function() {

				function clickMe1(rowID) {
					clearTimeout(clickTimeout);

					  // Set a new timer for click action
					  clickTimeout = setTimeout(() => {
					console.log("Single-click on row:", rowID);
					if (deleteSpot==1){  //delete button clicked
						deleteSpot=0;
						return;
					}
					rowIDSelected=rowID;
					updatedRow = document.getElementById(rowID); // Assuming rows are <tr> in a <table>
					highlightAndScroll(updatedRow);

//						$(updatedRow).addClass('highlight').siblings().removeClass('highlight');
					cTable = document.getElementById('logt1');

//						rowID = $(this).attr('id').textContent;
					scrollLock=0;
					 if (rowID>0){
						// Find the same row again by ID
//					   updatedRow = document.getElementById(rowID);
//						   cTable.scrollTop = updatedRow.offsetTop - (cTable.clientHeight / 2) + (updatedRow.clientHeight / 2);
				  }

					tMode=updatedRow.find('td').eq(8).text();
//						console.log('Clicked column content:', tMode);
					rowdx = updatedRow.getAttribute('call');
					$('#searchText').val(rowdx);
					$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: rowdx, table: "MySettings"});
					if (tNoRadio==0 && updatedRow){
						tFreq = updatedRow.getAttribute('frequency');
						tMain=tFreq;
						selectedRow=updatedRow.getAttribute('id');
						tBand = updatedRow.getAttribute('band');
						 tMode = updatedRow.getAttribute('mode');
						if (tMode=='follow'){
							tMode=tCurMode;
						}
						var tFreqHi=getBSHiFreq(tFreq,tBand);
						var scrollPixels=((tFreqHi-parseInt(tFreq))/100)-400;
						$("#marker").html("");
						getBSFreqFromFreq(bsCenterFreq,tBand);
						paintBS(bsCenterFreq,tBand);
						paintBSSpots(tBand);
						$("#bs").animate({ scrollTop: scrollPixels });
						paintBSMarker(scrollPixels);
						cTable = document.getElementById('logt1');
//						$.post("/programs/SetSettings.php", {field: "ModeOutCk", radio: tMyRadio, data: "1", table: "RadioInterface"},function(response){
//							$.post("/programs/test.php", function(response){
								$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: tMode, table: "RadioInterface"}, function(){

							$.post("/programs/SetSettings.php", {field: "BWOut", radio: tMyRadio, data: tBW, table: "RadioInterface"},function(response){
//								$.post("/programs/SetSettings.php", {field: "ModeIn", radio: tMyRadio, data: tMode, table: "RadioInterface"}, function(){
									console.log('Main set to:'+tFreq+ " with mode: "+tMode);

									$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: tFreq, table: "RadioInterface"}, function(){
//											$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: tFreq, table: "RadioInterface"}, function(){
//														$.post("/programs/SetSettings.php", {field: "ModeIn", radio: tMyRadio, data: tMode, table: "RadioInterface"}, function(){
$.post("/programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tCall, table: "MySettings"}, function(){
//																		$.post("/programs/SetSettings.php", {field: "ModeIn", radio: tMyRadio, data: tMode, table: "RadioInterface"}, function(){
//																		});
//															});
//													});
//											});
									})
//								});
							});
						});
						});
						waitRefresh=6;
						bsCenterFreq=tFreq;
					};
					  }, 250);
				}
				function clickMe(rowID) {
					scrollLock=0;
					waitSpots=4;
					console.log("Single-click on row:", rowID);
					updatedRow = document.getElementById(rowID); // Assuming rows are <tr> in a <table>
					var tMode1 = updatedRow.getAttribute('mode'); // Assuming rows are <tr> in a <table>
					tNote = updatedRow.getAttribute('note');
					tFreq = updatedRow.getAttribute('freq');
/*					if (tFreq && tFreq.includes("074"))
						{
						   tMode='USB-D';
						}
*/					   if (tNote && tNote.toLowerCase().includes("ft8")) {
						   tMode='USB-D';
					   }

					console.log("mode on row: "+ tMode);//updatedRow.getAttribute('mode'));
					clearTimeout(clickTimeout);
					  // Set a new timer for click action
					  clickTimeout = setTimeout(() => {
					if (deleteSpot==1){  //delete button clicked
						deleteSpot=0;
						return;
					}
					rowIDSelected=rowID;
//								highlightAndScroll(updatedRow);

						$(updatedRow).addClass('highlight').siblings().removeClass('highlight');

//						rowID = $(this).attr('id').textContent;
//									scrollLock=0;
					 if (rowID>0){
						// Find the same row again by ID
					   updatedRow = document.getElementById(rowID);
					   cTable.scrollTop = updatedRow.offsetTop - (cTable.clientHeight / 2) + (updatedRow.clientHeight / 2);
				  }

					console.log('Clicked column content:'+ tMode1);
					rowdx = updatedRow.getAttribute('call');
					$('#searchText').val(rowdx);
					$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: rowdx, table: "MySettings"});
					if (tNoRadio==0 && updatedRow){
						tFreq = updatedRow.getAttribute('frequency');
						tNote = updatedRow.getAttribute('note');
						   let freqs = "14074000|7074000|21074000|1840000|3573000|5357000|10136000|18100000|24915000|28074000|50313000".split("|");
						   if (freqs.includes(tFreq)) {
							   tMode1='PKTUSB';
						   }
						   if (typeof tNote === "string" && tNote.trim().length > 0) {
							 if (tNote.toLowerCase().includes('ft8')) {
							   tMode1='PKTUSB';
						   }
					   }
					   if (modeList.indexOf(tMode1)<0){
//							  tMode='USB';
						  }

						var tFreq1=addPeriods(tFreq);
						$("#BSFreqDisp").html(tFreq1);

						selectedRow=rowID;//updatedRow.getAttribute('id');
						tBand = updatedRow.getAttribute('band');
//										 tMode = updatedRow.getAttribute('mode');
						if (tMode=='follow'){
							tMode=tCurMode;
						}
						tFreqHi=getBSHiFreq(tFreq,tBand);
						var scrollPixels=((tFreqHi-parseInt(tFreq))/100)-400;
						$("#marker").html("");
						getBSFreqFromFreq(bsCenterFreq,tBand);
						paintBS(bsCenterFreq,tBand);
						paintBSSpots(tBand);
						$("#bs").animate({ scrollTop: scrollPixels });
						paintBSMarker(scrollPixels);
						cTable = document.getElementById('logt1'); //?
						if (modeList.indexOf(tMode1)<0){
//							   tMode='USB';
						   }

						console.log ("saving mode: " + tMode1);
//						$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: tFreq, table: "RadioInterface"},function(response){
							$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: tFreq, table: "RadioInterface"},function(response){
								var tBW=-1;
							$.post("/programs/SetSettings.php", {field: "BWOut", radio: tMyRadio, data: tBW, table: "RadioInterface"},function(response){
								//.$.post("/programs/SetSettings.php", {field: "ModeOutCk", radio: tMyRadio, data: "1", table: "RadioInterface"},function(response){

								$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: tMode1, table: "RadioInterface"}, function(response){
									console.log('Main out set to:', tFreq + " " + tMode1 + " " + response );

									$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: tMode1, table: "RadioInterface"}, function(response){
											$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: tFreq, table: "RadioInterface"}, function(){
//														$.post("/programs/SetSettings.php", {field: "ModeIn", radio: tMyRadio, data: tMode, table: "RadioInterface"}, function(){
															$.post("/programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: rowdx, table: "MySettings"}, function(){
//																		$.post("/programs/SetSettings.php", {field: "ModeIn", radio: tMyRadio, data: tMode, table: "RadioInterface"}, function(){ //note dupe required for some reason
//																		});
//															});
//													});
										});
									});
								});
							});
						});
						waitRefresh=4;  //allow radio to catch up

						});
						bsCenterFreq=tFreq;
					};
					  }, 250);
				  }

				document.addEventListener("click", (e) => {
					console.log("running click event");
					const row = e.target.closest("tr");
					if (row && row.parentNode.tagName === "TBODY") {
					  // Remove highlight from other rows
					  row.closest("#tbody").querySelectorAll("tr").forEach(r => r.classList.remove("highlight"));
					  // Highlight clicked row
					  if (row) {
						  row.classList.add("highlight");
						   tMode = row.getAttribute("mode");
						   tNote = row.getAttribute("note");
						   rowID=row.getAttribute("id");
						   tFreq = row.getAttribute("frequency").trim();
						   cTable = document.getElementById('sp');  //?

//						   tNote="huh";
						   console.log("Note: " + tNote);
						   let freqs = "14074000|7074000|21074000|1840000|3573000|5357000|10136000|18100000|24915000|28074000|50313000".split("|");
						   if (freqs.includes(tFreq)) {
							   tMode='USB-D';
						   }
						   if (typeof tNote === "string" && tNote.trim().length > 0) {
							 if (tNote.toLowerCase().includes('ft8')) {
							   tMode='USB-D';
						   }
					   }
						   console.log("Row mode:", tNote + " " + tMode);
						   clearTimeout(clickTimeout);
							 // Set a new timer for click action
							 clickTimeout = setTimeout(() => {
						  row.classList.add("highlight");
						tMode = row.getAttribute("mode");
						console.log("Row mode new:" + tMode);

						tNote = row.getAttribute("note");
						rowID=row.getAttribute("id");
						tFreq = row.getAttribute("frequency").trim();
						cTable = document.getElementById('tbody');  //?

							 //						   tNote="huh";
														console.log("Note: " + tNote);

						   clickMe(rowID);
						   }, 250);

						}
					}
				  });

				  document.addEventListener("dblclick", (event) => {
						clickedRow = event.target.closest("tr");
						if (!clickedRow) return;

						clearTimeout(clickTimeout);
						rowID4 = clickedRow.id;
						scrollLock = 4;

						updatedRow = document.getElementById(rowID4);
						if (updatedRow) {
						  cTable.scrollTop = updatedRow.offsetTop - (cTable.clientHeight / 2) + (updatedRow.clientHeight / 2);
						  $(updatedRow).addClass('centerHighlight').siblings().removeClass('centerHighlight');
						  if (rowID){
							  var tRow=document.getElementById(rowID);
							  $(tRow).addClass('highlight').siblings().removeClass('highlight');

						  }

						}
					});

					$.post('/programs/testInternet.php',function(response){
						if (response !=0){
							tNoInternet=1;
						}else{
							$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'ClusterID', table: 'MySettings'}, function(response){
							var ClusterID=response;

							$.post('/programs/GetCluster.php',{id: ClusterID, field: 'NodeCall', table: 'Clusters'},function(response) {
						tClusterNode=response;
							$.post('/programs/GetCluster.php',{id: ClusterID, field: 'Port', table: 'Clusters'},function(response) {
							tClusterPort=response;
							$.post("/programs/checkPort.php", {clusterPort: tClusterPort}, function(data) {
								if (data){
									telnetConnected=1;
								}else{
									telnetConnected=0;
								}
							});
						});
					});
						});
					};
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


				$.post('/programs/GetSelectedRadio.php',{un:tUserName}, function(response)
				{
					tMyRadio=response;
					$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'RadioName', table: 'MySettings'}, function(response)
					  {
						  tRadioName=response;

					$.post("/programs/getRigCaps.php", {myRadioName: tRadioName, myRadio: tMyRadio, cap: 'Mode list:'}, function(response){
						modeList=response;
					});
						if (telnetConnected==1){
							$("#connectButton").addClass('btn-small-success-on');
						}else{
							$("#connectButton").addClass('btn-small-lock-on');
						};

					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'SpotsSort', table: 'MySettings'}, function(response){
						tSort=response;
					});
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'SpotsSortDir', table: 'MySettings'}, function(response){
						tSortDir=response;
					});
					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'SpotBand', table: 'MySettings'}, function(response){
						tFil=response;
						 $.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'SpotMode', table: 'MySettings'}, function(response){
							tFilMode=response;
							 $.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'SpotNeed', table: 'MySettings'}, function(response){
								tNeed=response;
								$.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:tMyCall}, function(response)
								{
									var tAData=response.split('`');
									if (!$.isNumeric(tAData[0])){
										tNoRadio=1;
										tAData[0]="0";
									}else{
										tNoRadio=0;
									}
									tMain=tAData[0];
									if (tMain != bsCenterFreq){
										bsCenterFreq=tMain;
										tBand=tAData[5];
										var tFreqHi=getBSHiFreq(bsCenterFreq, tBand);
										var scrollPixels=((tFreqHi-bsCenterFreq)/100)-400;
										$("#marker").html("");
										paintBS(bsCenterFreq,tBand);
										paintBSSpots(tBand);
										getBSFreqFromFreq(bsCenterFreq,tBand);
//										getBSFreq(tBand);
										$("#bs").animate({ scrollTop: scrollPixels });
										paintBSMarker(scrollPixels);
//										$.post("/programs/SetSettings.php", {field: "LastFreq", radio: tMyRadio, data: tMain, table: "MySettings"});
//										$.post("/programs/SetSettings.php", {field: "LastBand", radio: tMyRadio, data: tBand, table: "MySettings"});
									}

									var tSplitState=tAData[1];
									if (tSplitState=="0")
									{
										//no split marker
									}else{
										//add split marker
									}

									tSplit=tAData[2];
									tMode=tAData[3];
								});
							});
						});
					});

					$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response){
						$('#searchText').val(response);
					});





//);

					$(document).on('click', '#bandCanvas', function(e){
						var fTune=e.pageY;
						var fT=e.clientY;
						var sT=$('#bandCanvas').scrollTop();
						var cP=$('#bandCanvas').offset().top;
						var oF=(cP-fTune)*100;
						tFreq=getBSHiFreq(tMain,tBand)+oF;
						if (GetBandFromFrequency(tFreq)=='UNK'){
							return;
						}
						bsCenterFreq=tFreq;
						 var scrollPixels=((-1*oF/100)-400);
						$("#bs").animate({ scrollTop: scrollPixels+"px" });
						 $("#marker").html("");
//						getBSFreq(tBand);
						paintBS(bsCenterFreq,tBand);
						getBSFreqFromFreq(bsCenterFreq,tBand);
						paintBSMarker(scrollPixels);
						 paintBSSpots(tBand);
//						 waitRefresh=4;
						$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: bsCenterFreq, table: "RadioInterface"});
						$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: bsCenterFreq, table: "RadioInterface"});
						$('tr').removeClass('highlight'); // remove class from other rows

						waitRefresh=4;
					});

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
						}else if (nFreq > 222000000 && nFreq < 225000000){
							return "1.25";
						}else if (nFreq > 420000000 && nFreq < 450000000){
							return "1.25";
						}else if (nFreq > 1240000000 && nFreq < 1300000000){
							return "1.25";
						}else {
							return "UNK";
						}
					}
					$(document).on('click', '.BSbutton', function(event) {
												tFreq = $(this).attr('frequency');
												tBand = $(this).attr('band');
												rowdx = $(this).attr('call');
												var tMode = $(this).attr('mode');
												if (modeList.indexOf(tMode)<0){
//													   tMode='USB';
												   }


												var idSpot =$(this).attr('id');
												scrollLock=0;
												var tID=idSpot.substring(1);
												selectedRow=tID;
												rowID=tID;
												$.post("/programs/SetSettings.php", {field: "MainOut", radio: tMyRadio, data: tFreq, table: "RadioInterface"});
												$.post("/programs/SetSettings.php", {field: "MainIn", radio: tMyRadio, data: tFreq, table: "RadioInterface"});

//											$.post("/programs/SetSettings.php", {field: "ModeOutCk", radio: tMyRadio, data: "1", table: "RadioInterface"},function(response){
/*//////												$.post("/programs/SetSettings.php", {field: "ModeOut", radio: tMyRadio, data: tMode, table: "RadioInterface"},function(response){
													$.post("/programs/SetSettings.php", {field: "BWOut", radio: tMyRadio, data: 0, table: "RadioInterface"},function(response){
//													$.post("/programs/SetSettings.php", {field: "ModeIn", radio: tMyRadio, data: tMode, table: "RadioInterface"}, function(){
//													});
												});
*///											});
/////											});
											waitRefresh=4;
											bsCenterFreq=tFreq;
											$('#searchText').val(rowdx);
											var tFreqHi=getBSHiFreq(bsCenterFreq,tBand);
												var scrollPixels=((tFreqHi-tFreq)/100)-400;
											$("#marker").html("");
											$("#bs").animate({ scrollTop: scrollPixels });
											paintBSMarker(scrollPixels);
					//////////////						$('tr').removeClass('highlight'); // remove class from other rows
					////////////////						$('#'+tID).addClass('highlight');
											$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tCall, table: "MySettings"});
											clickMe(rowID);
											});

					$(document).on('click', '.band', function(event) {
						tFil = $(this).attr('id');
						getSpots(tFil,tNeed, tFilMode, tSortDir, tSort);
						$.post("/programs/SetSettings.php", {field: "SpotBand", radio: tMyRadio, data: tFil, table: "MySettings"});
						pCount=10000;
					});

					$(document).on('click', '.needed', function(event) {
						tNeed = $(this).attr('id');
						getSpots(tFil,tNeed, tFilMode, tSortDir, tSort);
						$.post("/programs/SetSettings.php", {field: "SpotNeed", radio: tMyRadio, data: tNeed, table: "MySettings"});
						pCount=10000;
					});

					$(document).on('click', '.mode', function(event) {
						tFilMode = $(this).attr('id');
						getSpots(tNeed, tFil, tFilMode, tSortDir, tSort);
						$.post("/programs/SetSettings.php", {field: "SpotMode", radio: tMyRadio, data: tFilMode, table: "MySettings"});
						pCount=10000;
					});
});
				});

					fillDescription();
					function getCenteredVisibleRow() {
						  const container = document.getElementById('logt');
						  const containerRect = container.getBoundingClientRect();
						  const containerCenterY = containerRect.top + container.clientHeight / 2;

						  const rows = container.querySelectorAll('tr');
						  closestRow = null;
						  let closestDistance = Infinity;

						  rows.forEach(row => {
							const rowRect = row.getBoundingClientRect();

							// Check if row is inside visible viewport of container
							if (rowRect.bottom >= containerRect.top && rowRect.top <= containerRect.bottom) {
							  const rowCenterY = (rowRect.top + rowRect.bottom) / 2;
							  const distance = Math.abs(rowCenterY - containerCenterY);

							  if (distance < closestDistance) {
								closestDistance = distance;
								closestRow = row;
							  }
							}
						  });

						  return closestRow;
						}

						function getEndVisibleRow() {
						  const container = document.getElementById('logt');
						  const containerRect = container.getBoundingClientRect();
						  const containerCenterY = containerRect.top + container.clientHeight;

						  const rows = container.querySelectorAll('tr');
						  let closestRow = null;
						  let closestDistance = Infinity;

						  rows.forEach(row => {
							const rowRect = row.getBoundingClientRect();

							// Check if row is inside visible viewport of container
							if (rowRect.bottom >= containerRect.top && rowRect.top <= containerRect.bottom) {
							  const rowCenterY = (rowRect.top + rowRect.bottom) / 2;
							  const distance = Math.abs(rowCenterY - containerCenterY);

							  if (distance < closestDistance) {
								closestDistance = distance;
								closestRow = row;
							  }
							}
						  });

						  return closestRow;
						}
						  function debouncedCallback(func, wait) {
						let timeout;
						return function() {
						  clearTimeout(timeout);
						  timeout = setTimeout(func, wait);
						};
					}
			$(document).keydown(function(e){
				var t=e.key;
				e.multiple
				var w=e.which;
				if (w=1){
					$("#searchButton").click();
				}

				cTable = document.getElementById('tbody');
				  const firstRow = document.querySelector("#tbody tr");
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
//							rowID=0;
						scrollLock=4;
						getSpots(tFil,tNeed, tFilMode, tSortDir, tSort);

					}
					if (t=="Help" || t=='r'){ //lock to red
						scrollLock=0;
						pCount=10000;
						getSpots(tFil,tNeed, tFilMode, tSortDir, tSort);
					}
					scrollLockOld=scrollLock;
					if (scrollLock==1){
						cTable.scrollTop = cTable.scrollHeight;
					}
					if (scrollLock==2){
						cTable.scrollTop = 0;
					}
					if (rowID){
						updatedRow = document.getElementById(rowID);
						cTable.scrollTop = updatedRow.offsetTop - (cTable.clientHeight / 2) + (updatedRow.clientHeight / 2);
						$(updatedRow).addClass('highlight').siblings().removeClass('highlight');
					}
					if (rowID4){
						updatedRow = document.getElementById(rowID4);
						$(updatedRow).addClass('centerHighlight').siblings().removeClass('centerHighlight');

					}
				}


					  if (scrollLock==0){
//							  rowID = $(this).attr('id');

					  if (scrollLock==1){
						  cTable.focus();
						  cTable.scrollTop = cTable.scrollHeight;
						  const rows = document.querySelectorAll("#logt tr");
						  const endIndex = Math.floor(rows.length);
						  const endRow =getEndVisibleRow();
//						  rowID = endRow.getAttribute("id");
						  rowdx = endRow.getAttribute("call");
						  $('#searchText').val(rowdx);

					  }
					  if (scrollLock==2){
							cTable.focus();
							cTable.scrollTop = 0;
						}
						cTable.focus();

					  if (scrollLock==4){
						const rows = document.querySelectorAll("#logt tr");
						const middleIndex = Math.floor(rows.length / 2);
						const centerRow =getCenteredVisibleRow();
						rowID4 = centerRow.getAttribute("id");
						rowdx = centerRow.getAttribute("call");
						$('#searchText').val(rowdx);

						if (rowID4){
							updatedRow = document.getElementById(rowID4);
						  cTable.scrollTop = updatedRow.offsetTop - (cTable.clientHeight / 2) + (updatedRow.clientHeight / 2);
						  if (centerRow) {
//									highlightCenteredRow();
//								  centerRow.addClass.classList.add('active').siblings().removeClass('active');
							  $(centerRow).addClass('centerHighlight').siblings().removeClass('centerHighlight');

							console.log('Visible center row ID:', centerRow.id);
							// or highlight it:
//								centerRow.classList.add('highlight');
						  }
//						  }else{
//							  scrollLock=4;
						  }
						  if (rowID){
							  updatedRow = document.getElementById(rowID);
//...							  $(updatedRow).addClass('highlight').siblings().removeClass('highlight');
						  }
						}

						}

				if (e.altKey){
					switch(w){

					case 65: // a
						showCalendar();
						e.preventDefault();
						break;
					case 67: // c
						$('#connectButton').click();
						e.preventDefault();
						break;
					case 68: // d
						$('#deleteButton').click();
						e.preventDefault();
						break;
					case 69: // e
						showSettings();
						e.preventDefault();
						break;
					case 72: // h
						showHelp();
						e.preventDefault();
						break;
					case 73: // i
						$('#disconnectButton').click();
						e.preventDefault();
						break;
					break;
					case 75: //k
						var win="/keyer.php?x="+tUserName+"&c="+tMyCall;
						window.open(win, "_self");
						break;
					case 76: //l
						var win="/log.php?x="+tUserName+"&c="+tMyCall;
						window.open(win, "_self");
						break;
					case 83: // s
						var win="/spots.php?x="+tUserName+"&c="+tMyCall;
						window.open(win, "_self");
						break;
					case 84: // t
						var win="/index.php?x="+tUserName+"&c="+tMyCall;
						window.open(win, "_self");
						break;
					case 87: // w
						var win="/web.php?x="+tUserName+"&c="+tMyCall;
						window.open(win, "_self");
						break;
					case 88: // x
						openWindowWithPost("/login.php", {
						status: "loggedout",
						username: tUserName});
					}

					return false
				}
				if (w==191)
				{
					if (e.shiftKey){
					<?php require $dRoot . "/includes/shortcutsSpots.php"; ?>
						$(".modalCO-body").html(tSh);
						$(".modalCO-title").html("Shortcut Keys");
						  window.$("#MyModalCancelOnlySpots").modal({show:true});
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
			});
					$("input").bind("keydown", function(event)
					{
						// track enter key
						var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
						if (keycode == 13) { // keycode for enter key
							if ($('#searchText').val()==''){
								return false;
							}
							rowdx=$('#searchText').val().toUpperCase();
							$('#searchText').val(rowDX);
							document.getElementById('searchButton').click();
							$.post("/programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: rowdx, table: "MySettings"});
							return false;
						} else  {
							return true;
						}
					});
					tUpdate = setInterval(updateTimer,500);

			});

			$(document).on("click", "#searchButton", function () {
			  var dx = $("#searchText").val().toUpperCase();
			  if (dx.length == 0 || ~dx.indexOf("*") || ~dx.indexOf("=")) {
				return;
			  }
//			  tUserName='admin';
			  tMyRadio=1;
			  $.post(
				"/programs/GetUserField.php",
				{ un: tUserName, field: "uID" },
				function (response) {
				  tUser = response;
				  $.post("/programs/SetSettings.php", {
					field: "waitReset",
					radio: tMyRadio,
					data: 1,
					table: "RadioInterface",
				  });

				  $.post(
					"./programs/GetCallbook.php",
					{ call: dx, what: "QRZData", user: tUser, un: tUserName },
					function (response) {
					  $(".modal-body").html(response);
					  $.post(
						"./programs/GetCallbook.php",
						{ call: dx, what: "QRZpix", user: tUser, un: tUserName },
						function (response) {
						  var aPix = response.split("|");
						  var h = aPix[1];
						  var w = aPix[2];
						  if (h > 0) {
							var wP = aPix[2] / 280;
							var tW = w / wP;
							var tH = h / wP;
							$(".modal-pix").attr("height", tH + "px");
							$(".modal-pix").attr("width", tW + "px");
							$(".modal-pix").attr("src", aPix[0]);
						  } else {
							$(".modal-pix").attr("height", "0px");
							$(".modal-pix").attr("width", "0px");
							$(".modal-pix").attr("src", "");
						  }
						  $(".modal-title").html(dx);
						  $("#myModal").modal({ show: true });
						}
					  );
					}
				  );
				  $.post("./programs/SetSettings.php", {
					field: "DX",
					radio: tMyRadio,
					data: dx,
					table: "MySettings",
				  });
				}
			  );
			});

			$(document).on("click", "#stop", function () {
			  $.post("./programs/SetMyRotorBearing.php", {
				w: "stop",
				i: tMyRadio,
				a: "1",
			  });
			});

			$(document).on("click", "#modalClose", function () {
				if (disconnected==1){
					showConnectAlert();
					return false;
				}

			  $.post("/programs/SetSettings.php", {
				field: "waitReset",
				radio: tMyRadio,
				data: 0,
				table: "RadioInterface",
			  });
			  $("#myModal").modal("hide");
			});

			function showConnectAlert(){
				$("#modalA-body").html("<br>&nbsp;&nbsp;The radio is not connected.<p><p>");
				$(".modalA-title").html("Radio Connection");
				  $("#myModalAlert").modal({show:true});
				  setTimeout(function(){
					  $("#myModalAlert").modal('hide');
				 },
				  2000);
				return;
			}

			$(document).on("click", "#rotate", function () {
				if (disconnected==1){
					showConnectAlert();
					return false;
				}
			  var dx = $("#searchText").val().toUpperCase();
			  $.post(
				"./programs/GetCallbook.php",
				{ call: dx, what: "Bearing", user: tUser, un: tUserName },
				function (response) {
				  if (confirm("Rotate to " + dx + " at " + response + " degrees?")) {
					$.post(
					  "/programs/SetSettings.php",
					  {
						field: "waitReset",
						radio: tMyRadio,
						data: 1,
						table: "RadioInterface",
					  },
					  function (response1) {
						$.post("./programs/SetMyRotorBearing.php", {
						  w: "turn",
						  i: tMyRadio,
						  a: response,
						});
					  }
					);
				  }
				}
			  );
			});

			/*$(document).keydown(function(event) {
			  if (event.keyCode == 27) {
					document.getElementById('modalClose').click();
			//        document.getElementById('modalAlertClose').click();
			  }
			});
			*/
			//var tUpdate = setInterval(bearingTimer,500)

			/*function bearingTimer()
			{
				$.post("./programs/GetRotorIn.php", {rotor: tMyRadio},function(response){
					var tAData=response.split('+');
					var tAz=Math.round(tAData[0])+"&#176;";
					$(".angle").html(tAz);
				});

			}
			*/



				function addPeriods(nStr) {
							  nStr += "";
							  var x = nStr.split(".");
							  var x1 = x[0];
							  var x2 = x.length > 1 ? "." + x[1] : "";
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
			//				  var tUserName='admin';//<php echo "admin";?>';

							/*  $.post('/programs/GetSelectedRadio.php',{un:tUserName}, function(response)
							  {
								tMyRadio=response;
							  })
							*/
							var tCall="<?php echo $_SESSION["myCall"]; ?>";
							var tMyRadio="<?php echo $_SESSION["myRadio"]; ?>";
							  $.post(
								"/programs/GetInterfaceIn.php",
								{ radio: tMyRadio, un: tUserName, myCall: tCall },
								function (response) {
								  var tAData = response.split("`");
								  var tF=tAData[0];
								  var tBW=tAData[17];
								   console.log("mode after in: " + tAData[3] + " " + tBW);
								   tM = tAData[3].trim();
										  let freqs = "14074000|7074000|21074000|1840000|3573000|5357000|10136000|18100000|24915000|28074000|50313000".split("|");
									   if (freqs.includes(tF)) {
										 tM='PKTUSB';
									}
								  if (tAData.indexOf("NG")<0) {
									var tRadioUpdate = tAData[8];
									if (tRadioUpdate.length != 0) {
									  $("#modalA-body").html('<br>&nbsp;&nbsp;'+tRadioUpdate+'<br><br>');
									  $(".modalA-title").html("RigPi Report");
									  $("#myModalAlert").modal('show');
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
									disconnected=1;
								  }else{
									  disconnected=0;
								  }
								  var cFreq2m = ("0000000000" + tAData[0]).slice(-10);
								  var tF = addPeriods(cFreq2m);
								  var tSplit = tAData[1];
								  var tSplitOn = tSplit;
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
									 if (tM == "PKTUSB") {
									  tM = "USB-D";
									}
									if (tM == "PKTLSB") {
									  tM = "LSB-D";
									}


									$("#fPanel3").text("Mode: " + tM + " - BW: "+tBW);
									$("#fPanel1").attr("style", "background-color:black");
								  }
								 }
							  );
							}

			var tUpdate = setInterval(bearingTimer,1000)
			function bearingTimer()
			{
				$.post("/programs/checkPort.php", {clusterPort: tClusterPort}, function(data) {
					if (data){
						telnetConnected=1;
					}else{
						telnetConnected=0;
					}
					if (telnetConnected==1){
						$("#connectButton").addClass('btn-small-success-on');
					}else{
						$("#connectButton").addClass('btn-small-lock-on');
					};

				});

//				$.post("/programs/GetSetting.php", {radio:tMyRadio,table:"MySettings",field:"ClusterConnected"}, function(response){
/*				if (telnetConnected==1){
					$("#connectButton").addClass('btn-small-success-on');
					$("#connectButton").removeClass('btn-small-lock-on');
				}else{
					$("#connectButton").addClass('btn-small-lock-on');
					$("#connectButton").removeClass('btn-small-success-on');
				};
//			});
*/
				$.post("/programs/GetRotorIn.php", {rotor: tMyRadio},function(response){
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
		$.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:tMyCall }, function(response)
		{
			var tAData=response.split('`');

		  tBW=tAData[17];

		  if (tAData[8] !== "NG") {
			var tRadioUpdate = tAData[8];
			if (tRadioUpdate.length != 0) {
			  $("#modalA-body").html('<br>&nbsp;&nbsp;'+tRadioUpdate+'<br><br>');
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
			disconnected=1;
		  }else{
			  disconnected=0;
		  }
		  var cFreq2m = ("0000000000" + tAData[0]).slice(-10);
		  var tF = addPeriods(cFreq2m);
		  var tSplit = tAData[1];
//.		  tSplitOn = tSplit;
let tSplitOn=0;
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


			function updateTimer()
			{
				waitRefresh=waitRefresh-1;
				if (waitRefresh>0)
				{
					console.log("wait: "+waitRefresh);
					return;
				}
				waitRefresh=0;
				$.post('/programs/GetInterfaceIn.php',{radio: tMyRadio, un: tUserName, myCall:tMyCall}, function(response)
				{
					var tAData=response.split('`');
					if (!$.isNumeric(tAData[0])){
						tNoRadio=1;
						tAData[0]="0";
					}else{
						tNoRadio=0;
					}
					tMain=tAData[0];
					updateFooter();
					if (tNoRadio==0){
//						return;
						var tMainBand=GetBandFromFrequency(tMain);
						if (tMain != bsCenterFreq){// && tMain>0){//&& tMainBand==tAData[5]){
							var tMainx=addPeriods(tMain);
//							$("#BSFreqDisp").html('');
							$("#BSFreqDisp").html(tMainx);
							bsCenterFreq=tMain;
							tBand=tMainBand;  /////////////////////////////////////////////
							var tFreqHi=getBSHiFreq(bsCenterFreq,tBand);
							var scrollPixels=((tFreqHi-bsCenterFreq)/100)-400;
							$("#marker").html("");
							$("#bs").animate({ scrollTop: scrollPixels });
							paintBSMarker(scrollPixels);
//							$.post("/programs/SetSettings.php", {field: "LastFreq", radio: tMyRadio, data: tMain, table: "MySettings"});
//							$.post("/programs/SetSettings.php", {field: "LastBand", radio: tMyRadio, data: tBand, table: "MySettings"});
						}
						var tSplitState=tAData[1];
						tSplit=tAData[2];
						tMode=tAData[3];
					}
				 });

				var now = new Date();
				var now_hours=now.getUTCHours();
				now_hours=("00" + now_hours).slice(-2);
				var now_minutes=now.getUTCMinutes();
				now_minutes=("00" + now_minutes).slice(-2);
				$("#fPanel5").text(now_hours+":"+now_minutes+'z');
				waitSpots=waitSpots-1;
				if (waitSpots>0){
					return;
				}else{
					waitSpots=4;	//throttle spot dump
					getSpots(tFil,tNeed, tFilMode, tSortDir, tSort);
				}
			}

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

			function getBandFilter(tWhat){
				if (tWhat!=tFil || tBandOld!=tBand){
					pCount=10000;
				};
				tBandOld=tBand;
				var tFilx='';
				switch (tWhat){
					case ('followMe'):
						tFilx = 'Band='+"'"+ tBand + "'";
						break;
					case ('hf'):
						tFilx = 'Band>6 and Band <=160';
						break;
					case ('low'):
						tFilx = 'Band>30 and Band <=160';
						break;
					case ('high'):
						tFilx = 'Band=10 or Band=15 or Band=20';
						break;
					case ('warc'):
						tFilx = 'Band=12 or Band=17 or Band=30';
						break;
					case ('vhfUHF'):
						tFilx = 'Band<10 or Band=23 or Band=70';
						break;
					case ('160'):
						tFilx = 'Band=160';
						break;
					case ('80'):
						tFilx = 'Band=80';
						break;
					case ('60'):
						tFilx = 'Band=60';
						break;
					case ('40'):
						tFilx = 'Band=40';
						break;
					case ('30'):
						tFilx = 'Band=30';
						break;
					case ('20'):
						tFilx = 'Band=20';
						break;
					case ('17'):
						tFilx = 'Band=17';
						break;
					case ('15'):
						tFilx = 'Band=15';
						break;
					case ('10'):
						tFilx = 'Band=10';
						break;
					case ('6'):
						tFilx = 'Band=6';
						break;
					case ('2'):
						tFilx = 'Band=2';
						break;
					default:
						tFilx = '1=1';
				}
				var tMainx=addPeriods(tMain);
//				$("#BSFreqDisp").html('');
				$("#BSFreqDisp").html(tMainx);
				return tFilx;
			}


function getBSHiFreq(bsfre, band){
	var tFreq1;
	switch (band){
		case '160':
			tFreq1=2100000;
			break;
		case '80':
			tFreq1=4050000;
			break;
		case '60':
			tFreq1=5500000;
			break;
		case '40':
			tFreq1=7400000;
			break;
		case '30':
			tFreq1=10250000;
			break;
		case '20':
			tFreq1=14450000;
			break;
		case '17':
			tFreq1=18270000;
			break;
		case '15':
			tFreq1=21500000;
			break;
		case '12':
			tFreq1=25090000;
			break;
		case '10':
			var freq=30000000 - parseInt(bsfre);
			if (freq>1500000){
				tFreq1=28550000;
			}else if (freq>1000000){
				tFreq1=29050000;
			}else if (freq>500000){
				tFreq1=29550000;
			}else{
				tFreq1=30050000;
			}
			break;
//			tFreq=28500000;
//						tFreq=parseInt(bsfre)+300000;//1246300000;
//			break;
		case '6':
			tFreq1=parseInt(bsfre/500000)*500000+550000;//1246300000;
			break;
		case '2':
			tFreq1=parseInt(bsfre/500000)*500000+550000;//1246300000;
			break;
		case '1.25':
			tFreq1=parseInt(bsfre/500000)*500000+550000;//1246300000;
			break;
		case '70':
			tFreq1=parseInt(bsfre/500000)*500000+550000;//1246300000;
			break;
		case '23':
			tFreq1=parseInt(bsfre/500000)*500000+550000;//1246300000;
			break;
		default:
			tFreq1=14025000;
			break;
	}
	tHiFreq=tFreq1;
	return tFreq1;
}
			function getBSHiFreqxxx(freq, band){
				switch (band){
					case '10':
						freq=30000000 - parseInt(freq);
						if (freq>1500000){
							tFreq=28550000;
						}else if (freq>1000000){
							tFreq=29050000;
						}else if (freq>500000){
							tFreq=29550000;
						}else{
							tFreq=30050000;
						}
						break;
					default:
						freq1=parseInt(freq);
						freq2=parseInt(freq1/500000);
						var tOffset=0;
						if (band=='160'){
							tOffset=210000;
						}else if (band=='80'){
							tOffset=404000;
						}else if (band=='40'){
							tOffset=550000;
						}else if (band=='15'){
							tOffset=500000;
						}else if (band=='15'){
							tOffset=500000;
						}else{
							tOffset=450000
						}
						tFreq=freq2*500000+tOffset;
						break;
				}
				tHiFreq=tFreq;
				return tFreq;
			}

			function paintBS(freq,band){
				var strokeUpper=0;
				var strokeLower=0;
				switch (band){
					case '160':
						strokeUpper=1000;
						strokeLower=3000;
						break;
					case '80':
						strokeUpper=500;
						strokeLower=500;
						break;
					case '60':
						strokeUpper=950;
						strokeLower=4000;
						break;
					case '40':
						strokeUpper=1000;
						strokeLower=2000;
						break;
					case '30':
						strokeUpper=1000;
						strokeLower=4500;
						break;
					case '20':
						strokeUpper=1000;
						strokeLower=1500;
						break;
					case '17':
						strokeUpper=1020;
						strokeLower=3980;
						break;
					case '15':
						strokeUpper=500;
						strokeLower=1000;
						break;
					case '12':
						strokeUpper=1000;
						strokeLower=4000;
						break;
					case '10':
						freq=30000000 - parseInt(freq);
						if (freq>1500000){
							strokeUpper=500;
							strokeLower=500;
						}else if (freq>1000000){
							strokeUpper=500;
							strokeLower=500;
						}else if (freq>500000){
							strokeUpper=500;
							strokeLower=500;
						}else if (freq>300000){
							strokeUpper=3500;
							strokeLower=500;
						}
						break;
					default:
						strokeUpper=500;
						strokeLower=500;
						break;
				}
				var startLower=6000-strokeLower;
				$('#frequencies').empty();
				$('#frequencies').line(136,0, 337, 0, {color:"red", stroke:strokeUpper, zindex:0});
				$('#frequencies').line(136, startLower, 337, startLower, {color:"red", stroke:strokeLower, zindex:0});
				getSpots(tFil,tNeed, tFilMode, tSortDir, tSort);

			}

			function paintBSMarker(freq){
				var bsFreq=freq+400;//window.innerHeight/2-50;
				$('#marker').line(17, bsFreq, 130, bsFreq, {color:"red", stroke:'2', zindex:0});
				$('#marker').line(17, bsFreq+2, 130, bsFreq+2, {color:"FireBrick", stroke:'2', zindex:0});
			}

			function paintBSSpots(bandx){
				$.post('/programs/GetBandSpotter.php',{radio:tMyRadio,band:bandx,frequency: bsCenterFreq, folder:'Inbox',need:tNeed}, function(response){
					 var aRSpots=response.split("top: ");
					 var aLSpots=response.split("top='");
					 var arr1;
					 var arr2;
					 var i=0;
					 var len=aRSpots.length;
					for(i=1;i<len;i++){
						arr1 = parseInt(aLSpots[i])+15;
						arr2 = parseInt(aRSpots[i])+15;
						$('#frequencies').line(135, arr1, 180, arr2, {color:"black", stroke:'2', zindex:0});
					}
					 $("#frequencies").append(response);
				 });
			}

			function getBSFreqFromFreq(nfreq,nband)
			{
				$.post("/programs/GetBSFrequenciesFreq.php",{freq: nfreq, band: nband},function(response){
					var freqs=response;
					$("#frequencies").append(freqs);
				});
			};

			function getSubSpots(response)
			{
				var pCount1=response.substr(3, response.indexOf(">")-3);
				if (pCount1==pCount){
					return;
				}
				pCount=pCount1;
				response=response.substr(response.indexOf(">")+1);
				if (scrollLock !== 10){
					$('#tbody').empty();
					$('#tbody').append(response);
				}else{
					return;
				}
				var tFreqHi=tHiFreq;
				if (tNoRadio==0){
					var scrollPixels=((tFreqHi-bsCenterFreq)/100)-400;
					$("#marker").html("");
					paintBS(bsCenterFreq,tBand);
					paintBSSpots(tBand);
					getBSFreqFromFreq(bsCenterFreq,tBand);
					$("#bs").animate({ scrollTop: scrollPixels });
					var tFreq1=addPeriods(bsCenterFreq);
					$("#BSFreqDisp").html(tFreq1);

					paintBSMarker(scrollPixels);
					$('tr').removeClass('highlight'); // remove class from other rows
					selectedRow=document.getElementById(rowID);
					$(selectedRow).addClass('highlight');
				}
				const rows = document.querySelectorAll('#sp tr');
				let fadeTimeout;
				let selectionTimeout;

				// Store each row's original background color on load
				rows.forEach(row => {
				  const computedStyle = window.getComputedStyle(row);
				  row.dataset.original = computedStyle.backgroundColor;
				});

				rows.forEach(row => {
				  row.addEventListener('mouseenter', () => {
					clearTimeout(fadeTimeout);
					clearTimeout(selectionTimeout);

					fadeTimeout = setTimeout(() => {
					  // Set selection color
					  row.style.backgroundColor = '#d0e6ff';

					  // After short delay, fade back to original color
					  selectionTimeout = setTimeout(() => {
						row.style.backgroundColor = row.dataset.original;
					  }, 500); // how long the selection color stays before fading out

					}, 1000); // how long to wait before applying selection color
				  });
				});
				$(function () {
					$('.BSdelete').on('click', function () {
						deleteSpot=1;
						var tID = $(this).attr('id');
						if (tID.substring(0,1)=="b"){
							tID=tID.substring(1);
							   if (confirm('Delete one Spot from list?')){
								   $.post('/programs/deleteSpot.php',{id:tID},function(response){
									$("#modalA-body").html('<br>&nbsp;&nbsp;'+response+"<br><br>");
									$(".modalA-title").html("Delete Spot");
									  $("#myModalAlert").modal({show:true});//			  				alert(response);
									   getSpots(tFil,tNeed, tFilMode, tSortDir, tSort);
									var tFreqHi=tHiFreq;
									var scrollPixels=((tFreqHi-bsCenterFreq)/100)-400;
									$("#marker").html("");
									paintBS(bsCenterFreq,tBand);
									paintBSSpots(tBand);
									getBSFreqFromFreq(bsCenterFreq,tBand);
//									getBSFreq(tBand);
									$("#bs").animate({ scrollTop: scrollPixels });
									paintBSMarker(scrollPixels);
								   })
							   }
						 }
					 })
				 })

				$('.hClk').on('click', function() {
					var tIDV=$(this).attr('id');
					if (!(tSort==tIDV)){
						tSortDir='ASC';
					}else{
						if (tSortDir=='ASC'){
							tSortDir='DESC';
						}else{
							tSortDir='ASC';
						}
					}
					tSort=tIDV;
					pCount=1000;
					getSpots(tFil,tNeed, tFilMode, tSortDir, tSort);
					  $.post("/programs/SetSettings.php",{field: "SpotsSortDir", radio: tMyRadio, data: tSortDir, table: "MySettings"},function(response){
						  $.post("/programs/SetSettings.php",{field: "SpotsSort", radio: tMyRadio, data: tSort, table: "MySettings"},function(response){
						  });
					  });
				 });
			}

			function fillDescription(){
				$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'ClusterID', table: 'MySettings'}, function(response){
					var clusterID=response;
					$.post('/programs/GetCluster.php',{id: clusterID, field: 'NodeCall', table: 'Clusters'},function(response) {
						tClusterNode=response;
						var tFilx='';
						if (tFil=='followMe'){
							tFilx='Follow Me';
						}else if (tFil=='hf'){
							tFilx='HF';
						}else if (tFil=='vhfUHF'){
							tFilx='VHF-UHF';
						}else if (tFil=='allSpots'){
							tFilx='All Spots';
						}else if (tFil=='warc'){
							tFilx='WARC';
						}else if (tFil=='low'){
							tFilx='Low';
						}else if (tFil=='high'){
							tFilx='High';
						}else{
							tFilx=tFil+'M';
						}
						var tNeedx='no status';
						if (tNeed=='callWorked'){
							tNeedx="Call Worked";
						}else if (tNeed=='callConfirmed'){
							tNeedx="Call Confirmed";
						}else if (tNeed=='callWorkedBand'){
							tNeedx="Call Worked this Band";
						}else if (tNeed=='callConfirmedBand'){
							tNeedx="Call Confirmed this Band";
						}else if (tNeed=='entityWorked'){
							tNeedx="Entity NOT Worked";
						}else if (tNeed=='entityWorkedBand'){
							tNeedx="Entity NOT Worked this Band";
						}else if (tNeed=='entityConfirmed'){
							tNeedx="Entity NOT Confirmed";
						}else if (tNeed=='entityConfirmedBand'){
							tNeedx="Entity NOT Confirmed this Band";
						}
						var tModex='';
						if (tFilMode=='cw'){
							tModex='on CW';
						}else if (tFilMode=='phone'){
							tModex='on Phone';
						}else if (tFilMode=='follow'){
							tModex='on '+tCurMode;
						};
						var descr=tClusterNode+" SPOTS";
						$('#call').text(descr);
						descr=tFilx+': ' +tNeedx+' '+tModex;
						$('#descr').text(descr);
					});
				});
			}
			function myClickHandler(){

			}
			function clickHandler(e) {
				let target = e.target;

				  console.log("Initial target:", target);
				  // Climb up if it's not an Element (like if it's a Text Node)
				  while (target && target.nodeType !== 1) {
					target = target.parentNode;
				  }

				  // If somehow no valid element found, exit
				  if (!target) return;

				  const row = target.closest("tr");

				  if (row) {
					console.log("You clicked on row:", row);
					// Example: highlight row
//					row.classList.add("highlight");
				  }
return;
				}
/*				row = e.target.nodeType === 1 ? e.target.closest("tr") : e.target.parentElement?.closest("tr");

//				row = e.target?.closest?.("tr");
				  if (row) {
					console.log("Found row:", row);
				  }else{
					  return;
				  }

*//*				if (e.target.nodeType === 1) { // 1 === Element Node
					const row = e.target.closest("tr");
					if (row) {
					  console.log("Found row:", row);
					}
				  } else {
					console.log("Clicked something that isn't an element:", e.target);
				  }
//				}
*/
/*			  console.log("clicked");
			  clickedRow = row;// e.target.closest("tr");
//												if (!clickedRow) return;

												clearTimeout(clickTimeout);

												clickTimeout = setTimeout(() => {
			  //									if (scrollLock==10) {
			  //									  console.log("Skipping programmatic scroll — user is scrolling.");
			  //									  return;
			  //									}
												  allowScrollLock=false;
												  scrollLock = 0;
												  rowID=row.getAttribute('id');
												  clickMe(rowID);
			  //									cTable = document.getElementByID('logt1');


			  //		updatedRow = document.getElementById(clickedRow);
														  // Remove highlight from other rows
														  cTable.querySelectorAll("tr").forEach(r => {
															  r.classList.remove("highlight");
															  r.style.filter = "brightness(1)";
														  });

														  // Add highlight class
														  $(clickedRow).addClass("highlight");

														  // Animate brightness from 0.8 to 1
			  //											brightenRow(updatedRow);

														  // Scroll to center this row
			  //											cTable = document.getElementByID('logt1');

														  const container = cTable;
			  //											container.scrollTop = updatedRow.offsetTop - (container.clientHeight / 2) + (updatedRow.clientHeight / 2);
			  //									cTable.scrollTop = cTable.clientHeight;//;//$(clickedRow).offsetTop - (cTable.clientHeight / 2) + ($(clickedRow).clientHeight / 2);

												}, 200);
												allowScrollLock=true;
			  //});
											  };
//			}
			*/
			function getRowClosestToCenter(containerSelector, rowSelector) {
			  const container = document.querySelector(containerSelector);
			  var lowest=Infinity;
			  if (container){
			  const rows = container.querySelectorAll(rowSelector);

			  const containerCenter = container.scrollTop + (container.clientHeight / 2);
			  closestRow = null;
			  let closestDistance = Infinity;
			  rows.forEach(row => {
				const rowOffset = row.offsetTop + (row.clientHeight / 2);
				const distance = containerCenter - rowOffset;
				if (distance < closestDistance && distance >0) {
				  closestDistance = distance;
				  closestRow = row;
				  lowest=distance;
//				  console.log closestRow.id;
				}
			  });
			}else{
				closestRow=0;
			}
			  return closestRow ? closestRow.id : null;
			}

			function restripeTable() {
//			  const rows = document.querySelectorAll("table.striped tbody tr");
//			  rows.forEach((row, index) => {
//				row.style.backgroundColor = (index % 2 === 0) ? "#ffffff" : "#bae8f1";
//			  });
			}

			// Call restripeTable() after adding new rows
//			function myClickHandler(e) {
//			  console.log("clicked");
//			}


			function getSpots(tBandT,tNeedT, tModeT, tSortDirT, tSortT){
				var tFilterOut=getBandFilter(tBandT)
				var tFilEnc=tFilterOut;
				var tModeFilter=tModeT;
				let container1;
				const container = document.getElementById('sp');
				  const tableBody = document.querySelector('#sp tbody');
//				  const preserver = new ScrollCenterPreserver(container, document.getElementById('logt1'));

				if (tModeT=='follow'){
				   tModeFilter=tCurMode;
				}
				$.post('/programs/getSpotList.php',{radio: tMyRadio, folder: 'Inbox', order: 'Webdate', need: tNeedT, band: tFilEnc, mode: tModeFilter, direction: tSortDirT, sort: tSortT },function(response) {
					const preserver = new ScrollCenterPreserver(container, document.getElementById('logt'));

					if (scrollLock!==10){
//						container1 = document.getElementById('logt1').parentElement;  // assuming tbody is inside a div that scrolls
//						var centerOffset = container1.scrollTop + (container1.clientHeight / 2);
//						var scrollRatio = centerOffset / container1.scrollHeight;
//?						preserver.getCenterRow(container,document.getElementById('sp')); //?
//						rowID = getRowClosestToCenter('#logt1', 'tbody tr');
//};
//						preserver.preserveAndUpdate(() => {
						suppressPointerEvents = true;
						getSubSpots(response);
						restripeTable();
						suppressPointerEvents = false;
					}else{
						return;
					}

//					if (scrollLock==10){
//						preserver.getRowClosestToCenter(container,document.getElementById('logt1'));

//						var newCenterOffset = scrollRatio * container1.scrollHeight;
//						container1.scrollTop = newCenterOffset - (container1.clientHeight / 2);
//						scrollLock=0;
//						return;
//					}
					console.log("Got subspots");
				fillDescription();
				cTable = document.getElementById("tbody");  /////////
				cTable.addEventListener("pointerdown", () => {
					if (suppressPointerEvents) {
						e.stopPropagation();
						e.preventDefault();
						return;
					  }

				  userIsScrolling = true;
				});

				cTable.addEventListener("wheel", () => {
					if (suppressPointerEvents) {
						e.stopPropagation();
						e.preventDefault();
						return;
					  }

				  userIsScrolling = true;
				});

/**
				  cTable.addEventListener("scroll", (e) => {
					  if (userIsScrolling) {
						  console.log("Manual scroll detected!");
						  userIsScrolling = false; // Reset for next
						  return
						}

									  console.log("wanted to scroll");
				  //					return;
									console.log("User is scrolling..."+allowScrollLock);
				  //				  if (allowScrollLock){
										console.log("but allowscrolllock=" + allowScrollLock);
									scrollLock = 10;
									clearTimeout(scrollTimer);
									scrollTimer = setTimeout(() => {

									  scrollLock = scrollLockOld;
				  //									   cTable.scrollTop = cTable.clientHeight / 2;
//				  console.log("row now " + row.getAttribute('Mode'));
									  console.log("User stopped scrolling — program scrolls allowed again. Scrolllock now " + scrollLock);
									  return;

									}, 1000);
								});
*/
				row=document.getElementById(rowID);
				if (row){
					cTable.scrollTop = row.offsetTop - (cTable.clientHeight / 2) + (row.clientHeight / 2);
				}

				allowScrollLock=true;
				// Remove
			if (cTable){
//				cTable=document.getElementById("logt1");
///				cTable.removeEventListener("click",clickHandler);
//				cTable.removeEventListener("dblclick", myClickHandler);
//				cTable.removeEventListener("scroll", myClickHandler);
				// Scroll event
/*
//				cTable.addEventListener("scroll", () => {
//					console.log("wanted to scroll");
//					return;
//				  console.log("User is scrolling..."+allowScrollLock);
//				  if (allowScrollLock){
//					  console.log("but allowscrolllock=" + allowScrollLock);
//				  scrollLock = 10;
//				  clearTimeout(scrollTimer);
//				  scrollTimer = setTimeout(() => {

//					scrollLock = scrollLockOld;
//									   cTable.scrollTop = cTable.clientHeight / 2;
//console.log("row now " + row.getAttribute('Mode'));
//					console.log("User stopped scrolling — program scrolls allowed again. Scrolllock now " + scrollLock);
//				  }, 5000);
//?				  return;
//			  });
*/
			// Click event
/*			function brightenRow(row) {
				  let brightness = 0.6;
				  const target = 1;
				  const duration = 500;
				  const startTime = performance.now();

				  function animate(time) {
					const elapsed = time - startTime;
					const progress = Math.min(elapsed / duration, 1);
					const eased = 1 - Math.pow(1 - progress, 2);

					row.style.filter = `brightness(${brightness + (target - brightness) * eased})`;

					if (progress < 1) {
					  requestAnimationFrame(animate);
					}
				  }
				  requestAnimationFrame(animate);
			  }
*/
				function highlightAndScroll(row) {
								// Remove highlight from other rows
								cTable.querySelectorAll("tr").forEach(r => {
									r.classList.remove("highlight");
									r.style.filter = "brightness(1)";
								});

								// Add highlight class
//..								$(row).addClass("highlight");

								// Animate brightness from 0.8 to 1
				  let brightness = 0.6;
								  const target = 1;
								  const duration = 500;
								  const startTime = performance.now();

								  function animate(time) {
									const elapsed = time - startTime;
									const progress = Math.min(elapsed / duration, 1);
									const eased = 1 - Math.pow(1 - progress, 2);

									row.style.filter = `brightness(${brightness + (target - brightness) * eased})`;

									if (progress < 1) {
									  requestAnimationFrame(animate);
									}
								  }
								  requestAnimationFrame(animate);

								// Scroll to center this row
								const container = cTable;
								container.scrollTop = row.offsetTop - (container.clientHeight / 2) + (row.clientHeight / 2);
							}


//					}
//								cTable.addEventListener("click", clickHandler(event));
//									myClickHandler(event);

//							});
		// Double-click event

							  // Focus container
											cTable.focus();
											var cTable1 = document.getElementById("sp");
											let data = response;

											let match = data.match(/TCOUNT:\s*(\d+)/);
												if (match) {
												  spotCount = parseInt(match[1]);
												  console.log(spotCount);
												}

												if (spotCountOld !== spotCount && spotCount > 0) {
												  spotCountOld = spotCount;
												  document.getElementById("sp").classList.add("glow-on");
//												  cTable1.classList.add("glow-on");
														  setTimeout(() => {
											cTable1.classList.remove("glow-on");
//											cTable1.classList.add("glow-off");


													}, 500);
											}

											// Handle scrolling based on scrollLock value
											if (scrollLock == 0) {
											  if (rowID > 0) {
												  rowID=rowIDSelected;
												var updatedRow = document.getElementById(rowID);
												if (updatedRow) {
												  // Scroll to selected row
												  cTable.scrollTop = updatedRow.offsetTop - (cTable.clientHeight / 2) + (updatedRow.clientHeight / 2);
												  $(updatedRow).addClass('highlight').siblings().removeClass('highlight');

												}else{
													scrollLock=4;
												}
											  }
											  var updatedRow = document.getElementById(rowID4);
											  if (updatedRow) {
												$(updatedRow).addClass('centerHighlight').siblings().removeClass('centerHighlight');
											}

										  }
											  if (scrollLock==4){
							  //					const rows = document.querySelectorAll("#logt-wrapper tr");
							  //					  const middleIndex = Math.floor(rows.length / 2);
							  //					  const centerRow = rows[middleIndex];
							  //					  rowID = centerRow.getAttribute("id");
											  if (rowID4){
												  cTable = document.getElementById("tbody");  //?
												  console.log("rowID4: "+rowID4);
														updatedRow = document.getElementById(rowID4);
												if (updatedRow){
													cTable.scrollTop = updatedRow.offsetTop - (cTable.clientHeight / 2) + (updatedRow.clientHeight / 2);
													$(updatedRow).addClass('centerHighlight').siblings().removeClass('centerHighlight');
											  }
											}
											if (rowID){
												updatedRow = document.getElementById(rowID);

//...												$(updatedRow).addClass('highlight').siblings().removeClass('highlight');

											}
											} else if (scrollLock == 1) {
											  // Scroll to bottom
											  cTable.scrollTop = cTable.scrollHeight;
											} else if (scrollLock == 2) {
											  // Scroll to top
											  cTable.scrollTop = 0;
											}

//							  return;

							  };
//						  });
						  });
					  };
//				})

//})}



			$(document).on('click', '#connectButton', function() {
				if (tNoInternet==1){
					$("#modalA-body").html("<br>&nbsp;&nbsp;RSS cannot reach the Internet so can't connect to the Telnet site for spots.<br><br>");
					$(".modalA-title").html("No Internet");
					  $("#myModalAlert").modal({show:true});
				}else{
					if (tCall.toLowerCase()=='admin'){
						$("#modalA-body").html("<br>&nbsp;&nbsp;Please enter your call in SETTINGS>Accounts>Account Editor. Spots NOT started.<br><br>");
						$(".modalA-title").html("Spots Status");
						  $("#myModalAlert").modal({show:true});
					}else{
						  $.post('./programs/SpotsStart.php', {action: 'start', radio: tMyRadio, call: tCall}, function(response) {
							  if (response.indexOf("Not")>0){
								$.post("/programs/SetSettings.php", {field: "ClusterConnected", radio: tMyRadio, data: 0, table: "MySettings"});
								$("#connectButton").addClass('btn-small-lock-on');
								$("#modalA-body").html("<br>&nbsp;&nbsp;Connection to " + tClusterNode + " failed.  Try Another spot server.<br><br>");
								$(".modalA-title").html("Spots Status");
								$("#myModalAlert").modal({show:true});
								}else{
								$.post("/programs/SetSettings.php", {field: "ClusterConnected", radio: tMyRadio, data: 1, table: "MySettings"});
								  $("#connectButton").addClass('btn-small-success-on');
								  $("#modalA-body").html('<br>&nbsp;&nbsp;'+tClusterNode + " Connection OK.<br><br>");
								  $(".modalA-title").html("Spots Status");
								  $("#myModalAlert").modal({show:true});
							  };
						});
					};
				};
			 });

			$(document).on('click', '#disconnectButton', function() {
				   $.post('/programs/SpotsStart.php', {action: 'stop', radio: tMyRadio, call: tCall}, function(response) {
				 if (response.indexOf('Warning')>0)
				 {
					 $("#connectButton").removeClass('btn-small-success-on');
					 $("#connectButton").addClass('btn-small-lock-on');
					 $("#modalA-body").html('<br>&nbsp;&nbsp;Error disconnecting.<br><br>');
					 $(".modalA-title").html("Spots Status");
					   $("#myModalAlert").modal({show:true});
					   //			  				alert(response);
				 }else{
					 $("#connectButton").removeClass('btn-small-success-on');
				  $("#connectButton").addClass('btn-small-lock-on');
				  $("#modalA-body").html('<br>&nbsp;&nbsp;'+tClusterNode + ' is disconnected.<br><br>');
				  $(".modalA-title").html("Spots Status");
					$("#myModalAlert").modal({show:true});

				 };
			 });
			});
			$(document).on('click', '#deleteButton', function() {
				   $.post('/programs/SpotsStart.php', {action: 'delete', radio: tMyRadio, call: tCall}, function(response) {
					   getSpots(tFil,tNeed, tFilMode, tSortDir, tSort);

				 });
			 });
	</script>
</head>

<body class="body-black-scroll" id="spots">
	<?php require $dRoot . "/includes/header.php"; ?>
   <div class="container-fluid body-black">
		<div class="row" style="margin-top:10px;">
			<div class="col-12 col-md-12 btn-padding">
				<span class="label label-success text-white" id="call" style="cursor: default; text-center; margin-top:10px;"></span>
			</div>
		</div>
		<div class="row">
			<div class="col-12 col-md-4 text-center text-spacer">
				<button class="btn btn-outline-success btn-sm my-1 my-sm-0 text-white" style="width:125px;" id="connectButton"
					title="Click to connect to Cluster" type="button">
					<i class="fas fa-play"></i>
					Connect
				</button>
				<button class="btn btn-outline-danger btn-sm my-1 my-sm-0 text-white" style="width:150px;" id="disconnectButton"
					title="Click to disconnect from Cluster" type="button">
					<i class="fas fa-stop"></i>
					Disconnect
				</button>
				<button class="btn btn-outline-danger btn-sm my-1 my-sm-0 text-white" style="width:140px;"  id="deleteButton"
					title="Click to delete all spots" type="button">
					<i class="fas fa-trash-alt fa-fw"></i>
					Delete <u>A</u>ll
				</button>
			</div>
			<div class="col-12 col-md-2 btn-padding">
				<span class="label label-success text-white" id="descr" style="cursor: default; text-center; margin-top:10px;"></span>
			</div>
<div class="col-12 col-md-3 btn-padding">
				<div class="dropdown" style="text-center; margin-top:10px;">
					<button class="btn btn-color dropdown-toggle hButton" id="pgNum" data-size="3" type="button"
						title="Choose Band Filter" data-toggle="dropdown"><i class="fas fa-filter fa-lg"></i>
					</button>
					<ul class="dropdown-menu menu-scroll" id="fnList">
						<li role="presentation" class="dropdown-header">Band Filter</li>
						<li class="band" id='allSpots'><a class='dropdown-item ' id='fn' href='#'>All Spots</a></li>
						<li class="band" id='followMe'><a class='dropdown-item ' id='fn' href='#'>Follow Me</a></li>
						<li class="band" id='hf'><a class='dropdown-item ' id='fn' href='#'>HF</a></li>
						<li class="band" id='low'><a class='dropdown-item ' id='fn' href='#'>Low Bands</a></li>
						<li class="band" id='high'><a class='dropdown-item ' id='fn' href='#'>High Bands</a></li>
						<li class="band" id='warc'><a class='dropdown-item ' id='fn' href='#'>WARC</a></li>
						<li class="band" id='vhfUHF'><a class='dropdown-item ' id='fn' href='#'>VHF/UHF</a></li>
						<li class="band" id='160'><a class='dropdown-item ' id='fn' href='#'>160</a></li>
						<li class="band" id='80'><a class='dropdown-item ' id='fn' href='#'>80</a></li>
						<li class="band" id='60'><a class='dropdown-item ' id='fn' href='#'>60</a></li>
						<li class="band" id='40'><a class='dropdown-item ' id='fn' href='#'>40</a></li>
						<li class="band" id='30'><a class='dropdown-item ' id='fn' href='#'>30</a></li>
						<li class="band" id='20'><a class='dropdown-item ' id='fn' href='#'>20</a></li>
						<li class="band" id='17'><a class='dropdown-item ' id='fn' href='#'>17</a></li>
						<li class="band" id='15'><a class='dropdown-item ' id='fn' href='#'>15</a></li>
						<li class="band" id='12'><a class='dropdown-item ' id='fn' href='#'>12</a></li>
						<li class="band" id='10'><a class='dropdown-item ' id='fn' href='#'>10</a></li>
						<li class="band" id='6'><a class='dropdown-item ' id='fn' href='#'>6</a></li>
						<li class="band" id='2'><a class='dropdown-item ' id='fn' href='#'>2</a></li>
						<li role="presentation" class="dropdown-header">Mode Filter</li>
						<li class="mode" id='all'><a class='dropdown-item ' id='fn' href='#'>Show All</a></li>
						<li class="mode" id='follow'><a class='dropdown-item ' id='fn' href='#'>Follow Me</a></li>
						<li class="mode" id='cw'><a class='dropdown-item ' id='fn' href='#'>Only CW</a></li>
						<li class="mode" id='phone'><a class='dropdown-item ' id='fn' href='#'>Only Phone</a></li>
						<li class="mode" id='digital'><a class='dropdown-item ' id='fn' href='#'>Only Digital</a></li>
					</ul>
				</div>
				<div class="dropdown">
					<button class="btn btn-color dropdown-toggle hButton" id="selStyle" data-size="3" type="button"
						title="Choose Worked Status" data-toggle="dropdown"><i class="fas fa-paint-brush fa-lg"></i>
					</button>
					<ul class="dropdown-menu menu-scroll" id="fnList">
						<li class="needed" id='noColor'><a class='dropdown-item' id='fn' href='#'>
							No color</a></li>
						<li class="needed" id='callWorked'><a class='dropdown-item' id='fn' href='#'>
							Teal: Call Worked</a></li>
						<li class="needed" id='callConfirmed'><a class='dropdown-item' id='fn' href='#'>
							Green: Call Confirmed</a></li>
						<li class="needed" id='callWorkedBand'><a class='dropdown-item ' id='fn' href='#'>
							Orange: Call Worked: this Band</a></li>
						<li class="needed" id='callConfirmedBand'><a class='dropdown-item ' id='fn' href='#'>
							Red: Call Confirmed: this Band</a></li>
						<li class="needed" id='entityWorked'><a class='dropdown-item ' id='fn' href='#'>
							Teal: Entity NOT Worked</a></li>
						<li class="needed" id='entityConfirmed'><a class='dropdown-item ' id='fn' href='#'>
							Green: Entity NOT Confirmed</a></li>
						<li class="needed" id='entityWorkedBand'><a class='dropdown-item ' id='fn' href='#'>
							Orange: Entity NOT Worked: this Band</a></li>
						<li class="needed" id='entityConfirmedBand'><a class='dropdown-item ' id='fn' href='#'>
							Red: Entity NOT Confirmed: this Band</a></li>
					</ul>
				</div>
			</div>
			  <div class="col d-none d-lg-block col-lg-2 text-spacer">
				<div class='text-white' id='BSFreqDisp'>14.025.333</div>
			  </div>
		</div>
		<div class="row" style="margin-top:10px;">
			<div class="full-viewport-height col-xs-12 col-lg-9" id='sp' style="margin-right:0px; padding-right:0px; height:800px;" >
				<div  id='tbody'></div>
			  </div>
			  <div class="col-xs-12 col-lg-3" id='bs' style="overflow-x: hidden;height:800px;">
				  <div class="row">
					  <div class="col">
						  <img src="./Images/BandMapperG.jpg" class="img rounded" id="bandCanvas" />
						<div id='frequencies'></div>
						<div id='spots_data'></div>
						<div id='marker'></div>
					  </div>
				  </div>
		</div>
	</div>
	<?php require $dRoot . "/includes/footer.php"; ?>
		<br />
<div class="modal fade" id="myModal" tabindex="0">
			<div class="modal-dialog modal-sm">
				<div class="modal-content">
					<!-- Modal Header -->

					<div class="modal-header">
						<h1 class="modal-title" id="dxcall"></h1><button type="button" class="close" id = "closeModal" data-dismiss="modal">&times;</button>
					</div>
					<!-- Modal body -->
					<div class="modal-body"></div>
					<!-- Modal pix -->
					<div class="mobile-header" id="pixFrame"><img class='modal-pix' height='' width='' src=''></div>
					<!-- Modal footer -->
					<div class="modal-footer">
						<h6 class="angle" style="margin-top:4px"></h6>
						<button type="button" class="btn btn-success" id="rotate" >Rotate</button>
						<button type="button" class="btn btn-danger" id="stop" >Stop</button>
						<button type="button" class="btn btn-secondary" id="modalClose" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
	<?php require $dRoot . "/includes/modal.txt"; ?>
		  <?php require_once $dRoot . "/includes/modalCancelOnlySpots.txt"; ?>
	  <?php require $dRoot . "/includes/modalAlert.txt"; ?>

</body>
<script src="/Bootstrap/popper.min.js" type="text/javascript"></script>
<script src="/Bootstrap/jquery-ui.js" type="text/javascript"></script>
</script><script src="./Bootstrap/bootstrap.min.js" type="text/javascript"></script>
<script src="/js/nav-active.js"></script>
<script type="text/javascript" src="/js/jquery.line.js"></script>

</html>
