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
require $dRoot . "/programs/getLogStyles.php";
require_once $dRoot . "/programs/GetSettingsFunc.php";
$tRadio = include $dRoot . "/programs/GetSelectedRadioInc.php";
$logName = GetField($tRadio, "LogName", "MySettings");
$logStyle = GetField($tRadio, "LogStyle", "MySettings");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<meta charset="utf-8">

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo $tCall; ?> RigPi Log Designer</title>
	<meta name="RigPi Log Designer" content="">
	<meta name="author" content="Howard Nurse, W6HN">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">
	<script defer src="./awe/js/all.js" ></script>
	<link href="./awe/css/all.css" rel="stylesheet">
	<link href="./awe/css/fontawesome.css" rel="stylesheet">
	<link href="./awe/css/solid.css" rel="stylesheet">	
	<script src="/Bootstrap/jquery.min.js" ></script>
	<script src='js/jquery-sortable.js'></script>
	<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="apple-touch-icon" href="/apple-touch-icon.png">
	 <link rel='stylesheet' href='./includes/jquery-ui.css'>
	<?php require $dRoot . "/includes/styles.php"; ?>
	<script>
	////
  		var tMyRadio='1';
  		var tMyCall="<?php echo $tCall; ?>";
  		var tCall=tMyCall;
  		var tMyKeyer="non";
  		var tMyRadioName='';
    	var fields2=""; //list for editor
    	var fields3=""; //list for log
    	var alias="";
  		var currentStyle='<?php echo $logStyle; ?>';
  		if (currentStyle==""){
	  		currentStyle="General";
  		}
        var tUserName="<?php echo $tUserName; ?>";
        var tUser='';
        var tNewStyle=0;
        var skipRefresh=0;
        var dragSource='';
  		$(document).ready(function()
  		{
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

						

	                    if ($('#saveasValue').val().length>0){
							var saveStyle = $("#saveasValue").val();
							$("#saveasValue").val('');
							alias="MobileID";
							$.post('/programs/SetStyle.php',{style: currentStyle, fields: '', value: '', operation: 'SA', from: 'Logbook', orderList: 'OrderValueLog', for: saveStyle}, function(response){
								currentStyle=saveStyle;
								$("#curStyle").val(currentStyle);
								getLists();
							});
	                    }
						return false;
	            });

		        $.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
		        {
					$('#searchText').val(response);
			    });

			   	$(document).on('click', '.mystyle', function()
			   	{
					currentStyle = $(this).text();
	  				$("#curStyle").val(currentStyle);
	  				getLists();
				});

			   	$(document).on('click', '#newStyle', function()
			   	{
					var newStyle = $("#newValue").val();
					alias="MobileID";
					tNewStyle=1;
					$.post('./programs/SetStyle.php',{style: newStyle, fields: alias, value: '', operation: 'N', from: 'Logbook', orderList: 'OrderValueLog', for: ''}, function(response){
						currentStyle=newStyle;
						$("#curStyle").val(currentStyle);
						getLists();
					});
				});

			   	$(document).on('click', '#saveasSel', function()
			   	{
					var saveStyle = $("#saveasValue").val();
					alias="MobileID";
					$.post('/programs/SetStyle.php',{style: currentStyle, fields: '', value: '', operation: 'SA', from: 'Logbook', orderList: 'OrderValueLog', for: saveStyle}, function(response){
						alert(response);
						currentStyle=saveStyle;
						$("#curStyle").val(currentStyle);
						getLists();
					});
				});

				$(document).on('click', '.myAttr', function()
				{
					var text = $(this).text();
					if (text=='NONE'){
						text='';
					}
					$('#Attribute').val(text);
					setStyleFields('Attribute');
				});
						
$(document).on('click', '#searchButton', function()
{
	doSearch();
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
					$(".modal-pix").attr("src",'about:blank');
				}
				$('.modal-title').html(tDX);
				  $('#myModal').modal({show:true});
				  $('#myModal').focus();
			  });
		});
		$.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tDX, table: "MySettings"});
	}
}


		   	});

