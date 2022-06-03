<?php
if (!isset($GLOBALS['htmlPath'])){
	$GLOBALS['htmlPath']=$_SERVER['DOCUMENT_ROOT'];
}
$dRoot=$GLOBALS['htmlPath'];
$tCall=$_GET["c"];
$tUserName=$_GET["x"];
require_once ($dRoot.'/classes/Membership.php');
$membership = New Membership();
$membership->confirm_Member($tUserName);	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html lang="en">
	<head>
		<meta charset="utf-8">

		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title><?php echo $tCall;?> RigPi Accounts</title>
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
	 	  	var tCall=tMyCall;
	 	  	var tMyRadio='';
	        var tUserName="<?php echo $tUserName;?>";
	        var tUser='';
	        var tCurrentUser='';
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
				window.pageCount=1;
				getUsers();
				$.post('/programs/GetUserField.php',{un: tUserName, field: 'uID'}, function(response) {
				    tCurrentUser=response;
				});
				$.post('/programs/GetSelectedRadio.php',{un:tUserName}, function(response) 
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
		
					$(document).on('click', '#newUser', function() {
						 window.open("/userEditor.php?id=0&what=edit&c="+tMyCall+"&x="+tUserName,"_self");
					})
	
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
					
				})
				
				$.getScript("/js/modalLoad.js");

			})
			
			function getUsers(){
	  			$.post('/programs/getUsers.php',function(response) {
		  			getSubUsers(response);
		  		})
			}
			
			function getSubUsers(response){
				var arrayList=new Array();
				var tL="";
				$('#logt tbody').empty();
			    $('#logt tbody').append(response);
			    $(function () {
			        $('.logButton').on('click', function () {
			            var tID = $(this).attr('id');
			            if (tID.substring(0,1)=="e"){
				            tID=tID.substring(1);
			 				window.open("/userEditor.php?id="+tID+"&what=edit&c="+tMyCall+"&x="+tUserName,"_self");
			 			}
			            if (tID.substring(0,1)=="b"){
				            tID=tID.substring(1);
				            var tUN='';
							$.post('/programs/GetUserFieldFromID.php',{uid:tID, field: 'Username' },function(response){
					            tUN=response;
						   		if (confirm('Delete one User with Username '+tUN+' from this server?')){
								   	$.post('/programs/deleteUser.php',{id:tID, un: tUN},function(response){
										$("#modalA-body").html(response);			  				
										$("#modalA-title").html("Delete User");
									  	$("#myModalAlert").modal({show:true});//			  				alert(response);
									   	if (tID==tCurrentUser){
									  		$.post('./login.php',{status:'loggedout'});
									  		window.location.replace("login.php");
									   	}else{
										   	getUsers();
									   	}
								   	})
						   		}
					        });
			 			}
			 		})
			 	})
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
		  	}

			var tUpdate = setInterval(bearingTimer,1000)
			function bearingTimer()
			{
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
	<?php require ($dRoot."/includes/header.php");?>
	<div class="container-fluid">
		<div class="row" style="margin-top:20px;">
			<div class="col-sm-4 text-center">
			</div>
			<div class="col-sm-4 text-center">
				<span class="label label-success text-white "  style='cursor: default;'>Account Settings</span>
			</div>
			<div class="col-sm-4 btn-padding">
				<div class="plus">
					<button class='btn btn-color hbutton' id='newUser' style="margin-top:-10px" type='button'>
						<div style="background:transparent; color:white">
							<i class="fas fa-plus fa-lg"></i>
						</div>
					</button>
				</div>
			</div>
		</div>
		<div class='table-striped'>
 		<table class='table table-striped table-sm' style="margin-top:10px" id='logt'>
			<thead>
            <tr class='sortable'>
              	<th style='text-align:center;' id='tActive'>On
						</th>
              	<th style='text-align:center;' id='tCall'>Call
						</th>
			  	<th style='text-align:center;' id='tAccess' name='tCall'>Access
						</th>
			  	<th style='text-align:center;' id='tAccess' name='tCall'>Username
						</th>
			  	<th style='text-align:center;' id='tFirst'>First
						</th>
			  	<th style='text-align:center;' id='tLast'>Last
						</th>
			  	<th style='text-align:center;' id='tQTH'>QTH
						</th>
			  	<th style='text-align:center;' id='tLastVisit'>Last Visit
						</th>
			  	<th style='width: 20%'></th>
            </tr>
        	</thead>
        	<tbody>

			 </tbody>
		</table>
		</div><br />

    <?php require($dRoot.'/includes/modal.txt'); ?>
    <?php require($dRoot.'/includes/modalAlert.txt'); ?>
    <?php require($dRoot.'/includes/footer.php'); ?>
    </div>
<script src="./Bootstrap/popper.min.js"</script>
<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
<script src="./Bootstrap/jquery-ui.js"></script>
<script src="./Bootstrap/bootstrap.min.js"></script>
</body>
</html>
