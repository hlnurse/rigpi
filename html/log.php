<?php
session_start();
$tUserName=$_SESSION['myUsername'];
$tCall=$_SESSION['myCall'];
$dRoot = "/var/www/html";
require_once($dRoot . "/classes/Membership.php");
$membership = new Membership();
//	echo $tUserName;
 $membership->confirm_Member($tUserName);
?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<html lang="en">
<head>
	<meta charset="utf-8">

	<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
	Remove this if you use the .htaccess -->
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo $tCall ?> Log</title>
	<meta name="description" content="">
	<meta name="author" content="Howard Nurse">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
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
require($dRoot . "/includes/styles.php");
 require($dRoot . "/programs/getLogList.php");
  require($dRoot . "/programs/getLogStyles.php");
 require($dRoot . "/programs/GetSettingsFunc.php");
 require($dRoot . "/programs/sqldata.php");
require_once($dRoot . "/classes/MysqliDb.php");
 $db = new MysqliDb(
   "localhost",
   $sql_radio_username,
   $sql_radio_password,
   $sql_radio_database
 );
 $db->where("Username", $tUserName);
 $row = $db->getOne("Users");
 $tRadio = 1; //$row["SelectedRadio"];
 $logName = GetField($tRadio, "LogName", "MySettings");
 $logStyle = GetField($tRadio, "LogStyle", "MySettings");

 if (!empty($_POST["page"])) {
   $tPage = $_POST["page"];
 } else {
   $tPage = GetField($tRadio, "LogPage", "MySettings");
 }
 if (!empty($_POST["order"])) {
   $tOrder = $_POST["order"];
 } else {
   $tOrder = GetField($tRadio, "LogSort", "MySettings");
 }
 if (!empty($_POST["direction"])) {
   $tDir = $_POST["direction"];
 } else {
   $tDir = GetField($tRadio, "LogSortDir", "MySettings");
 }

//		$logName='Main';
//		$logStyle='General';
//		$tPage=1;
//		$tOrder='Callsign';
//		$tDir='ASC';
?>

	<script type="text/javascript">
	 var sOrderG=<?php echo "'" . $tOrder . "'"; ?>;
	 var sDirG=<?php echo "'" . $tDir . "'"; ?>;
	 var sDirNext=<?php echo "'" . $tDir . "'"; ?>;
	 var curPage=<?php echo "'" . $tPage . "'"; ?>;
	 var searchCall='';
	 var filteredNow=false;
	 var filteredByNow='';
	 var dlfile='';
// 	var logName=<?php echo "'" . $logName . "'"; ?>;
// 	var logStyle="General";
	var logName="<?php echo $logName; ?>";
	// 20200525 Rob KI4MCW - Get Style to carry over into editor and new contact entry.
	// var logStyle="General";
	var logStyle="<?php echo $logStyle; ?>";
	// end 20200525 KI4MCW 
	var tMyRadio=<?php echo "'" . $tRadio . "'"; ?>;
	var aChk;
	var gettingLog=false;
	var tUserName="<?php echo $tUserName; ?>";
	var disconnected=1;
//	var tUserName=<?php echo "'" . $tUserName . "'"; ?>;
	var tUser='';
	var tRowCount=0;
