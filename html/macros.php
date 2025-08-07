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
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo $tCall; ?> RigPi Macro Settings</title>
	<meta name="RigPi Macro Settings" content="">
	<meta name="author" content="Howard Nurse, W6HN">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">
    <script src="/Bootstrap/jquery.min.js" ></script>
	<script defer src="./awe/js/all.js" ></script>
	<link href="./awe/css/all.css" rel="stylesheet">
	<link href="./awe/css/fontawesome.css" rel="stylesheet">
	<link href="./awe/css/solid.css" rel="stylesheet">	
	<script src="/js/FileSaver.js"></script>

	<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="apple-touch-icon" href="/apple-touch-icon.png">
	<?php require $dRoot . "/includes/styles.php"; ?>
	<script>
		var tRadioName="";
 	  	var tMyCall='<?php echo $tCall; ?>';
 	  	var tCall=tMyCall;
 	  	var tMyRadio='';
        var tUserName=<?php echo "'" . $tUserName . "'"; ?>;
        var tUser='';
		var formdata = false;
		var mBank=1;
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

			$.post('/programs/GetSelectedRadio.php', {un:tUserName}, function(response) 
			{
				tMyRadio=response;
					$.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'RadioName', table: 'MySettings'}, function(response)
					{
						tRadioName=response;
					});
					$.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'MacroBankTuner'}, function(response){ 
						mBank=response;
						var tCap="Macro Settings  (Bank: "+mBank+" User: <?php echo $tUserName; ?>)";
						$('#topCaption').text(tCap);
						loadMacros(mBank);
					});
		        $.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
		        {
					$('#searchText').val(response);
			    });
			});

		    $(document).on('click', '.export', function() {
		        var tAsk='';
			    tAsk='Download Macros?';
				if (confirm(tAsk)){
					$('#downloadModal').modal('show'); 
					getDownloadCount=1;
			        var tMacros="";
					for (i = 0; i < 32; i++) {
						var mIDV='m'+(i+1)+'V';
						var mIDL='m'+(i+1)+'L';
						var mac =document.getElementById(mIDL);
						tMacros=tMacros+mac.value+'|';
						var mac =document.getElementById(mIDV);
						tMacros=tMacros+mac.value+'~';
					}
					tMacros=encodeURIComponent(tMacros);
					tMacros=tMacros.replace("'","%27");
					var tMacroFile="rigpiMacroBank"+mBank+"_"+tUserName+"_"+tMyRadio+"_"+tRadioName+"_"+Date.now()+".txt";
					var blob = new Blob([tMacros],{type: "text/plain,charset=utf-8"});
					saveAs(blob, tMacroFile);
						$('#dlresult').html("Macro download: "+tMacroFile);

//					document.getElementById("dlresult").src="http://rigpi4.local/my/downloads/t.mac";

/*					$.post("./programs/exportMacros.php",{uid: tUserName, macro: tMacros, bank: mBank, radio: tMyRadio}, function(response){
							
						if (response=="0"){
							$('#dlresult').html("No macros found to export.");
							getDownloadCount=0;
						}else{
							
							var dlsrc = "/my/downloads/"+response;
							document.getElementById("downloadFrame").src = dlsrc;
							getDownloadCount=0;
						}
						$('#dlresult').html("Macro download: "+response);

					});
*/
};
			//	};
			});
	
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
	                if(!!file.name.match(/.*\.txt$/)){
	                    if (formdata) {
	                        formdata.append("files[]", file);
	                    }
	                } else {
	                    $("#input_file").val('').prop('disabled',false);
								$("#modalA-body").html(file.name+" is not a Macro txt file!");			  				
								$("#modalA-title").html("Incorrect Format");
							  	$("#myModalAlert").modal({show:true});//			  				alert(response);
	                }
	        });
	        
	        $('#btn_submit').on('click',function(event){ 
	            if (formdata) {
	                $.ajax({
	                    url: "./my/uploadMacro.php",
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
	                                getUploadCount=1;
	                                $.post('./programs/importMacros.php',{file:buf,radio:tMyRadio},function(response){
										var tMacros=decodeURIComponent((response+'').replace(/\+/g,'%20'));
										var aMacros=tMacros.split('~');
										for (i = 0; i < 32; i++) {
											var mIDV='m'+(i+1)+'V';
											var mIDL='m'+(i+1)+'L';
											var tLabel = aMacros[i];
											tLabel=tLabel.split('|');
											var mac =document.getElementById(mIDL);
											var t = tLabel[0];
											mac.value=t;
											var mac =document.getElementById(mIDV);
											mac.value=tLabel[1];
										}				
										saveMacros(); 
										$('#uploadModal').modal('hide');
	                                });
	                            }else{
	                                $('#ulresult').html("File not specified.");
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

			$(document).on('click', '.import', function() {
				$('#uploadModal').modal('show'); 
			});

			$(document).on('click', '.copy', function() {
				copyMacros();
				saveMacros(); 
			});

			$(document).on('click', '.b1', function() {
				loadMacros(1);
				mBank=1;
//				saveMacros(); 
				var tCap="Macro Settings  (Bank: "+mBank+" User: <?php echo $tUserName; ?>)";
				$('#topCaption').text(tCap);
  				$.post("/programs/SetSettings.php", {field: "MacroBankTuner", radio: tMyRadio, data: mBank, table: "RadioInterface"});				
			});

			$(document).on('click', '.b2', function() {
				loadMacros(2);
				mBank=2;
//				saveMacros(); 
				var tCap="Macro Settings  (Bank: "+mBank+" User: <?php echo $tUserName; ?>)";
				$('#topCaption').text(tCap);
  				$.post("/programs/SetSettings.php", {field: "MacroBankTuner", radio: tMyRadio, data: mBank, table: "RadioInterface"});				
			});

			$(document).on('click', '.b3', function() {
				loadMacros(3);
				mBank=3;
//				saveMacros(); 
				var tCap="Macro Settings  (Bank: "+mBank+" User: <?php echo $tUserName; ?>)";
				$('#topCaption').text(tCap);
  				$.post("/programs/SetSettings.php", {field: "MacroBankTuner", radio: tMyRadio, data: mBank, table: "RadioInterface"});				
			});

			$(document).on('click', '.b4', function() {
				loadMacros(4);
				mBank=4;
//				saveMacros(); 
				var tCap="Macro Settings  (Bank: "+mBank+" User: <?php echo $tUserName; ?>)";
				$('#topCaption').text(tCap);
  				$.post("/programs/SetSettings.php", {field: "MacroBankTuner", radio: tMyRadio, data: mBank, table: "RadioInterface"});				
			});
			
			$(document).on('click', '.defMacro', function() {
               $.post('./programs/setMacroDefault.php',{bank: mBank,radio:tMyRadio},function(response){
					var tMacros=decodeURIComponent((response+'').replace(/\+/g,'%20'));
					var aMacros=tMacros.split('~');
					for (i = 0; i < 32; i++) {
						var mIDV='m'+(i+1)+'V';
						var mIDL='m'+(i+1)+'L';
						var tLabel = aMacros[i];
						tLabel=tLabel.split('|');
						var mac =document.getElementById(mIDL);
						var t = tLabel[0];
						mac.value=t;
						var mac =document.getElementById(mIDV);
						mac.value=tLabel[1];
					}				
					saveMacros(); 
				})
			});

			$(document).on('click', '.myColor', function() {
				var id = $(this).attr('id');
				if ($('#m1D').hasClass('btn-primary')){
					$('#m1D').removeClass('btn-primary');
				}else if ($('#m1D').hasClass('btn-danger')){
					$('#m1D').removeClass('btn-danger');
				}else if ($('#m1D').hasClass('btn-warning')){
					$('#m1D').removeClass('btn-warning');
				}else if ($('#m1D').hasClass('btn-success')){
					$('#m1D').removeClass('btn-success');
				}else if ($('#m1D').hasClass('btn-default')){
					$('#m1D').removeClass('btn-default');
				}else if ($('#m1D').hasClass('btn-default')){
					$('#m1D').removeClass('btn-default');
				}
                $('#m1D').addClass(id);
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
			
			$.getScript("/js/modalLoad.js");

		});
		

        function loadMacros(which){
            $.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'Macros'+which}, function(response){ 
				var tMacros=decodeURIComponent((response+'').replace(/\+/g,'%20'));
				var aMacros=tMacros.split('~');
				for (i = 0; i < 32; i++) {
					var mIDV='m'+(i+1)+'V';
					var mIDL='m'+(i+1)+'L';
					var tLabel = aMacros[i];
					tLabel=tLabel.split('|');
					var mac =document.getElementById(mIDL);
					var t = tLabel[0];
					mac.value=t;
					var mac =document.getElementById(mIDV);
					mac.value=tLabel[1];
				}				
			})
        }

        function copyMacros(){
            $.post('/programs/GetInterface.php',{radio: tMyRadio, field: 'Macros1'}, function(response){ 
				var tMacros=decodeURIComponent((response+'').replace(/\+/g,'%20'));
				var aMacros=tMacros.split('~');
				for (i = 0; i < 32; i++) {
					var mIDV='m'+(i+1)+'V';
					var mIDL='m'+(i+1)+'L';
					var tLabel = aMacros[i];
					tLabel=tLabel.split('|');
					var mac =document.getElementById(mIDL);
					var t = tLabel[0];
					mac.value=t;
					var mac =document.getElementById(mIDV);
					mac.value=tLabel[1];
				}				
			})
        }

        function saveMacros(){
	        var tMacros="";
			for (i = 0; i < 32; i++) {
				var mIDV='m'+(i+1)+'V';
				var mIDL='m'+(i+1)+'L';
				var mac =document.getElementById(mIDL);
				tMacros=tMacros+mac.value+'|';
				var mac =document.getElementById(mIDV);
				tMacros=tMacros+mac.value+'~';
			}
			tMacros=encodeURIComponent(tMacros);
			$.post("/programs/SetSettings.php", {field: 'Macros'+mBank, data: tMacros, radio:tMyRadio, table:'RadioInterface'});
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

<body class="body-black-scroll" id="macros">
	<?php require $dRoot . "/includes/header.php"; ?>
	<div class="container-fluid">
		<div class="row" style="margin-bottom:10px;">
			<div class="col-2 text-center">
			</div>
			<div class="col-sm-8 text-center">
				<span class="label label-success text-white" style="cursor: default; margin-top:10px;" id="topCaption"></span>
			</div>
			<div class="col-2 col-lg-1 btn-padding">
	            <div class="dropdown">
					<button class='btn btn-color dropdown-toggle dropdown-ham hButton' type='button' title='Export/Import Actions' data-toggle="dropdown">
						<i class='fas fa-bars fa-fw fa-lg'></i>
					</button>
				    <ul class="dropdown-menu dropdown-menu-right menu-scroll" id="fnList">
						<div class='ex'>
							<li id='bank1'><a class='dropdown-item b1' id='b1' href='#'>Load Bank 1</a></li>
							<li id='bank2'><a class='dropdown-item b2' id='b2' href='#'>Load Bank 2</a></li>
							<li id='bank3'><a class='dropdown-item b3' id='b3' href='#'>Load Bank 3</a></li>
							<li id='bank4'><a class='dropdown-item b4' id='b4' href='#'>Load Bank 4</a></li>
							<div class="dropdown-divider"></div>
							<li id='default'><a class='dropdown-item defMacro' id='defMacro' href='#'>Set this Bank to default macro</a></li>
							<li id='export'><a class='dropdown-item export' id='fn' href='#'>Save this Bank</a></li>
							<li id='import'><a class='dropdown-item import' id='fn' href='#uploadModel'>Restore this Bank</a></li>
							<li id='copy'><a class='dropdown-item copy' id='fn1' href='#'>Copy from Bank 1</a></li>
						</div>
				    </ul>
	            </div>
			</div>
		</div>
		<hr>	
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon" id="m1Q">Button 1</span>
					</div>
					<input type="text" onfocusout="saveMacros()" class="form-control" placeholder=""  data-index="3" id="m1L" aria-lable="macro" aria-describedby="macro-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
					<span class="input-group-text">Macro 1</span>
					</div>
					<input type="text" onfocusout="saveMacros()" class="form-control" placeholder=""  data-index="4" id="m1V" aria-lable="m1V" aria-describedby="m1V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 2</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="5" id="m2L" aria-lable="m2L" aria-describedby="m2L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Macro 2</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="6" id="m2V" aria-lable="m2V" aria-describedby="m2V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 3</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="7" id="m3L" aria-lable="m3L" aria-describedby="m3L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Macro 3</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="8" id="m3V" aria-lable="m3V" aria-describedby="m3V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 4</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="9" id="m4L" aria-lable="m4L" aria-describedby="m4L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Macro 4</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="10" id="m4V" aria-lable="m4V" aria-describedby="m4V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 5</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="11" id="m5L" aria-lable="m5L" aria-describedby="m5L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Macro 5</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="12" id="m5V" aria-lable="m5V" aria-describedby="m5V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 6</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="13" id="m6L" aria-lable="m6L" aria-describedby="m6L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"\>Macro 6</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="14" id="m6V" aria-lable="m6V" aria-describedby="m6V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 7</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="15" id="m7L" aria-lable="m7L" aria-describedby="m7L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Macro 7</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="16" id="m7V" aria-lable="m7V" aria-describedby="m7V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 8</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="17" id="m8L" aria-lable="m8L" aria-describedby="m8L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"\>Macro 8</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="18" id="m8V" aria-lable="m8V" aria-describedby="m8V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 9</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="19" id="m9L" aria-lable="m9L" aria-describedby="m9L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Macro 9</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="20" id="m9V" aria-lable="m9V" aria-describedby="m9V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 10</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="21" id="m10L" aria-lable="m10L" aria-describedby="m10L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"\>Macro 10</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="22" id="m10V" aria-lable="m10V" aria-describedby="m10V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 11</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="23" id="m11L" aria-lable="m11L" aria-describedby="m11L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<span class="input-group-text">Macro 11</span>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="24" id="m11V" aria-lable="m11V" aria-describedby="m11V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<span class="input-group-text macro-group-addon">Button 12</span>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="25" id="m12L" aria-lable="m12L" aria-describedby="m12L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"\>Macro 12</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="26" id="m12V" aria-lable="m12V" aria-describedby="m12V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 13</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="27" id="m13L" aria-lable="m13L" aria-describedby="m13L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Macro 13</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="28" id="m13V" aria-lable="m13V" aria-describedby="m13V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 14</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="29" id="m14L" aria-lable="m14L" aria-describedby="m14L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"\>Macro 14</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="30" id="m14V" aria-lable="m14V" aria-describedby="m14V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 15</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="31" id="m15L" aria-lable="m15L" aria-describedby="m15L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Macro 15</span>
					</div>
					<input type="text" onfocusout="saveMacros()" class="form-control" placeholder="" data-index="32" id="m15V" aria-lable="m15V" aria-describedby="m15V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 16</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="33" id="m16L" aria-lable="m16L" aria-describedby="m16L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"\>Macro 16</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="34" id="m16V" aria-lable="m16V" aria-describedby="m16V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 17</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="35" id="m17L" aria-lable="m17L" aria-describedby="m17L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Macro 17</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="36" id="m17V" aria-lable="m17V" aria-describedby="m17V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 18</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="37" id="m18L" aria-lable="m18L" aria-describedby="m18L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"\>Macro 18</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="38" id="m18V" aria-lable="m18V" aria-describedby="m18V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 19</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="39" id="m19L" aria-lable="m19L" aria-describedby="m19L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Macro 19</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="40" id="m19V" aria-lable="m19V" aria-describedby="m19V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 20</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="41" id="m20L" aria-lable="m20L" aria-describedby="m20L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"\>Macro 20</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="42" id="m20V" aria-lable="m20V" aria-describedby="m20V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 21</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="43" id="m21L" aria-lable="m21L" aria-describedby="m21L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Macro 21</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="44" id="m21V" aria-lable="m21V" aria-describedby="m21V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 22</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="45" id="m22L" aria-lable="m22L" aria-describedby="m22L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"\>Macro 22</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="46" id="m22V" aria-lable="m22V" aria-describedby="m22V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 23</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="47" id="m23L" aria-lable="m23L" aria-describedby="m23L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Macro 23</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="48" id="m23V" aria-lable="m23V" aria-describedby="m23V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 24</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="49" id="m24L" aria-lable="m24L" aria-describedby="m24L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"\>Macro 24</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="50" id="m24V" aria-lable="m24V" aria-describedby="m24V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 25</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="51" id="m25L" aria-lable="m25L" aria-describedby="m25L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Macro 25</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="52" id="m25V" aria-lable="m25V" aria-describedby="m25V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 26</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="53" id="m26L" aria-lable="m26L" aria-describedby="m26L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"\>Macro 26</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="54" id="m26V" aria-lable="m26V" aria-describedby="m26V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 27</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="55" id="m27L" aria-lable="m27L" aria-describedby="m27L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Macro 27</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="56" id="m27V" aria-lable="m27V" aria-describedby="m27V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 28</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="57" id="m28L" aria-lable="m28L" aria-describedby="m28L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"\>Macro 28</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="58" id="m28V" aria-lable="m28V" aria-describedby="m28V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 29</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="59" id="m29L" aria-lable="m29L" aria-describedby="m29L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Macro 29</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="60" id="m29V" aria-lable="m29V" aria-describedby="m29V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 30</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="61" id="m30L" aria-lable="m30L" aria-describedby="m30L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text"\>Macro 30</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="62" id="m30V" aria-lable="m30V" aria-describedby="m30V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 31</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="63" id="m31L" aria-lable="m31L" aria-describedby="m31L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Macro 31</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="64" id="m31V" aria-lable="m31V" aria-describedby="m31V-addon">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text macro-group-addon">Button 32</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="65" id="m32L" aria-lable="m32L" aria-describedby="m32L-addon">
				</div>
			</div>
			<div class="col-md-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text">Macro 32</span>
					</div>
					<input type="text"  onfocusout="saveMacros()" class="form-control" placeholder="" data-index="66" id="m32V" aria-lable="m32V" aria-describedby="m32V-addon">
				</div>
			</div>
		</div>
		<p>
		<!-- The Upload Modal -->
		<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog">
		    <div class="modal-dialog" role="document">
		    	<div class="modal-content">
					<div class="modal-header">
						<h2 class="modal-title">Upload/Import Macro</h2>
						<button type="button" class="close" id = "closeModal" data-dismiss="modal">&times;</button>
						</button>
		      		</div>
			  		<div class="modal-body">
						<form action="./my/upload.php" method="POST" role="form" class="form-horizontal">
							<div class="input-group">
								<div class="custom-file">
								    <input type="file" class="custom-file-input" id="input_file" aria-describedby="inputGroupFileAddon04">
								    <label class="custom-file-label" for="input_file">Choose file</label>
								</div>
								<div class="input-group-append">
								    <button class="btn btn-outline-primary" type="button" id="btn_submit">Upload/Import</button>
								</div>
							</div>					
						</form>
	 			        <div id="ulnumber"></div>
				        <div id="ulresult"></div>
		    		</div>
					<div class="modal-footer">
						<button class="importClose btn btn-primary" type="button" data-dismiss="modal">Close</button>
		    		</div>
		    	</div>
		  	</div>
		</div>

		<!-- The Download Modal -->
		<div class="modal fade" id="downloadModal" tabindex="-1" role="dialog">
		    <div class="modal-dialog" role="document">
		    	<div class="modal-content">
					<div class="modal-header">
						<h2 class="dlmodal-title">Download Macros</h2>
						<button type="button" class="close" id = "closeDLModal" data-dismiss="modal">&times;</button>
						</button>
		      		</div>
			  		<div class="dlmodal-body">
	 			        <div class="spacer" id="dlnumber"></div>
				        <div class="spacer" id="dlresult"></div>
		    		</div>
					<div class="modal-footer">
						<button class="exportClose btn btn-primary" type="button" data-dismiss="modal">Close</button>
		    		</div>
		    	</div>
		  	</div>
		</div>
	</div>
	<iframe id="downloadFrame" style="display:none"></iframe>
    <?php require $dRoot . "/includes/footer.php"; ?>
<?php require $dRoot . "/includes/modal.txt"; ?>
<?php require $dRoot . "/includes/modalCancelOnly.txt"; ?>
<script src="./Bootstrap/popper.min.js"</script>
<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
<script src="./Bootstrap/jquery-ui.js"></script>
<script src="./Bootstrap/bootstrap.min.js"></script>
</body>
</html>
