<?php
if (!isset($GLOBALS['htmlPath'])){
	$GLOBALS['htmlPath']=$_SERVER['DOCUMENT_ROOT'];
}
$dRoot=$GLOBALS['htmlPath'];
$tCall=$_POST["c"];
$tUserName=$_POST["x"];
require_once($dRoot.'/classes/Membership.php');
$membership = New Membership();
$membership->confirm_Member($tUserName);	
require ($dRoot.'/programs/GetMyRadioFunc.php');
require_once($dRoot.'/programs/GetUserFieldFunc.php');
$tUser=getUserField($tUserName,"uID");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<meta charset="utf-8">

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo $tCall;?> RigPi Log Editor</title>
	<meta name="description" content="RigPi Log Editor">
	<meta name="author" content="Howard Nurse, W6HN">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">
	<script src="/Bootstrap/jquery.min.js" ></script>
	<script src="./js/moment.js" ></script>
	<script defer src="./awe/js/all.js" ></script>
	<link href="./awe/css/all.css" rel="stylesheet">
	<link href="./awe/css/fontawesome.css" rel="stylesheet">
	<link href="./awe/css/solid.css" rel="stylesheet">	

	<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
	<link rel="shortcut icon" href="./favicon.ico">
	<link rel="apple-touch-icon" href="./favicon.ico">
	<?php 
		ini_set('error_reporting', E_ALL);
		ini_set('display_errors', 1);
		require_once($dRoot."/includes/styles.php");
		require($dRoot."/programs/sqldata.php");
		require_once($dRoot."/classes/MysqliDb.php");	
		require_once($dRoot."/programs/getModeList.php");
		require_once($dRoot."/programs/timeDateFunc.php");
		require_once($dRoot."/programs/GetBand.php");
		require_once($dRoot."/programs/getLogEditorFunc.php");	
		require_once($dRoot."/programs/GetSettingsFunc.php");
		if (!empty($_POST['page'])){
			$tPage=$_POST['page'];
		}else{
			$tPage=1;
		}
		if (!empty($_POST['order'])){
			$tOrder=$_POST['order'];
		}else{
			$tOrder='Time_Start';
		}
		if (!empty($_POST['direction'])){
			$tDir=$_POST['direction'];
		}else{
			$tDir='DESC';
		}
		if (!empty($_POST['style'])){
			$logStyle=$_POST['style'];
		}else{
			$logStyle='General';
		}
		if (!empty($_POST['radio'])){
			$tMyRadio=$_POST['radio'];
		}else{
			$tMyRadio='1';
		}
		$id=$_POST['id'];
		$main='0';			
		$db = new MysqliDb('localhost', $sql_radio_username, $sql_radio_password, $sql_radio_database);
		
		if ($id>0){
			$db->where ("MobileID", $id);
			$row = $db->getOne ("Logbook");
			$ln=$row['Logname'];
		}else{
			$row = $db->getOne ("Logbook");
			$row['MobileID']=0;
			$ln=GetField($tMyRadio,'LogName','MySettings');
			if ($ln=='ALL Logs'||$ln==''){
				$ln='Main';
			}
			$row['Logname']=$ln;
			
			//add fields and "" to $data
			//!!!Use getLogFields with $id=0.
		}

	?>
	<script>
	var dbRow=<?php echo json_encode($row);?>;
	var timeUnixStart='';
	var timeUnixEnd='';
	var his_QTH='';
	var his_Country='';
	var his_State='';
	var tID=<?php echo "'" . $id . "'"; ?>;
	var tF=<?php echo "'".$main."'"; ?>;
	var logStyle=<?php echo "'".$logStyle."'"; ?>;
	var tMyRadio=<?php echo "'".$tMyRadio."'"; ?>;
	var tFI=tF.indexOf('PRT');
	var bAutoUpdate=false;
	var tUserName=<?php echo "'".$tUserName."'";?>;
	var tUser=<?php echo "'".$tUser."'";?>;
	var tMyCall=<?php echo "'".$tCall."'";?>;
	var tCall=tMyCall;
	var tLogName=<?php echo "'".$ln."'";?>;
	var forgetUpdate=false;
	var call='';
	if (tID=='0' && tFI==-1){
		bAutoUpdate=true;
	}else{
		bAutoUpdate=false;
	}
	$(document).ready(function(){
		$('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
			if (!$(this).next().hasClass('show')) {
				$(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
//				}
//				var $subMenu = $(this).next(".dropdown-menu");
//				$subMenu.toggleClass('show');
			}
			var $subMenu = $(this).next(".dropdown-menu");
			$subMenu.toggleClass('show');
				
				
//				$(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
				$(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
					$('.dropdown-submenu .show').removeClass("show");
			});
			return false;
		});

  		var idV=<?php echo $id;?>;
  		var logStyle=<?php echo "'".$logStyle."'"; ?>;
//        $.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
		$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response) {

//        {
			$('#searchText').val(response.toUpperCase());
			$('#Callsign').val(response.toUpperCase());

	    });

 		
		function GetFields(doLookup){
			var fields='';
			if (doLookup==1){
				$('#searchText').val($('#Callsign').val().toUpperCase());
				$.post("./programs/GetCallbook.php", {call: $('#Callsign').val(), what: 'QRZData', user: tUser, un: tUserName},function(response){
					$.post("./programs/getLogEditor.php", {myRadio: tMyRadio, uID: tID, style: logStyle, doLookup:1, uName: tUserName},function(response){
						fields=response;
						var tL=$('#Logname').val();
						var tB=$('#Time_Start_Plain').val();
						var tE=$('#Time_End_Plain').val();
						$('#lFields').empty();
						$('#lFields').append(fields);
						$('#Logname').val(tL);
						$('#Time_Start_Plain').val(tB);
						$('#Time_End_Plain').val(tE);
						var call=document.getElementById('Callsign');
						call.addEventListener("blur", function(){
							$('#searchText').val($('#Callsign').val().toUpperCase());
						})
					})
				})
			}else{
				fields=<?php echo(getLogFields($tMyRadio,$id,$logStyle,'0',$tUserName))?>;
				$('#lFields').empty();
//			    $('#lFields').append(fields);
//			    if ($('#Logname').val()==''){
				$('#lFields').append(fields);
				if ($('#Logname').val()==''){
					$('#Logname').val(tLogName);
			    }
				if($('#Time_Start_Plain').val()==''){
					var curSVal=new Date($.now());
					var sTime=moment.utc(curSVal).format('HHmm DD-MMM-YYYY').toUpperCase(); //uses moment.js, a library with many time/date functions
	 				$('#Time_Start_Plain').val(sTime);
				}
				var call=document.getElementById('Callsign');
				call.addEventListener("blur", function(){
					$('#searchText').val($('#Callsign').val().toUpperCase());
				})
			}// end if doLookup, still in function GetFields
			$(document).on('click', '#startButton', function() {
				var curSVal=new Date($.now());
				var sTime=moment.utc(curSVal).format('HHmm DD-MMM-YYYY').toUpperCase(); //uses moment.js, a library with many time/date functions
 				$('#Time_Start_Plain').val(sTime);
			});	
			$(document).on('click', '#endButton', function() {
				var curEVal=new Date($.now());
				var eTime=moment.utc(curEVal).format('HHmm DD-MMM-YYYY').toUpperCase(); //uses moment.js, a library with many time/date functions
 				$('#Time_End_Plain').val(eTime);
			});	
			$(document).on('click', '.time', function() {
				var tBut = '#'+$(this).attr('id');
				var curEVal=new Date($.now());
				var eTime=moment.utc(curEVal).format('HHmm DD-MMM-YYYY').toUpperCase(); //uses moment.js, a library with many time/date functions
 				$(tBut).val(eTime);
			});	
			$(document).on('click', '.dropdown-item', function() {
				var tIDD = '#'+$(this).attr('id');
				var tIDD=tIDD.replace('ID', '');
				var text=$(this).text();
  				$(tIDD).val(text);
			});	
//		}
		} // end GetFields; still in ready fn