//	var tMyCall=<?php echo "'" . $tCall . "'"; ?>;
	var tMyCall="<?php echo $tCall ; ?>";
	var tCall=tMyCall;
	var getUploadCount=0;
	var getDownloadCount=0;
	var tableCount=0;
	var filterCall=0;
	var input="";
	var speedPot=0;
	var formdata = false;
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

		$.post('./programs/GetSelectedRadio.php', {un:tUserName}, function(response)
		{
			tMyRadio=response;
			input = document.getElementById("input_file");


			$(document).keydown(function(event) {
				if (event.keyCode==13 ){
					$("#searchButton").click();
				}

				if (event.keyCode == 27) { 
					document.getElementById('closeModal').click();
				}
			});

			$.post('/programs/GetUserField.php',{un: tUserName, field: 'uID'}, function(response) {
				tUser=response;
			})

		  
			$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response){
				$('#searchText').val(response.toUpperCase());
				searchCall=response.toUpperCase();
				filteredNow=false;
				if (logName!="ALL Logs"){
					filteredNow=true;
				}
				getLog(searchCall,1,filteredNow,logName);
			});
		})


		$(document).on('click', '.mypages', function() {
			var curPage = $(this).text();
				$.post("/programs/SetSettings.php",{field: "LogPage", radio: tMyRadio, data: curPage, table: "MySettings"});
			getLog(searchCall,curPage,filteredNow,filteredByNow);
		});

		$(document).on('click', '#selAll', function() {
			if($('#selAll:checked').val()=='on'){
				  $.post('./programs/putLog.php', {record:'0',field:'Sel',data:'1',filter: ''}, function(response) {
					  getLog(searchCall,"1",filteredNow,filteredByNow);
					$("#modalA-body").html(response);			  				
					$("#modalA-title").html("Log Selections");
					  $("#myModalAlert").modal({show:true});//			  				alert(response);
					  $('#myModalAlert').on('hidden.bs.modal', function (e) {
						  document.getElementById("selAll").checked = true;
					});	
				 });
			}else{
				  $.post('./programs/putLog.php', {record:'0',field:'Sel',data:'0',filter: ''}, function(response) {
					  getLog(searchCall,"1",filteredNow,filteredByNow);
					$("#modalA-body").html(response);			  				
					$("#modalA-title").html("Log Selections");
					  $("#myModalAlert").modal({show:true});//			  				alert(response);
					  $('#myModalAlert').on('hidden.bs.modal', function (e) {
						  document.getElementById("selAll").checked = false;
					});	
				 });
			}
		})

		//the following 2 functions would normally be imported with modalLoad.js, but additional code required to filter log here
		$(document).on('click', '#searchButton', function()
		{
			doSearch();
		 });
		 
		$("input").bind("keydown", function(event) 
		{
			// track enter key
			var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
			if (keycode == 13) { // keycode for enter key
				var tDX=$('#searchText').val().toUpperCase();
				$('#searchText').val(tDX);
				if (!$('#myModal').is(':visible')){
					doSearch();
				}
					
				$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tDX, table: "MySettings"});
				return false;
			} else  {
				return true;
			}
		});

		function doSearch(){
			 var tDX=$('#searchText').val().toUpperCase();
			 $('#searchText').val(tDX);
			searchCall=tDX;
			if (tDX.length==0){
				getLog(tDX,"1",true,filteredByNow);
				return;
			}
			if (!~tDX.indexOf("*")&&!~tDX.indexOf("=")){
				$.post("./programs/GetCallbook.php", {call: tDX, what: 'QRZData', user: tUser, un: tUserName},function(response){
					$(".modal-body").html(response);
					$.post("./programs/GetCallbook.php", {call: tDX, what: 'QRZpix', user: tUser, un: tUserName},function(response){
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
						$('.modal-title').html(tDX);
						  $('#myModal').modal({show:true});
						  $('#myModal').focus();
					  });
				});
				$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tDX, table: "MySettings"});
			}
		}
	
			$(document).keydown(function(event) {
				if (event.keyCode == 27) { 
					document.getElementById('closeModal').click();
				}
			});
