<!-- look at jQuery File Tree to get a tool that will list files -->
<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/classes/Membership.php');
$membership = new Membership();
$membership->confirm_Member();
$tUserName=$_SESSION['name'];
require_once('./includes/ackStyles.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<html lang="en">
	<head>
		<meta charset="utf-8">

		<!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
		Remove this if you use the .htaccess -->
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

		<title><?php echo $_SESSION['call'];?> RigPi Log</title>
		<meta name="description" content="">
		<meta name="author" content="Howard Nurse">

		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
		<!-- Bootstrap CSS -->
		<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
		<link rel="shortcut icon" href="/favicon.ico">
		<link rel="apple-touch-icon" href="/favicon.ico">
		<link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">
		<script src="./Bootstrap/jquery-3.2.1.js" ></script>
		<script defer src="./awe/js/all.js" ></script>
		<link href="./awe/css/all.css" rel="stylesheet">
		<link href="./awe/css/fontawesome.css" rel="stylesheet">
		<link href="./awe/css/solid.css" rel="stylesheet">	

		 <script type="text/javascript">
			 var tMyRadio=1;
			 var dxLat='';
			 var dxLon='';
			 var tDX='';
            var tUserName=<?php echo "'".$tUserName."';"?>;
            var tUser='';
	        $(document).ready(function() {
				$.post('./programs/GetSelectedRadio.php', {un: tUserName}, function(response) 
				{
					tMyRadio=response;
			        $.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
			        {
						$('#searchText').val(response);
						var tDX=response;
				    });
	            });	
				$.getScript("/js/modalLoad.js");
			});

			$(document).on('click', '#logoutButton', function() {
  				$.post('./login.php',{status:'loggedout'});
  				window.location.replace("login.php");
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
			
			var tUpdate = setInterval(updateTimer,100);
			function updateTimer(){
		        var now = new Date();
		        var now_hours=now.getUTCHours();
		        now_hours=("00" + now_hours).slice(-2);
		        var now_minutes=now.getUTCMinutes();
		        now_minutes=("00" + now_minutes).slice(-2);
		        $("#time").text(now_hours+":"+now_minutes+' utc');
		  	}

    	</script>
	</head>
    <body class="margin-left:auto;margin-right:auto;" id="ack">
        <?php require(dirname(__FILE__) . "/includes/header.php");?>
        <div class="container-fluid">
<center>The MIT License (MIT)</center>
<p>
<center>Copyright (c) 2018 Howard Nurse, W6HN.</center>
<p><p>
Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:
<p>
The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.
<p>
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
    <?php require(dirname(__FILE__) . '/includes/modal.txt'); ?>
    <div class="row">
    <div class="mx-auto" style="color: white; margin-top:100px; width=400px;">
        </div>
 		<script src="Bootstrap/popper.min.js"</script>
		<link rel="stylesheet" href="Bootstrap/jquery-ui.css">
		<script src="Bootstrap/jquery-ui.js"></script>
		<script src="Bootstrap/bootstrap.min.js"></script>
        <script src="js/nav-active.js"></script>
</body>