//		$("input").bind("keydown", function(event) 
		// 20200528 Rob KI4MCW - Stay in editor after Save
		// Re-work this section to be able to capture Enter in multiple contexts
		// The original usage was to capture the Enter key within the search field
		// Binding to the "input" fields does not always work)
		$(document).keydown(function(event) 
		{
            // track enter key
//            var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
			var keycode = (event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode));
			//           if (keycode == 13) { // keycode for enter key
//               if ($('#searchText').val()==''){
//               	return false;
//               }
			if (keycode != 13) // keycode for Enter key
			{ return true ; } // not what we're looking for
			else if ($('#searchText').is(':focus')) // Enter key while in the search field
			{	
				if ($('#searchText').val()=='') // search box is empty
				{ return false ; } // ignore
				else 
				{ 
//                var tDX=$('#searchText').val().toUpperCase();
//                $('#searchText').val(tDX);
//                document.getElementById('searchButton').click();
//				$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tDX, table: "MySettings"});
//               return false;
//           } else  {
//               return true;
					var tDX=$('#searchText').val().toUpperCase();
 					$('#searchText').val(tDX);
					document.getElementById('searchButton').click();
					$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tDX, table: "MySettings"});
					return false ;
             }
//        });
			} 
			else // Enter key from any other field - save QSO
			{
				document.getElementById('logSave').click(); 
				return false ; 
			} 
		}); // end keydown routine; still in ready fn
		// end 20200528 KI4MCW

		// 20200525 Rob KI4MCW - Get Style to carry through to editor.
		// Write log name and style to top banner
		fillDescription() ;
		// end 20200525 KI4MCW
 
		$(document).on('click', '#logCancel', function(){
			$.post("./programs/deleteTempQSO.php",{name:tLogName},function(response){
				open('POST', './log.php', {c: tMyCall, x: tUserName, page: <?php echo "'".$tPage."'";?>, order: <?php echo "'".$tOrder."'";?>, direction: <?php echo "'".$tDir."'";?>});
			})
		});

		$(document).on('click', '#logSave', function() {
//			saveLog(1);
			// 20200528 Rob KI4MCW - Stay in editor after Save 
			//saveLog(1);
			// First draft - no persistence
			if ($('#stayInEditor').prop('checked')) { 
//				saveLog(0) ; 
			// 20200604 Rob KI4MCW - Bugfix for stay-in-editor
			//      saveLog(0) ; 
				saveLog(2) ; // stay in editor
			}else{ 
//				saveLog(1) ; 
				saveLog(1) ; // return to Log view
			}
			// end 20200528 KI4MCW
//		});	
			// end 20200604 KI4MCW
		}); // still in ready fn
		
		function saveLog(doAlert){
			// 20200604 Rob KI4MCW - Bugfixes for stay-in-editor changes
			// doAlert = 0, load callbook data into fields, format, do not save QSO
			// doAlert = 1, save QSO and return to Log view
			// doAlert = 2, NEW, save QSO but stay in logEditor
			forgetUpdate=true;
			// sanity check
			if ($('#Callsign').val().length==0){
				$("#modalA-body").html("Please enter a callsign.");			  				
				$("#modalA-title").html("Log Editor");
			  	$("#myModalAlert").modal({show:true});
				return;
			}
			// get Style data, so we know which fields we're working with
 			<?php
				$dbStyle = new MysqliDb("localhost", $sql_log_username, $sql_log_password, $sql_log_database);
				$dbStyle->where('Name',$logStyle);
				$styles=$dbStyle->get('LogStyles');				
				$size=$dbStyle->count;
			?>
			$('#searchText').val($('#Callsign').val().toUpperCase());
			// save recently used data
 			$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: $('#searchText').val(), table: "MySettings"});
			// clean and format data
 			var tStyles=<?php echo json_encode($styles);?>;
			var curSVal=$('#Time_Start_Plain').val();
			var sTime=moment.utc(curSVal,'HHmm DD-MMM-YYYY').format('X'); //uses moment.js, a library with many time/date functions
			var curEVal=$('#Time_End_Plain').val();
			var eTime=moment.utc(curEVal,'HHmm DD-MMM-YYYY').format('X');
			// apply Attribute rules and fill empty fields
 			var list;
			$.each(tStyles, function(index,value){
				list=value['IDValue'];
				if (list.length>2){ //list can be "", if so causes error
					var attr=value['Attribute'];
					curVal=$('#'+list).val();
					if (curVal!=null){
						if (attr=='Caps'){
							curVal=curVal.toUpperCase();
						}
						if (attr=='Add Periods'){
							curVal=curVal.replace(/\./g, "");  //remove periods for frequency, stored without periods
						}
						if (attr=='Set Time' && list=='Time_Start_Plain'){
							dbRow['Time_Start']=sTime;
						};
						if (attr=='Set Time' && list=='Time_End_Plain'){
							dbRow['Time_End']=eTime;
						}
						dbRow[list]=curVal;
						if (dbRow['Logname']=='' || dbRow['Logname']=='ALL Logs'){
							dbRow['Logname']='Main';
						}
//					}
//					
//				}
					} // end if curVal != null
				} // end if ist.length>2
 //			});
			}); // end each tStyles
			if (dbRow['Rx_Frequency']==""){
				dbRow['Rx_Frequency']=0;
			}
			if (dbRow['Tx_Frequency']==""){
				dbRow['Tx_Frequency']=0;
			}
			if (dbRow['Time_End']==""){
				dbRow['Time_End']=0;
			}
			dbRow['MobileID']=tID;
			if (doAlert>0){
				dbRow['ID']=0;
			}
			// save this QSO, maybe
 			$.post('./programs/SetLog.php',{data: dbRow}, function(response) {
				// where response is the record number of the new entry 
				// (the MobileID field)
//				alert(response);
				if (doAlert==1){
//						open('POST', './log.php', {c: tMyCall, x: tUserName, page: <?php echo "'".$tPage."'";?>, order: <?php echo "'".$tOrder."'";?>, direction: <?php echo "'".$tDir."'";?>});
//				}else{
//					tID=response;
//					GetFields('1');
//				}
//			});
					// 20200528 Rob KI4MCW - Stay in editor after Save 
					//GetFields('1');
					// doAlert = 0, so stay in logEditor after save
					// go to main Log view
					open('POST', './log.php', {c: tMyCall, x: tUserName, page: <?php echo "'".$tPage."'";?>, order: <?php echo "'".$tOrder."'";?>, direction: <?php echo "'".$tDir."'";?>});
				}
				else if (doAlert==2){
					// 20200604 Rob KI4MCW - new
					// stay in logEditor
 					GetFields('0') ; // clear fields
					tID=0 ; // reset record number for a new QSO
 					$('#lFields').find('input:first').focus() ; // set focus on first field
					showSavedIndicator() ; // blink light
					// end 20200528 KI4MCW
					// end 20200604 KI4MCW	
				}
				else {
					// doAlert = 0
					// we were just pre-filling fields from logbook
					tID=response;
					GetFields('1');
  				}
			}); // end post SetLog
		} // end saveLog ; still in ready fn

		open = function(verb, url, data, target) {
			var form = document.createElement("form");
			form.action = url;
			form.method = verb;
			form.target = target || "_self";
			if (data) {
				for (var key in data) {
					var input = document.createElement("textarea");
					input.name = key;
					input.value = typeof data[key] === "object" ? JSON.stringify(data[key]) : data[key];
					form.appendChild(input);
				}
			}
			form.style.display = 'none';
			document.body.appendChild(form);
			form.submit();
 //		    	}
//		    }
//		    form.style.display = 'none';
//		    document.body.appendChild(form);
//		    form.submit();
//		};
//		}; // end open
		}; // end open, still in ready fn
 
		$(document).on('click', '#endButton', function() {
			<?php
				$uTi=getUnixTime();
				$tD=convertUnix($uTi);
			?>
			timeUnixEnd=<?php echo "'" . $uTi . "'"; ?>;
			var text = <?php echo "'" . $tD . "'"; ?>;
//				$('#endValue').val(text);
			$('#endValue').val(text);
 		});	


		$.getScript("/js/modalLoad.js");

		$(document).on('click', '#getCallbook', function(){
			if (dbRow['MobileID']==0){
				dbRow['ID']=-1;
			}else{
				dbRow['ID']=0;
			}
			saveLog('0');
		});
		