////////////////////////

		$('.custom-file-input').change(function() { 
			if (window.FormData) {
				formdata = new FormData();
			}
		   let fileName = $(this).val().split('\\').pop(); 
		   $(this).next('.custom-file-label').addClass("selected").html(fileName); 
		});
		
		$("#input_file").change(function(event){
			var i = 0, len = this.files.length, img, reader, file;
			//console.log('Number of files to upload: '+len);
			$('#ulresult').html('');
				 file = this.files[0];
				 var filel=file.name.toLowerCase();
				if(!!filel.match(/.*\.adi$/)){
					if (formdata) {
						formdata.append("files[]", file);
					}
				} else {
					$("#input_file").val('').prop('disabled',false);
							$("#modalA-body").html("<br>" + file.name+" is not an ADIF file!<br><br>");			  				
							$("#modalA-title").html("Incorrect Format");
								  $("#myModalAlert").modal({show:true});
								  setTimeout(function(){ 
									  $("#myModalAlert").modal('hide');
								 },
								  2000);
								return;
							}
		});
		
		$('.importClose').on('click',function(event){
			$('.custom-file-label').text('Choose file');
			$('#ulresult').html('');
			$('#ulnumber').html('');
			getUploadCount=0;
		}); 

		$('.topClose').on('click',function(event){
			$('.custom-file-label').text('Choose file');
			$('#ulresult').html('');
			$('#ulnumber').html('');
			getUploadCount=0;
		}); 

							 
		$('#btn_submit').on('click',function(event){ 
			if (formdata) {
				$.ajax({
					url: "./my/upload.php",
					type: "POST",
					data: formdata,
					cache: false,
					processData: false,
					contentType: false, // this is important!!!
					success: function (res) {
						var result = JSON.parse(res);
						$("#input_file").val('').prop('disabled',false);
						if(result.res === true){
							var buf=result.data[0];
							if (buf){
								var tLogname=logName;
								if (tLogname==''){
									tLogname="Main";
								}
								getUploadCount=1;
								$.post('./programs/importADIF.php',{file:buf,logname:tLogname,uid:tUser},function(response){
									formdata = false;
									formdata = new FormData();
									getLog(searchCall,"1", filteredNow,filteredByNow);
									$('.custom-file-label').text('Choose file');
								});
							}else{
								$('#ulresult').html("<br>&nbsp;&nbsp;File not specified.<br><br>");
								getUploadCount=0;
							}
						} else {
							$('#ulresult').html(result.data);
							getUploadCount=0;
						}
					}
				});
			}
			return false;
		});
		function showConnectAlert(){
			$("#modalA-body").html("<br>The radio is not connected.<p><p>");			  				
			$("#modalA-title").html("Radio Connection");
			  $("#myModalAlert").modal({show:true});
			  setTimeout(function(){ 
				  $("#myModalAlert").modal('hide');
			 },
			  2000);
			return;
		}

		$(document).on('click', '#stop', function(){ 
			$.post("./programs/SetMyRotorBearing.php", {w: "stop", i: tMyRadio, a: "1"});
		});	
		
		$(document).on('click', '#rotate', function(){ 
			if (disconnected==1){
				showConnectAlert();
				return false;
			}
			var dx=$('#searchText').val();//.toUpperCase();
			$.post("./programs/GetCallbook.php", {call: dx, what: 'Bearing', user: tUser, un: tUserName},function(response){
				if(confirm('Rotate to '+dx+' at '+response+' degrees?') ){
					$.post("./programs/SetMyRotorBearing.php", {w: "turn", i: tMyRadio, a: response});
				}
			});			
		});	
		
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
			$.post("./programs/GetTableCount.php", {Logname: logName},function(response){
				var tCount=response;
				if (tCount != tableCount){
					tableCount=tCount;
					var tF=filteredByNow;
					getLog('',"1",filteredNow,tF);
				}
			});	
