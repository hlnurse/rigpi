<?php
if (!isset($GLOBALS['htmlPath'])){
	$GLOBALS['htmlPath']=$_SERVER['DOCUMENT_ROOT'];
}
$dRoot=$GLOBALS['htmlPath'];
$tCall=$_GET["c"];
$tUserName=$_GET["x"];
require_once($dRoot.'/classes/Membership.php');
$membership = New Membership();
$membership->confirm_Member($tUserName);	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
	<head>
		<meta charset="utf-8">

		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title><?php echo $tCall;?> RigPi Spots Settings</title>
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
			require ($dRoot."/includes/styles.php");
			require_once ($dRoot."/programs/GetSettingsFunc.php");
		 ?>
		 <script type="text/javascript">
	 	  	var tMyCall="<?php echo $tCall;?>";
	 	  	var tCall=tMyCall
	 	  	var tMyRadio='';
	        var tUserName=<?php echo "'".$tUserName."'";?>;
	        var tUser='';
	        var tSelectedRow='';
	        var tFilter='';
	        var tNoInternet=0;
	        $(document).ready(function() {
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
									$("#modalA-body").html(response);			  				
									$("#modalA-title").html("Spots Status");
								  	$("#myModalAlert").modal({show:true});
								});
							};
						};
		 			});	
	
					$(document).on('click', '#disconnectButton', function() {
		  				$.post('/programs/SpotsStart.php', {action: 'stop', radio: tMyRadio, call: tMyCall}, function(response) {
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
			                var descr="Current node: "+response;
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
		
			function getSubClusters(response){
				var arrayList=new Array();
				var tL="";
				$('#logt tbody').empty();
			    $('#logt tbody').append(response);
		        $.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'ClusterID', table: 'MySettings'}, function(response)
		        {
					$('tr').removeClass('highlight'); // remove class from other rows
					$('#'+response).addClass('highlight');
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
	</head>
<body class="body-black">
	<?php require($dRoot."/includes/header.php");?>
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
		<div class='table-striped'>
			<table class='table table-striped table-sm' id='logt' style="margin-top:10px;">
				<thead>
			        <tr class='sortable'>
			          	<th  style='cursor: default; text-align:center;' id='node'>Node</th>
					  	<th  style='cursor: default; text-align:center;' id='ip' name='tCall'>IP</th>
					  	<th  style='cursor: default; text-align:center;' id='port'>Port</th>
					  	<th  style='cursor: default; text-align:center;' id='location'>Location</th>
					  	<th  style='cursor: default; text-align:center;' id='notes'>Notes</th>
			        </tr>
		    	</thead>
		    	<tbody>
				</tbody>
			</table>
		</div>
	</div>
	<br />
    <?php require($dRoot.'/includes/footer.php'); ?>
	<?php require($dRoot.'/includes/modal.txt'); ?>
    <?php require($dRoot.'/includes/modalAlert.txt'); ?>
<script src="./Bootstrap/popper.min.js"</script>
<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
<script src="./Bootstrap/jquery-ui.js"></script>
<script src="./Bootstrap/bootstrap.min.js"></script>
</body>
</html>