$(document).keydown(function(e){
var t=e.key;
e.multiple
var w=e.which;
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
};if (w == 27) { 
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

$.post('/programs/GetUserField.php',{un: tUserName, field: 'uID'}, function(response) {
	tUser=response;
})

 		   	getLists();
	  		$("#curStyle").val(currentStyle);


	   	function getLists(){
		   	var tField='';
			$("ol.connectedSortable").sortable("destroy");
			$.post("/programs/GetLogFieldList.php", {style: currentStyle, target: 'Logbook'},function(response)
			{
				$('#lFields').empty();
			    $('#lFields').append(response);
			    $('#lFields').show();
				$.post("/programs/GetLogFieldList.php", {style: currentStyle, target: 'LogEditor'},function(response)
				{
					$('#oFields').empty();
				    $('#oFields').append(response);
				    $('#oFields').show();
					$.post("/programs/GetLogFieldList.php", {style: currentStyle, target: 'source'},function(response)
					{
						$('#mFields').empty();
					    $('#mFields').append(response);
					    $('#mFields').show();
			
						$('.Logbook').on('click', function() //this is delete
						{
							skipRefresh=0;
						    var $this = $(this);
						    alias=$this.attr('id');
						    var tStyle=currentStyle;
							$.post('/programs/SetStyle.php',{style: currentStyle, fields: alias, value: '', operation: 'D', from: 'Logbook', orderList: 'OrderValueLog', for: ''}, function(response){
								setTimeout(function(){
									getLists();
								}, 500);
							});
						});
					
						$(".LogEditor").on('click',function() // this is delete
						{
							skipRefresh=0;
						    var $this = $(this);
						    alias=$this.attr('id');
						    var tStyle=currentStyle;
							$.post('/programs/SetStyle.php',{style: currentStyle, fields: alias, value: '', operation: 'D', from: 'LogEditor', orderList: 'OrderValueEdit', for: ''}, function(response){
								setTimeout(function(){
									getLists();
								}, 500);
							})
						});
							
						$('.list-group-item').on('click', function() {
							if (skipRefresh==1){
								skipRefresh=0;
								return;
							}
						    var $this = $(this);
						    alias=$this.data('alias');
						    $('.active').removeClass('active');
						    $this.toggleClass('active')
					        $.post('/programs/getLogStyle.php',{style: currentStyle, row: alias, field:'DefaultValue', new:tNewStyle}, function(response){
						        var data=JSON.parse(response);
						        $("#Label").val(data[0]);
						        $("#DefaultValue").val(data[1]);
						        $("#Attribute").val(data[2]);
						        $("#ListContents").val(data[3]);
						        $("#Notes").val(data[4]);
						        $("#Prompt").val(data[6]);
						        if (tNewStyle==1){
							        setStyleFields('Label');
							        setStyleFields('Attribute');
							        setStyleFields('ListContents');
							        setStyleFields('Notes');
							        setStyleFields('Prompt');
						        };						        
					        });
						});
						$(function  () {
							$("ol.connectedSortable").sortable();
						});					
				
						$("ol.connectedSortable").sortable({ //editor fields
							group: 'connectedSortable',
							handle: '#move',
							pullPlaceholder: false,
							  // animation on drop
							onDrop: function ($item, container, _super) {
							    var $clonedItem = $('<li/>').css({height: 0});
							    $item.before($clonedItem);
							    $clonedItem.animate({'height': $item.height()});
								var dest=container.el[0].id;
								tField=$item.data('alias');
								var sour=dragSource; //container.group.itemContainer.el[0].id;
							    $clonedItem.detach();
								container.el.removeClass("active");
						    	var sList="";
								var sorted2 = $( "#sortable2" ).sortable( "serialize");
								$.each(sorted2, function(key,value){
									sList='';
									$.each(value, function(key1,value1){
											sList+=value1["alias"]+'+';										
									});
									var sList1=sList.replace("undefined+", "");
									fields2=sList1.split("+");
								});
								var sorted3 = $( "#sortable3" ).sortable( "serialize");
								$.each(sorted3, function(key,value){
									sList='';
									$.each(value, function(key1,value1){
											sList+=value1["alias"]+'+';										
									});
									var sList1=sList.replace("undefined+", "");
									fields3=sList1.split("+");
								});
								if (dest=="sortable3" && sour=="sortable3"){
							        $.post('/programs/SetStyle.php',{style: currentStyle, fields: fields3, operation: 'N', from: 'Logbook', orderList: 'OrderValueLog', value: '', for: tField}, function(response){
										setTimeout(function(){
									        getLists();
										}, 500);
								    });
							    }
								if (dest=="sortable3" && sour=="sortable1"){
							        $.post('/programs/SetStyle.php',{style: currentStyle, fields: fields3, operation: 'N', from: 'Logbook', orderList: 'OrderValueLog', value: '', for: ''}, function(response){
										tNewStyle=1;
								        $.post('/programs/getLogStyle.php',{style: currentStyle, row: alias, field:'DefaultValue', new:tNewStyle}, function(response){
									        var data=JSON.parse(response);
									        $("#Label").val(data[0]);
									        $("#DefaultValue").val(data[1]);
									        $("#Attribute").val(data[2]);
									        $("#ListContents").val(data[3]);
									        $("#Notes").val(data[4]);
									        $("#ADIFTag").val(data[5]);
									        $("#Prompt").val(data[6]);
									        if (tNewStyle==1){
										        setStyleFields('Label');
										        setStyleFields('Attribute');
										        setStyleFields('ListContents');
										        setStyleFields('Notes');
										        setStyleFields('Prompt');
									        }
											setTimeout(function(){
												getLists();
											}, 500);
											tNewStyle=0;
										});
								    });
							    }
								if (dest=="sortable2" && sour=="sortable2"){
							        $.post('/programs/SetStyle.php',{style: currentStyle, fields: fields2, operation: 'N', from: 'LogEditor', orderList: 'OrderValueEdit', value: '', for: ''}, function(response){
										setTimeout(function(){
											getLists();
										}, 500);
								    });
							    }
								if (dest=="sortable1" && sour=="sortable1"){
							        $.post('/programs/SetStyle.php',{style: currentStyle, fields: fields2, operation: 'N', from: 'LogEditor', orderList: 'OrderValueEdit', value: '', for: ''}, function(response){
										setTimeout(function(){
											getLists();
										}, 500);
								    });
							    }
								if (dest=="sortable2" && sour=="sortable1"){
							        $.post('/programs/SetStyle.php',{style: currentStyle, fields: fields2, operation: 'N', from: 'LogEditor', orderList: 'OrderValueEdit', value: '', for: ''}, function(response){getLists()
										tNewStyle=1;
								        $.post('/programs/getLogStyle.php',{style: currentStyle, row: alias, field:'DefaultValue', new:tNewStyle}, function(response){
									        var data=JSON.parse(response);
									        $("#Label").val(data[0]);
									        $("#DefaultValue").val(data[1]);
									        $("#Attribute").val(data[2]);
									        $("#ListContents").val(data[3]);
									        $("#Notes").val(data[4]);
									        $("#ADIFTag").val(data[5]);
									        $("#Prompt").val(data[6]);
									        if (tNewStyle==1){
										        setStyleFields('Label');
										        setStyleFields('Attribute');
										        setStyleFields('ListContents');
										        setStyleFields('Notes');
										        setStyleFields('Prompt');
									        }
											setTimeout(function(){
												getLists();
											}, 500);
									        tNewStyle=0;
										});
							        });
							    }
								_super($item, container);
							},
							
							  // set $item relative to cursor position
							onDragStart: function ($item, container, _super) {
							    var offset = $item.offset(),
							    pointer = container.rootGroup.pointer;
								dragSource=container.el[0].id;
							    adjustment = {
							    	left: pointer.left - offset.left,
									top: pointer.top - offset.top
							    };
							
							    _super($item, container);
							},
							  
							onDrag: function ($item, position) {
							    $item.css({
							    	left: position.left - adjustment.left,
									top: position.top - adjustment.top
							    });
							}
						});
					});
				});
			});
		};

		function setStyleFields(which){
			var newVal=document.getElementById(which).value;
				$.post('/programs/SetStyle.php',{style: currentStyle, fields: which, operation: 'F', from: 'LogEditor', orderList: 'OrderValueEdit', value: newVal, for: alias}, function(response){
					var a=response;
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

	  	$.getScript("/js/addPeriods.js");

	</script>
</head>

<body class="body-black" >
	<?php require $dRoot . "/includes/header.php"; ?>
	<div class="container-fluid">
		<div class="row" style="margin-top:10px;">
			<div class="col-sm-12 text-center">
				<div class="label label-success text-white pageLabel" style='cursor: default;' >RigPi Log Designer</div>
				<hr>
			</div>
		</div>
		<div class="row" style="margin-top:20px;">
			<div class="col-lg-4" style="margin-top:5px;">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text log-group-addon">Style</span>
					</div>
					<input type="text" class="form-control disable-text" id="curStyle" aria-lable="style" aria-describedby="style-addon">
				    <span class="input-group-btn">
			            <div class="dropdown">
						    <button class="btn btn-primary dropdown-toggle" id="styleSel" data-size="3" type="button"  title="Select Style" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i>
						    </button>
						    <ul class="dropdown-menu dropdown-menu-right menu-scroll" onfocusout="setStyleFields(styleList)" id="styleList">
								<?php echo getLogStyles(); ?>
						     </ul>
			            </div>
				    </span>
				</div>
			</div>
			<div class="col-lg-4" style="margin-top:5px;">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text log-group-addon" >New</span>
					</div>
					<input type="text" class="form-control" id="newValue" aria-lable="new" aria-describedby="new-addon">
				    <span class="input-group-btn">
			            <div class="button">
						    <button class="btn btn-primary" id="newStyle" data-size="3" type="button"  title="Name New Style">OK</button>
			            </div>
				    </span>
				</div>
			</div>
			<div class="col-lg-4" style="margin-top:5px;">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text log-group-addon" >Save As</span>
					</div>
					<input type="text" class="form-control" id="saveasValue" aria-lable="saveas" aria-describedby="saveas-addon">
				    <span class="input-group-btn">
			            <div class="button">
						    <button class="btn btn-primary" id="saveasSel" data-size="3" type="button"  title="Save As Name">OK</button>
			            </div>
				    </span>
				</div>
			</div>
		</div>
		<div class="row" style="margin-top:20px;">
			<div class="col-12 col-lg-4">
				<div class="label label-success  text-white dKnob" style='cursor: default;'>Logbook Columns</div>
				<div class=" dKnob" id="lFields" style="cursor: pointer; margin-top:5px;">
				</div>
			</div>
			<div class="col-12 col-lg-4">
				<div class="label label-success text-white dKnob" style='cursor: default;' >Master List</div>
				<div class=" dKnob" id="mFields" style="cursor: pointer; margin-top:5px;">
				</div>
			</div>
			<div class="col-12 col-lg-4">
				<div class="label label-success  text-white dKnob" style='cursor: default;'>Editor Fields</div>
				<div class=" dKnob" id="oFields" style="cursor: pointer; margin-top:5px;">
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon" style='cursor: default;'>Label</span>
					</div>
					<input type="text" onblur="setStyleFields('Label')" class="form-control" id="Label" aria-lable="label" aria-describedby="label-addon">
				</div>
			</div>
			<div class="col-sm-4 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Default</span>
					</div>
					<input type="text" onblur="setStyleFields('DefaultValue')" class="form-control" id="DefaultValue" aria-lable="default" aria-describedby="default-addon">
				</div>
			</div>
			<div class="col-sm-4" style="margin-top:15px;">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Attr</span>
					</div>
					<input type="text"  onblur="setStyleFields('Attribute')" readonly="readonly" class="form-control" id="Attribute" title="Attribute" aria-lable="attr" aria-describedby="attr-addon">
				    <span class="input-group-btn">
			            <div class="dropdown">
						    <button class="btn btn-primary dropdown-toggle" id="idSel" data-size="1" type="button" title="Attribute List" data-toggle="dropdown"><i class="fas fa-list-alt fa-lg"></i></button>
							<div class="dropdown-menu dropdown-menu-right" id='attrList' aria-labelledby="attrSelectButton">
								<div class='myAttr' id='noAttr'><li><a class='dropdown-item' href='#'>NONE</a></li></div>
								<div class='myAttr' id='setTime'><li><a class='dropdown-item' href='#'>Set Time</a></li></div>
								<div class='myAttr' id='dropdownList'><li><a class='dropdown-item' href='#'>Dropdown List</a></li></div>
								<div class='myAttr' id='noEdit'><li><a class='dropdown-item' href='#'>No Edit</a></li></div>
								<div class='myAttr' id='caps'><li><a class='dropdown-item' href='#'>Caps</a></li></div>
								<div class='myAttr' id='addPeriods'><li><a class='dropdown-item' href='#'>Add Periods</a></li></div>
							</div>
			            </div>
				    </span>
				</div>
			</div>
		</div>
		<div class="row" style="margin-top:2px;">
			<div class="col-sm-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">List</span>
					</div>
					<input type="text"  onblur="setStyleFields('ListContents')" class="form-control" title="Mode List Contents" id="ListContents" aria-lable="list" aria-describedby="list-addon">
				</div>
			</div>
			<div class="col-sm-6 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Prompt</span>
					</div>
					<input type="text"  onblur="setStyleFields('Prompt')" class="form-control" title="Prompt" id="Prompt" aria-lable="prompt" aria-describedby="prompt-addon">
				</div>
			</div>
		</div>
		<div class="row" style="margin-top:2px;">
			<div class="col-sm-12 text-spacer">
				<div class="input-group">
					<div class="input-group-prepend">
						<span class="input-group-text radio-group-addon">Note</span>
					</div>
					<input type="text"  onblur="setStyleFields('Notes')" class="form-control" title="Field Note" id="Notes" aria-lable="note" aria-describedby="note-addon">
				</div>
			</div>
		</div>
    <?php require $dRoot . "/includes/footer.php"; ?>
    <?php require $dRoot . "/includes/modal.txt"; ?>
<?php require $dRoot . "/includes/modalCancelOnly.txt"; ?>
<script src="./Bootstrap/popper.min.js"</script>
<link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
<script src="./Bootstrap/jquery-ui.js"></script>
<script src="./Bootstrap/bootstrap.min.js"></script>
</body>
</html>