/*			$.post('./programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKPot'}, function(response) {
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
*/			
		}
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
			   
			  if (tAData[8] !== "NG" &&  tAData[8] !== "") {
				var tRadioUpdate = "<br>"+tAData[8]+"<br><br>";
				if (tRadioUpdate.length != 0) {
				  $("#modalA-body").html(tRadioUpdate);
				  $("#modalA-title").html("RigPi Report");
						  $("#myModalAlert").modal({show:true});
						  setTimeout(function(){ 
							  $("#myModalAlert").modal('hide');
						 },
						  2000);
//					}
				  $.post("/programs/SetSettings.php", {
					field: "RadioData",
					radio: tMyRadio,
					data: "",
					table: "RadioInterface",
				  });
				}
			  }
			  if (!$.isNumeric(tAData[0])) {
				  disconnected=1
				tAData[0] = "00000000";
				tAData[3] = "";
				tAData[2] = "00000000";
			  }else{
				  disconnected=0;
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
		
		var tFUpdate = setInterval(freqTimer, 1000);
		function freqTimer(){
			updateFooter();
			if (getDownloadCount==1 || getUploadCount==1){
				$.post('/programs/GetUserField.php',{un: tUserName, field: 'Count'}, function(response){
					if (getDownloadCount==1){
						$("#dlnumber").text("Records: "+response);
					}else{
						if (response.length>0){
							$("#ulnumber").text("Records: "+response);
						}else{
							$("#ulnumber").text("Done");
						}
					}
				})
			}
		};

	window.pageCount=1;

	$(document).on('click', '.export', function() {
		var tAsk='';
		var tF=filteredByNow;
		if (filteredNow==false){
			tAsk='Export ALL QSOs?';
			tF='x';
		}else{
			tAsk='Export ALL QSOs from log named '+filteredByNow+'?';
		}
		if (confirm(tAsk)){
			$('#downloadModal').modal('show'); 
			getDownloadCount=1;
			$.post("./programs/exportADIF.php",{sel:0,log:tF,call:tMyCall,type:'full',uid:tUser}, function(response){
				if (response=="0"){
					$('#dlresult').html("<br>No records found to export.<br><br>");
					getDownloadCount=0;
				}else{
					
					var dlsrc = "/my/downloads/"+response;
					document.getElementById("downloadFrame").src = dlsrc;
					getDownloadCount=0;
				}
				$('#dlresult').html("<br>ADIF download: "+response+"<br><br>");
			})
		}
	})

	$(document).on('click', '.exportsel', function() {
		var tF=filteredByNow;
		var tAsk;
		if (filteredNow==false){
			tAsk='Export ALL selected QSOs for LoTW?';
			tF='x';
		}else{
			tAsk='Export selected QSOs for LoTW from log named '+filteredByNow+'?';
		}
		if (confirm(tAsk)){
			$('#downloadModal').modal('show'); 
			getDownloadCount=1;
			$.post("./programs/exportADIF.php",{sel:1,log:tF,call:tMyCall,type:'full',uid:tUser}, function(response){
				if (response=="0"){
					$('#dlresult').html("<br>No records found to export.<br><br>");
					getDownloadCount=0;
				}else{
					var dlsrc = "/my/downloads/"+response;
					document.getElementById("downloadFrame").src = dlsrc;
					getDownloadCount=0;
				}
				$('#dlresult').html("<br>ADIF download: "+response+"<br><br>");
			})
		}
	})

	$(document).on('click', '.exportlotw', function() {
		var tF=filteredByNow;
		var tAsk='';
		if (filteredNow==false){
			tAsk='Export ALL QSOs for LoTW?';
			tF='x';
		}else{
			tAsk='Export QSOs for LoTW from log named '+filteredByNow+'?';
		}
		if (confirm(tAsk)){
			$('#downloadModal').modal('show'); 
			getDownloadCount=1;
			$.post("./programs/exportADIF.php",{sel:0,log:tF,call:tMyCall,type:'lotw',uid:tUser}, function(response){
				if (response=="0"){
					$('#dlresult').html("<br>No records found to export.<br><br>");
				}else{
					var dlsrc = "/my/downloads/"+response;
					document.getElementById("downloadFrame").src = dlsrc;
				}
				getDownloadCount=0;
				$('#dlresult').html("<br>ADIF download: "+response+"<br><br>");
			})
		}
	})

	$(document).on('click', '.exportsellotw', function() {
		var tF=filteredByNow;
		var tAsk='';
		if (filteredNow==false){
			tAsk='Export ALL selected QSOs for LoTW?';
			tF='x';
		}else{
			tAsk='Export selected QSOs for LoTW from log named '+filteredByNow+'?';
		}
		if (confirm(tAsk)){
			$('#downloadModal').modal('show'); 
			getDownloadCount=1;
			$.post("./programs/exportADIF.php",{sel:1,log:tF,call:tMyCall,type:'lotw',uid:tUser}, function(response){
				if (response=="0"){
					$('#dlresult').html("<br>No records found to export.<br><br>");
				}else{
					var dlsrc = "/my/downloads/"+response;
					document.getElementById("downloadFrame").src = dlsrc;
				}
				getDownloadCount=0;
				$('#dlresult').html("<br>ADIF download: "+response+"<br><br>");
			})
		}
	})

	$(document).on('click', '.import', function() {
		$('#uploadModal').modal('show'); 
	});

	$(document).on('click', '.deleteall', function() {
		var tAsk='';
		var tF=filteredByNow;
		if (filteredNow==false){
			tAsk='Delete ALL QSOs from ALL logs?';
			tF='x';
		}else{
			tAsk='Delete ALL QSOs from log named '+tF+'?';
		}
		   if (confirm(tAsk)){
			   $.post("./programs/deleteQSO.php",{log:tF,which:'all',id:0},function(response)
			   {
				getLog('',"1",filteredNow,tF);
			});
		   }
	});

	$(document).on('click', '.deletesel', function() {
		var tAsk='';
		var tF=filteredByNow;
		if (filteredNow==false){
			tAsk='Delete selected QSOs from ALL logs?';
			tF='x';
		}else{
			tAsk='Delete selected QSOs from log named '+tF+'?';
		}
		   if (confirm('Delete SELECTED QSOs from log?')){
			   $.post("./programs/deleteQSO.php",{log:tF,which:'selected',id:0},function(response){
				   getLog('',"1",filteredNow,tF);
					$("#modalA-body").html("<br>"+response+"<br><br>");			  				
				   $("#modalA-title").html("Delete Contacts");
					 $("#myModalAlert").modal({show:true});
					 setTimeout(function(){ 
						 $("#myModalAlert").modal('hide');
					},
					 2000);
			});
		   }
	});

	$(document).on('click', '#next', function() {
		curPage=parseInt(curPage)+1;
		if (curPage>pageCount){
			curPage=pageCount;
			$("#modalA-body").html("<br>Already viewing last page (of "+pageCount+")<br><br>");
						$("#modalA-title").html("Log Navigate");
			  $("#myModalAlert").modal({show:true});
			  setTimeout(function(){ 
				  $("#myModalAlert").modal('hide');
			 },
			  2000);
			  return;
//			alert("Already viewing last page (of "+pageCount+").");
		}
		if (!(sDirG==sDirNext)){
			if (sDirG=='DESC'){
				sDirNext='ASC';
			}else{
				sDirNext='DESC';
			}
			sDirG=sDirNext;
		}
		getLog(searchCall,curPage,filteredNow,filteredByNow);
	})

	$(document).on('click', '#end', function() {
		curPage=pageCount;
		getLog(searchCall,curPage,filteredNow,filteredByNow);
	})

	$(document).on('click', '#previous', function() {
		curPage=parseInt(curPage)-1;
		if (curPage<1){
			curPage=1;
			$("#modalA-body").html("<br>Already viewing first page (of "+pageCount+").<br><br>");			$("#modalA-title").html("Log Navigate");
			  $("#myModalAlert").modal({show:true});
			  setTimeout(function(){ 
				  $("#myModalAlert").modal('hide');
			 },
			  2000);
			  return;
		}
		if (!(sDirG==sDirNext)){
			if (sDirG=='DESC'){
				sDirNext='ASC';
			}else{
				sDirNext='DESC';
			}
			sDirG=sDirNext;
		}
		getLog(searchCall,curPage,filteredNow,filteredByNow);
	})

	$(document).on('click', '#start', function() {
		curPage=1;
		getLog(searchCall,curPage,filteredNow,filteredByNow);
	})

	$(document).on('click', '#newQSO', function() {
//		 open('POST', './logEditor.php', {c: tMyCall, x: tUserName, id: '0', radio: tMyRadio, what: 'edit', style: '', target: '_self'});
		// 20200525 Rob KI4MCW - Get Style to carry through to editor, new contact entry.
		// open('POST', './logEditor.php', {c: tMyCall, x: tUserName, id: '0', radio: tMyRadio, what: 'edit', style: '', target: '_self'});
		open('POST', './logEditor.php', {c: tMyCall, x: tUserName, id: '0', radio: tMyRadio, what: 'edit', style: logStyle, target: '_self'});
		// end 20200525 KI4MCW
	})

	$(document).on('click', '#showFew', function() {
		if (filterCall==0){
			filterCall=1;
			$("#buttonColor").css("color", "red");
			searchCall=$('#searchText').val();
		//alert(searchCall+' '+filteredNow + filteredByNow)
			getLog(searchCall,1,filteredNow,filteredByNow);
		}else{
			filterCall=0;
			$("#buttonColor").css("color", "white");
			getLog('',1,filteredNow,filteredByNow);
		}
	})

	open = function(verb, url, data, target) {
		var form = document.createElement("form");
		form.action = url;
		form.method = verb;
		form.target = target || "_self";
		form.style.display='none';
		if (data) {
			for (var key in data) {
				var input = document.createElement("textarea");
				input.name = key;
				input.value = typeof data[key] === "object" ? JSON.stringify(data[key]) : data[key];
				form.appendChild(input);
			}
		}
		document.body.appendChild(form);  //causes posted data to appear in table???
		form.submit();
	};

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

	function getSubLog(response,tPage,tOrder,tDir)
	{
		var aChk=$('#selAll:checked').val();
		var pCount=response.substr(3, response.indexOf(">")-3);
		response=response.substr(response.indexOf(">")+1);
		tRowCount=response.substr(3, response.indexOf(">")-3);
		var arrayList=new Array();
		var tL="";
		pageCount=pCount;
		for (i=1;i<=pCount;i++){
			tL=tL+"<div class='mypages'><li id='li"+i+"'><a class='dropdown-item' href='#'>" + i + "</a></li></div>";
		}
		$("#pageList").empty();
		$("#pageList").append(tL);
		response=response.substr(response.indexOf(">")+1);
		$('#tbodylog').empty();
		$('#tbodylog').append(response);
		  $("#pgNum").text(tPage);

		$(function(){
			$(document).on('click', '.clickme', function(event) {
				var tCall = $(this).attr('id');
				$('#searchText').val(tCall);
				$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tCall, table: "MySettings"});
			})
			$('.hClk').on('click', function() {
				var tIDV=event.target.id;

				 tIDV=$(this).attr('id');
				var radio="1";
				$.post("/programs/GetSetting.php",{field: "LogSortDir", radio: tMyRadio, table: "MySettings"},function(response){
					  sDirG=response;
					  $.post("/programs/GetSetting.php",{field: "LogSort", radio: tMyRadio, table: "MySettings"},function(response){
						  sOrderG=response;
						if (!(sOrderG==tIDV)){
							sDirG='ASC';
						}else{
							if (sDirG=='ASC'){
								sDirG='DESC';
							}else{
								sDirG='ASC';
							}
						}
						sOrderG=tIDV;
						var tF=filteredByNow;
						getLog(searchCall,"1",filteredNow,tF);
						sDirNext=sDirG;
						  $.post("/programs/SetSettings.php",{field: "LogSortDir", radio: tMyRadio, data: sDirNext, table: "MySettings"},function(response){
							  $.post("/programs/SetSettings.php",{field: "LogSort", radio: tMyRadio, data: sOrderG, table: "MySettings"},function(response){
								  gettingLog=false;
							  });
							  curPage=1;
						  });
					  });
				  });
			 });
		 });

		$(function () {
			$('.logButton').on('click', function () {
				var tID = $(this).attr('id');
				if (tID.substring(0,1)=="e"){
					tID=tID.substring(1);
					 open('POST', './logEditor.php', {c: tMyCall, x: tUserName, id: tID, what: "edit", order: tOrder, direction: tDir, page: curPage, radio: tMyRadio, style: logStyle}, '_self');
				 }
				if (tID.substring(0,1)=="b"){
					tID=tID.substring(1);
					var tF=filteredByNow;
					var tAsk='Delete one QSO from log named '+tF+'?';
					if (tF==''){
						tF="x";
						tAsk="Delete one QSO from ALL Logs?";
					}
					   if (confirm(tAsk)){
						   $.post('./programs/deleteQSO.php',{log:tF,which:'selected',id:tID},function(response){
						$("#modalA-body").html("<br>"+response+"<br><br>");			  		
							   $("#modalA-title").html("Log Delete");
								 $("#myModalAlert").modal({show:true});
								 setTimeout(function(){ 
									 $("#myModalAlert").modal('hide');
								},
								 2000);

				
							getLog(searchCall,"1",filteredNow,tF);
						   })
					   }
				 }
			 })
		 })

		 $(function(){
			$('.selCk').on('click', function () {
				var tID1 = $(this).attr('id');
				tID1=tID1.substring(1);
				$.post('./programs/getLogVal.php', {record:tID1,field:'Sel'}, function(response){
					if(response==0){
						  $.post('./programs/putLog.php', {record:tID1,field:'Sel',data:'1',filter: ''}, function(response){
							  getLog(searchCall,"1",filteredNow,filteredByNow);
						  });
					}else{
						  $.post('./programs/putLog.php', {record:tID1,field:'Sel',data:'0',filter: ''}, function(response){
							  getLog(searchCall,"1",filteredNow,filteredByNow);
						  });

					}
				});
			});
		 });
		 fillDescription();
	};

	$(document).on('click', '.mylog', function() {
		var text = $(this).text();
			$.post("/programs/SetSettings.php",{field: "LogName", radio: tMyRadio, data: text, table: "MySettings"});
		setFilter(text);
	});