//		GetFields('0');
		GetFields('0'); // clear feilds on first page render
 
//   	});  		
		// 20200528 Rob KI4MCW - Stay in editor after Save
		// set focus after initial form load (keyboard friendly)
		$('#lFields').find('input:first').focus() ;
		// end 20200528 KI4MCW
		
	}); // end ready fn 		


//    $(document).on('click', '#logoutButton', function() 
//    {
	$(document).on('click', '#logoutButton', function() 
	{
		openWindowWithPost("/login.php", {
		    status: "loggedout",
//		    username: ''});
			username: ''
		});
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
//	};                
	}; // end openwindowwithpost               

  	$.getScript("/js/addPeriods.js");
    
    function GetBandFromFrequency(nFreq)
    {
		if      (nFreq > 1800000 && nFreq < 2000000){ return "160M"; }
		else if (nFreq > 3500000 && nFreq < 4000000){ return "80M"; }
		else if (nFreq > 5330000 && nFreq < 5405010){ return "60M"; }
		else if (nFreq > 7000000 && nFreq < 7300000){ return "40M"; }
		else if (nFreq > 10100000 && nFreq < 10150000){ return "30M"; }
		else if (nFreq > 14000000 && nFreq < 14500000){ return "20M"; }
		else if (nFreq > 18060000 && nFreq < 18168000){ return "17M"; }
		else if (nFreq > 21000000 && nFreq < 21450000){ return "15M"; }
		else if (nFreq > 24890000 && nFreq < 24990000){ return "12M"; }
		else if (nFreq > 28000000 && nFreq < 29700000){ return "10M"; }
		else if (nFreq > 50000000 && nFreq < 54000000){ return "6M"; }
		else if (nFreq > 144000000 && nFreq < 148000000){ return "2M"; }
		else if (nFreq > 219000000 && nFreq < 225000000){ return "1.25M"; }
		else { return "UNK"; }
    }
 
        var tUpdate = setInterval(updateTimer,1000);
        var cFreqForBand=0;
        function updateTimer(){
			if (bAutoUpdate==true) {
	            $.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'MainIn'}, function(response) {
	                if (response.indexOf('OFF')==-1){
		                var tF=addPeriods(response);
		                cFreqForBand=response;
		                var cFreq=("   " + tF).slice(-11);
						$('#Rx_Frequency').val(cFreq);
			            $.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'SplitIn'}, function(response) {
				            var tF=cFreq;
				            if (response==1){
					            $.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'SubIn'}, function(response) {
					                var tF=addPeriods(response);
					               cFreq2=("   " + tF).slice(-11);
					                if (cFreq2.indexOf('PRT')==-1){
										$('#Tx_Frequency').val(cFreq2);
					                }
					            });				
				            }else{
								$('#Tx_Frequency').val(tF);		            
				            }
	//			        });
						}); // end post
			            $.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'ModeIn'}, function(response) {
							$('#Mode').val(response);
			            });
						$('#Band').val(GetBandFromFrequency(cFreqForBand));
	//			    };
	//           });				
					}; // end if response.indexof
				}); // end post GetInterface
 			} // end if bAutoUpdate
 	        updateFooter();
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
//			});
			}); // end post GetRotorIn
         
		} // end function updatetimer


	// 20200525 Rob KI4MCW - Get Style to carry through to editor.
	// Build contents for top banner
	function fillDescription(){
		var descr=tMyCall+" Log Editor: "+tLogName+"--"+logStyle ;
		$('#descr').text(descr);
	}
	// end 20200525 KI4MCW

	// 20200528 Rob KI4MCW - Stay in editor after Save 
	// Turn on the Saved indicator for a moment
	function showSavedIndicator() {
		// tricky - a jQuery fade works in parallel to CSS
		// un-hide both ways in both CSS and jQuery, 
		// then use jQuery to fade out. Do not set opacity directly.
		$('#qsoSaved').css('visibility', 'visible').show() ;
		$('#qsoSaved').delay(4000).fadeOut(2000) ;
    }
