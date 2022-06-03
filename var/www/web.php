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
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">

		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

		<title><?php echo $tCall; ?> RigPi Web</title>
		<meta name="description" content="RigPi Web">
		<meta name="author" content="Howard Nurse, W6HN">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<link rel="shortcut icon" href="/favicon.ico">
		<link rel="apple-touch-icon" href="/favicon.ico">
		<link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">
        <script src="/Bootstrap/jquery.min.js" ></script>
		<script defer src="./awe/js/all.js" ></script>
		<link href="./awe/css/all.css" rel="stylesheet">
		<link href="./awe/css/fontawesome.css" rel="stylesheet">
		<link href="./awe/css/solid.css" rel="stylesheet">	

		<?php require $dRoot . "/includes/styles.php"; ?>

		 <script type="text/javascript">
			 var tMyRadio=1;
			 var dxLat='';
			 var dxLon='';
			 var tDX='';
			 var tUserName="<?php echo $tUserName; ?>";
			 var tUser='';
			 var tMyCall="<?php echo $tCall; ?>";
			 var tCall=tMyCall;
			 var speedPot=0;

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


				$.post('/programs/GetSelectedRadio.php', {un: tUserName}, function(response) 
				{
					tMyRadio=response;
			        $.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
			        {
						var response1=response;
				        if (response=="NG"){
					        response="";
					        response1="Unknown Call"
				        }
						$('#searchText').val(response);
						var tDX=response;
						updateInfo(response1,dxLat,dxLon);
				    });
	                $(document).on('click', '#searchButton', function() 
	                {
		                tDX=$('#searchText').val().toUpperCase();
		                $('#searchText').val(tDX);
						updateInfo(tDX);
						$.post("/programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tDX, table: "MySettings"});
	                });
	            });	
			});

            $.post('/programs/GetUserField.php',{un: tUserName, field: 'uID'}, function(response) {
	           tUser=response;
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

		    $(window).keydown(function(event){
		    	if(event.keyCode == 13) {
	                if ($('#searchText').val()==''){
	                	return false;
	                }
	                var tDX=$('#searchText').val().toUpperCase();
	                $('#searchText').val(tDX);
	                document.getElementById('searchButton').click();
					$.post("/programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tDX, table: "MySettings"});
	                return false;
		    	}
			});

            function updateInfo(dx){
	            var dxLat;
	            var dxLon;
	            if (dx=="Unknown Call"){
					$('.web-title').html('<h1>'+dx+'</h1><h3>');
	            }else{
		            $('.web-title').html('<h1>'+dx+' Web</h1><h3>');
	            }
				$.post("./programs/GetCallbook.php", {call: dx, what: 'QRZData', user: tUser, un: tUserName },function(response){
					$('.web-body').html(response);
					$.post("./programs/GetCallbook.php", {call: dx, what: 'QRZbio', user: tUser, un: tUserName},function(response){
						$('.web-bio').html('<p>'+response);
						$.post("./programs/GetCallbook.php", {call: dx, what: 'QRZpix', user: tUser, un: tUserName},function(response){
						  	var aPix=response.split('|');
						  	var h=aPix[1];
						  	var w=aPix[2];
						  	if (h>0){
							  	var wP=(aPix[2]/350);
							  	var tW=w/wP;
							  	var tH=h/wP;
					  			$(".webPix").attr("height",tH+"px");
					  			$(".webPix").attr("width",tW+"px");
					  			$(".webPix").attr("src",aPix[0]);
						  	}else{
					  			$(".webPix").attr("height","0px");
					  			$(".webPix").attr("width","0px");
							  	$(".webPix").attr("src",'about:blank');
						  	}
						  	$.post("./programs/GetCallbook.php", {call: dx, what: 'His_Latitude', user: tUser, un: tUserName},function(response){
							 	dxLat=parseFloat(response);
							 	$.post("./programs/GetCallbook.php", {call: dx, what: 'His_Longitude', user: tUser, un: tUserName},function(response){
								 	dxLon=parseFloat(response);
					                $(".map").attr("src","https://myqsx.net/programs/mapRigPi.php?call="+dx+"&latitude="+dxLat+"&longitude="+dxLon);
								    $.post("./programs/GetCallbook.php", {call: dx, what: 'Abbreviation', user: tUser, un: tUserName},function(response){
									   var abbr=response;
									   if (abbr.length>0){
										   $(".flag").attr("src","./flags/"+abbr+"-flag.gif");
									   }
									});
								});
							});
						});
				    });
				});
            }
            
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
				$.post('./programs/GetKeyerOut.php',{radio: tMyRadio, field: 'WKPot'}, function(response) {
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
			}

			var tUpdate = setInterval(updateTimer,1000);
			function updateTimer(){
				updateFooter();
				var now = new Date();
		        var now_hours=now.getUTCHours();
		        now_hours=("00" + now_hours).slice(-2);
		        var now_minutes=now.getUTCMinutes();
		        now_minutes=("00" + now_minutes).slice(-2);
                $("#fPanel5").text(now_hours+":"+now_minutes+'z');
		  	}
		  	
	  		$.getScript("/js/addPeriods.js");

    	</script>
	</head>
    <body class="body-white margin-left:auto;margin-right:auto;" id="web">
        <?php require $dRoot . "/includes/header.php"; ?>
        <div class="container-fluid">
			<div class="row">
			    <div class="col-sm-12 web-title"></div>
			</div>
			<div class="row">
				<!-- Web body -->
			    <div class="col-sm-12 col-xl-4 web-body">
				    Loading...
			    </div>
			    <div class="col-sm-12 col-xl-4 flag" >
					<img class='flag'  style="align-items:right;height:100px;width:150px;"></img><br>
				</div>
				<div class="col-sm-12 col-xl-4 web-pix" id="pixFrame">
					<img class='webPix  modal-pix' height='' width='' src=''></img>
				</div>
			</div>
			<div class="row">
				<!-- Web bio -->
			    <div class="col-sm-12 col-xl-8 web-bio">
			    </div>
				<!-- Web map -->
				<div class="col-md-12 col-xl-4 web-pix" id="mapFrame">
					<iframe class='map'  style="margin-top:20px;height:400px;width:380px" frameBorder='0' allow="fullscreen" src=''></iframe>
				</div>
			</div>
        </div>
    <?php require $dRoot . "/includes/footer.php"; ?>
		<script src="Bootstrap/popper.min.js"</script>
		<link rel="stylesheet" href="Bootstrap/jquery-ui.css">
		<script src="Bootstrap/jquery-ui.js"></script>
		<script src="Bootstrap/bootstrap.min.js"></script>
        <script src="js/nav-active.js"></script>
</body>
</html>