function myFunction(ele){
	   alert(ele.options[ele.selectedIndex].id);
	}
	$(document).on('click', '#styleList', function(event) {
		$tClick=event.target.id;
		
		var text = document.getElementById($tClick).innerText;;
		logStyle=text;
		curPage=1;
		$.post("/programs/SetSettings.php",{field: "LogStyle", radio: tMyRadio, data: text, table: "MySettings"}, function(response){
			getLog(searchCall,"1",filteredNow,filteredByNow);
			fillDescription();
		});
	});


	function getLog(sCall,sPage,bFiltered,filter){
		gettingLog=true;
		$.post("/programs/GetSetting.php",{field: "LogStyle", radio: tMyRadio, table: "MySettings"},function(response){
			logStyle=response;
			var sOrder=sOrderG;
			var sDir=sDirG;
			if (filterCall==0){
				sCall='';
			}
			if (bFiltered==true){
				filteredNow=true;
				filteredByNow=filter;
				  $.post('./programs/getLog.php',{call: sCall, field: 'Logname', value: filter, page: sPage, order: sOrderG, direction: sDirG, style: logStyle},function(response) {
					  getSubLog(response,sPage,sOrder,sDir);
				})
			}else{
				filteredNow=false;
				filteredByNow='';
				  $.post('./programs/getLog.php',{call: sCall, page: sPage, order: sOrder, direction: sDir, style: logStyle},function(response) {
					  getSubLog(response,sPage,sOrder,sDir);
				  })
			}
		});
	};

	function setFilter(sLogName){
		if (sLogName=="ALL Logs"){
			filteredNow=false;
			filteredByNow='';
			getLog(searchCall,"1",filteredNow,filteredByNow);
		}else{
			filteredNow=true;
			filteredByNow=sLogName;
			getLog(searchCall,"1",filteredNow,filteredByNow);
		}
		fillDescription();
	}

	function clickedHam(x) {
		$('#menuPopup').css('display','block');
		var top = $("#deleteSelected").offset().top;
		top += $("#deleteSelected").height()-20;
		var left = $("#deleteSelected").offset().left - 900;
		$("#menuPopup").css({
			top: top+"px",
			left: left + "px"
		});
	};
	
	function fillDescription(){
		var tF;
		if (filteredByNow!=''){
			tF=filteredByNow;
		}else{
			tF="All Logs";
		}
		var descr=tMyCall+" Log: "+tF+"--"+logStyle+" ("+tRowCount+")";
		$('#descr').text(descr);
	}

	var tUpdate = setInterval(updateTimer,1000);
	function updateTimer(){
		var now = new Date();
		var now_hours=now.getUTCHours();
		now_hours=("00" + now_hours).slice(-2);
		var now_minutes=now.getUTCMinutes();
		now_minutes=("00" + now_minutes).slice(-2);
		$("#fPanel5").text(now_hours+":"+now_minutes+'z');
	  }
});
	  