//    }
	// end 20200528 KI4MCW

</script>
</head>

<body class="body-black">
	<?php require ($dRoot."/includes/header.php");?>
	<div class="container-fluid">
		<div class="row" style="margin-bottom:-10px;">
			<div class="col-12  col-sm-4 btn-padding">
<!-- 20200528 Rob KI4MCW - Stay in editor after Save -->
<!-- Add a Saved indicator 
     This remains hidden until we turn it on -->
				<span class="label label-success" id="qsoSaved"  
				   style="margin-top:0px;background:green;color:white;
					  padding:4px 10px 4px 10px;visibility:hidden;">QSO Saved
				</span>
<!-- end 20200528 KI4MCW -->
 			</div>						
 			<div class="col-6 col-sm-4 text-center">
<!--				<span class="label label-success text-white" style="margin-top:10px;">Log Editor</span> -->
				<!--  20200525 Rob KI4MCW - Get Style to feed through to editor.
				<span class="label label-success text-white" style="margin-top:10px;">Log Editor</span>
				-->
<!--  20200525 Rob KI4MCW - Get Style to feed through to editor. -->
				<span class="label label-success text-white" id="descr" style="margin-top:10px;"></span>
				<!-- end 20200525 KI4MCW -->
			</div>
			<div class="col-6 col-sm-4 btn-padding">
				<button class='btn btn-color' id="getCallbook" title='Get Defaults and Callbook' type='button'>
				<i class="fas fa-arrow-alt-circle-down fa-lg"></i>
				</button>
				<button class='btn btn-color' id="logCancel" title='Cancel and return to log' type='button'>
				<i class="fas fa-ban fa-lg"></i>
				</button>
				<button class='btn btn-color' id="logSave" title='Save and return to log' type='button'>
				<i class="fas fa-cloud-upload-alt fa-lg"></i>
				</button>
			</div>
		</div>
		<hr style="background-color:white;">
		<tFields id="lFields">
		</tFields>
