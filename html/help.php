<!-- look at jQuery File Tree to get a tool that will list files -->
<?php
session_start();
$tUserName=$_SESSION['myUsername'];
$tCall=$_SESSION['myCall'];
require_once $_SERVER["DOCUMENT_ROOT"] . "/classes/Membership.php";
$membership = new Membership();
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

		<title><?php echo $tCall; ?> RigPi Help</title>
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

		<?php require $_SERVER["DOCUMENT_ROOT"] . "/includes/styles.php"; ?>

		 <script type="text/javascript">
			 var tMyRadio=1;
			 var dxLat='';
			 var dxLon='';
			 var tDX='';
            var tUserName=<?php echo "'" . $tUserName . "'"; ?>;
            var tUser='';
	        $(document).ready(function() {
				$(document).keydown(function(e){
				var t=e.key;
				e.multiple
				var w=e.which;
				if (w==191)
				{
					if (e.shiftKey){
						<?php require "/var/www/html/includes/shortcutsOther.php"; ?>
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
							showCalendar();
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
				
				$.post('./programs/GetSelectedRadio.php', {un: tUserName}, function(response) 
				{
					tMyRadio=response;
						   $.post("/programs/SetSettings.php", {field: "waitReset", radio: tMyRadio, data: 1, table: "RadioInterface"});

			        $.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
			        {
						$('#searchText').val(response);
						var tDX=response;
				    });
	            });	
				
				$.getScript("/js/modalLoad.js");

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
  				window.location.replace(url);
			    form.submit();
				
				
			};                

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
					$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tDX, table: "MySettings"});
                    return false;
                } else  {
                    return true;
                }
            });

			$(document).on('click', '#searchButton', function() 
			{
				var dx=$('#searchText').val().toUpperCase();
			    if (dx.length==0 || ~dx.indexOf('*')){
			        return;
			    }
			        $.post('/programs/GetUserField.php',{un: tUserName, field: 'uID'}, function(response) {
				        tUser=response;
						$.post("./programs/GetCallbook.php", {call: dx, what: 'QRZData', user: tUser, un: tUserName},function(response){
							$(".modal-body").html(response);
					  	$.post("./programs/GetCallbook.php", {call: dx, what: 'QRZpix', user: tUser, un: tUserName},function(response){
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
							  	$(".modal-pix").attr("src",'about:blank');
						  	}
					  		$('.modal-title').html(dx);
					  		$('#myModal').modal({show:true});
					    });
					});
					$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: dx, table: "MySettings"});
			    })
			
			});
			
			$(document).on('click', '#logoutButton', function() {
				openWindowWithPost("/login.php", {
				    status: "loggedout",
				    username: tUserName});
			});
});
			
			var tUpdate = setInterval(updateTimer,100);
			function updateTimer(){
		        var now = new Date();
		        var now_hours=now.getUTCHours();
		        now_hours=("00" + now_hours).slice(-2);
		        var now_minutes=now.getUTCMinutes();
		        now_minutes=("00" + now_minutes).slice(-2);
		        $("#time").text(now_hours+":"+now_minutes+' utc');
		  	}

//			function relativeRedir(redir){
//			  location.pathname = location.pathname.replace(/(.*)\/[^/]*/, "$1/"+redir);
//			}
    	</script>
	</head>
<body class="noScroll" id="help">
	<?php require "./includes/header.php"; ?>
	<iframe src='./Help/RigPi.html' height='1000' width='100%'></iframe>
    <?php require dirname(__FILE__) . "/includes/modal.txt"; ?>
<?php require dirname(__FILE__) . "/includes/modalCancelOnly.txt"; ?>

 		<script src="Bootstrap/popper.min.js"</script>
		<link rel="stylesheet" href="Bootstrap/jquery-ui.css">
		<script src="Bootstrap/jquery-ui.js"></script>
		<script src="Bootstrap/bootstrap.min.js"></script>
        <script src="js/nav-active.js"></script>
</body>