</script>
</head>
<body class="body-black-scroll" id="log">
	<?php require($dRoot . "/includes/header.php"); ?>
	<div class="container-fluid">
	<div class="row" style="margin-top:10px;">
		<div class="col-12 col-lg-4 btn-padding">
			<div class="btn-group" role="group">
				<div class="button">
					<ul class="pagination">
						<li class="page-item" id="start">
							<a class="page-link btn-primary" title="First Page" href="#">
								<i class='fas fa-fast-backward fa-fw fa-lg'></i>
							</a>
						</li>
						<li class="page-item" id="previous">
							<a class="page-link btn-primary" title="Previous Page" href="#">
								<i class='fas fa-step-backward fa-fw fa-lg'></i>
							</a>
						</li>
					</ul>
				</div>
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle" data-size="3" type="button" title="Go to Page" data-toggle="dropdown">
						<i class='fas fa-file-alt fa-fw fa-lg'></i>
						<span style="background-color: transparent; color: white; margin-top: -4px;" id="pgNum">1</span>
					</button>
					<ul class="dropdown-menu menu-scroll" id="pageList">
					 </ul>
				</div>
				<div class="button">
					<ul class="pagination">
						<li class="page-item" id="next">
							<a class="page-link btn-color" title="Next Page" href="#">
								<i class='fas fa-step-forward fa-fw fa-lg'></i>
							</a>
						</li>
						<li class="page-item" id="end">
							<a class="page-link btn-color" title="Last Page" href="#">
								<i class='fas fa-fast-forward fa-fw fa-lg'></i>
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="col-12 col-lg-3 btn-padding">
			<span class="label label-success text-white" id='descr' style="cursor: default; margin-top:10px;"></span>
		</div>
		<div class="col-10 col-lg-4 btn-padding">
			<div class="btn-group" role"group">
				<div class="dropdown">
					<button class="btn btn-color dropdown-toggle" id="selStyle" data-size="3" type="button"  title="Choose Log Style" data-toggle="dropdown">
						<i class="fas fa-book fa-lg"></i>
					</button>
					<ul class="dropdown-menu menu-scroll" id="styleList">
						<?php echo getLogStyles(); ?>
					 </ul>
				</div>
				<div class="dropdown">
					<button class="btn btn-color dropdown-toggle" id="pgNum" data-size="3" type="button"  title="Choose Log" data-toggle="dropdown">
						<i class="fas fa-folder fa-lg"></i>
					</button>
					<ul class="dropdown-menu menu-scroll" id="nameList">
						<?php echo getLogNames(); ?>
					 </ul>
				</div>
				<button class='btn btn-color' id='newQSO' title='Add new QSO' type='button'>
					<div style="background:transparent; color:white">
						<i class="fas fa-plus fa-lg"></i>
					</div>
				</button>
				<button class='btn btn-color' id='showFew' title='Filter QSOs' type='button'>
					<div id="buttonColor" style="background:transparent; color:white">
						<i class="fas fa-filter"></i>
					</div>
				</button>
			</div>
		</div>
		<div class="col-2 col-lg-1 btn-padding">
			<div class="dropdown">
				<button class='btn btn-color dropdown-toggle dropdown-ham hButton' type='button' title='Export/Import Actions' data-toggle="dropdown">
					<i class='fas fa-bars fa-fw fa-lg'></i>
				</button>
				<ul class="dropdown-menu dropdown-menu-right menu-scroll" id="fnListTransfer">
					<div class='ex'>
						<li role="presentation" class="dropdown-header">Export/Import ADIF</li>
						<li id='export'><a class='dropdown-item export' id='fn' href='#'>Export to Downloads Folder</a></li>
						<li id='exportsel'><a class='dropdown-item exportsel' id='fn' href='#'>Export Selected Q's</a></li>
						<li id='exportlotw'><a class='dropdown-item exportlotw' id='fn' href='#'>Export for LoTW</a></li>
						<li id='exportsellotw'><a class='dropdown-item exportsellotw' id='fn' href='#'>Export Selected Q's for LoTW</a></li>
						<li id='import'><a class='dropdown-item import' id='fn' href='#uploadModel'>Import</a></li>
						<li role="presentation" class="dropdown-header">Delete Contacts</li>
						  <li id='deleteAll'><a class='dropdown-item deleteall' id='fn' href='#'>Entire Log</a></li>
						<li id='deleteSelected'><a class='dropdown-item deletesel' id='fn' href='#'>Selected Q's</a></li>
					</div>
				</ul>
			</div>
		</div>
	</div>
	</div>
	<table id="logbookt">
		<div id='tbodylog'>
		</div>
	</table>
	<br />
	<?php require($dRoot . "/includes/footer.php"); ?>
	<iframe id="downloadFrame" style="display:none"></iframe>

	<!-- The Upload Modal -->
	<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
		<div class="modal-dialog"  style="width:400px;" role="document">
			<div class="modal-content" >
				<div class="modal-header">
					<h2 class="dlmodal-title" style="color:red;">Upload/Import ADIF</h2>
					<button type="button" class="close" id = "closeULModal" data-dismiss="modal">&times;
					</button>
				  </div>
				  <div class="ulmodal-body">
					<form action="./my/upload.php" method="POST" role="form" class="form-horizontal">
						<div class="input-group">
							<div class="custom-file">
								<input type="file" class="custom-file-input" id="input_file" aria-describedby="inputGroupFileAddon04">
								<label class="custom-file-label" for="input_file">Choose file</label>
							</div>
							<div class="input-group-append">
								<button class="btn btn-success" style="height:42px;" type="button" id="btn_submit">Upload/Import</button>
							</div>
						</div>					
					</form>
					 <div id="ulnumber"></div>
					<div id="ulresult"></div>
				</div>
				<div class="modal-footer" style="padding-right: 20px;">
				  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
			</div>
		  </div>
	</div>

	<!-- The Download Modal -->
	<div class="modal fade" id="downloadModal" tabindex="-1" role="dialog">
		<div class="modal-dialog" style="width: 400px;" role="document" >
			<div class="modal-content">
				<div class="modal-header">
					<h2 class="dlmodal-title" style="color: red;">Export/Download ADIF</h2>
					<button type="button" class="close" id = "closeModal" data-dismiss="modal">&times;
					</button>
				  </div>
				  <div class="dlmodal-body">
					 <div class="spacer" id="dlnumber"></div>
					<div class="spacer" id="dlresult"></div>
				</div>
				<div class="modal-footer" style="padding-right: 20px;">
				  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				</div>
						</div>
		  </div>
	</div>
	<?php require_once($dRoot . "/includes/modal.txt"); ?>
	  <?php require($dRoot . "/includes/modalAlert.txt"); ?>
	<script src="Bootstrap/popper.min.js"></script>
	<script src="Bootstrap/jquery-ui.js"></script>
	<script src="Bootstrap/bootstrap.min.js"></script>
	<script src="js/nav-active.js"></script>
</body>
</html>