<!-- 20200528 Rob KI4MCW - Stay in editor after save (continued) -->
<!-- Original format (multiple row divs) 
		<div class="row" style="margin-left:20px;">
			<span class="label label-success text-white" style="margin-top:10px;">Green Label = Log Designer Default Value</span>
		</div>
		<div class="row" style="margin-left:20px;">
			<span class="label label-success text-white" style="margin-top:10px;">Red Value = No Edit</span>
		</div>
-->
<!-- Slight re-format (smaller font, tighter layout) -->
		<div class="row">
<!-- single block with line break -->
			<div class="col-sm-4 text-spacer">
				<label style="margin-top:10px;margin-left:20px;margin-right:20px;color:white;">
					Green Label = Log Designer Default Value<br/>
					Red Value = No Edit
				</label>
			</div>

<!-- Add basic instructions in that same style -->
<!-- These are also in a block, so can be relocated on smaller screens -->
			<div class="col-sm-4 text-spacer">
				<label style="margin-top:10px;margin-left:20px;margin-right:20px;color:white;">
					Tab or Shift-Tab to Move Between Fields<br/>
					Click Upload Button or Hit Enter to Save
				</label>
			</div>
<!-- Add checkbox to define behavior upon Save -->
			<div class="col-sm-4 text-spacer">
                <div class="form-check form-check-inline">
                    <label style="margin-top:10px;margin-left:20px;margin-right:20px;color:white;">
						<input type="checkbox" id="stayInEditor" class="form-check-input">Stay in Editor After Saving</input>
                   </label>
				</div>
			</div>
		</div>
<!-- end 20200528 KI4MCW -->
		<?php require($dRoot.'/includes/modal.txt'); ?>
	    <?php require($dRoot.'/includes/footer.php'); ?>
        <?php require($dRoot.'/includes/modalAlert.txt'); ?>
	</div>
</body>
</html>
<script src="./Bootstrap/popper.min.js"</script>
<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
<script src="./Bootstrap/jquery-ui.js"></script>
<script src="./Bootstrap/bootstrap.min.js"></script>
