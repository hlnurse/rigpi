<?php
if (!isset($GLOBALS["htmlPath"])) {
  $GLOBALS["htmlPath"] = $_SERVER["DOCUMENT_ROOT"];
}
$dRoot = $GLOBALS["htmlPath"];
include_once "/var/www/html/programs/getUsersText.php";
$users = getUsersText();
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
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en">
<head>
	<meta charset="utf-8">

	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo $tCall; ?> RigPi Radio Settings</title>
	<meta name="RigPi Settings" content="">
	<meta name="author" content="Howard Nurse, W6HN">

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />
	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">
	<script defer src="./awe/js/all.js" ></script>
	<link href="./awe/css/all.css" rel="stylesheet">
	<link href="./awe/css/fontawesome.css" rel="stylesheet">
	<link href="./awe/css/solid.css" rel="stylesheet">	
	<script src="/Bootstrap/jquery.min.js" ></script>
	<!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
	<link rel="shortcut icon" href="/favicon.ico">
	<link rel="apple-touch-icon" href="/apple-touch-icon.png">
	<?php require $dRoot . "/includes/styles.php"; ?>
	<script type="text/javascript">
		var cManu='';
		var cRadioID='';
		var tMyRadio='0';
		var tMyPort=4532;
		var tMyCall="<?php echo $tCall; ?>";
		var tCall=tMyCall;
		var tMyCWPort="/dev/ttyS0";  //later found from db
		var tMyRotorPort="/dev/ttyUSB1";  //later found from db
		var tMyKeyer="non";
		var tMyPTT=1;
		var tUserName=<?php echo "'" . $tUserName . "'"; ?>;
		var tUser='';
		var tMyRadioName='';
		var tMyKeyerFunction=0;
		var tMyKeyerPort;
		var tMyKeyerIP;
		var tCWPort;
	</script>
	<script src="/Bootstrap/jquery.min.js" ></script>
	<link href='/fullcalendar/main.css' rel='stylesheet' />
	<script src='/fullcalendar/main.js'></script>
	<script>
	document.addEventListener('DOMContentLoaded', function() {
		var users="<?php echo $users; ?>";
		users=users.split("~");
		var uList="";
		for (let i=0; i<(users.length-1);i++ ){
			uList=users[i].split("`");
			uList="<div class='fc-event fc-h-event fc-daygrid-event fc-daygrid-block-event'>"+
		  	"<div class='fc-event-main'>"+uList[2]+" "+uList[4]+"</div>";
		}
		$("#external-events-list").html(uList);
//		var uListEl=document.getElementByID('external-events-list');
		var containerEl = document.getElementById('external-events-list');
		new FullCalendar.Draggable(containerEl, {
  		itemSelector: '.fc-event',
  		eventData: function(eventEl) {
			return {
	  		title: eventEl.innerText.trim()
			}
  		}
		});
		
	
		
		var calendarEl = document.getElementById('calendar');
		
		var calendar = new FullCalendar.Calendar(calendarEl, {
		height: 'auto',
	  	headerToolbar: {
			left: 'prev,next today',
			center: 'title',
			right: 'dayGridMonth,timeGridWeek,timeGridDay'
	  	},
eventDidMount: function(info) {
	console.log(info.event.instanceId);
	var n = calendar.getEvents();
	// {description: "Lecture", department: "BioChemistry"}
  },
  		droppable: true, // this allows things to be dropped onto the calendar
	  	initialDate: '2022-01-01',
	  	navLinks: true, // can click day/week names to navigate views
	  	selectable: true,
	  	selectMirror: true,
	  	select: function(arg) {
			var title = prompt('Event Title:');
			if (title) {
		  	calendar.addEvent({
				title: title,
				start: arg.start,
				end: arg.end,
				allDay: arg.allDay
		  	})
			}
			calendar.unselect()
	  	},
	  	eventAdd: function(arg) {
//			if (confirm('Are you sure you want to delete this event?')) {
		  	alert(arg.event.title);
//			}
	  	},
	  	editable: true,
	  	dayMaxEvents: true, // allow "more" link when too many events
		});
	
		calendar.render();
  	});
$.post('/programs/GetUserField.php', {un:tUserName,field:'uID'}, function(response){
	  tMyRadio=response;
	tMyPort=tMyRadio*2+4530;
	$("#curID").val(response);
	$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
	{
		$('#searchText').val(response);
	});

	$.getScript("/js/modalLoad.js");


});
var tUpdate = setInterval(updateTimer,1000);
function updateTimer()
{
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
  <body>
<body class="body">
<?php require $dRoot . "/includes/header.php"; ?>
<div class="container-fluid">
<div class="row" style="margin-bottom:10px;" >
	<div class="col-12  col-lg-4 btn-padding">
	</div>
	<div class="col-6 col-lg-4 text-center">
		<span class="label-success text-black" style="cursor: default; margin-top:10px;">Scheduler (User: <?php echo $tUserName; ?>)</span>
	</div>
	<div class="col-6 col-lg-4 btn-padding">
	</div>
</div>
<hr>
<div class='row'>
<div class="col-12 col-lg-4">
	<div id='external-events'>
	  <h4>Users</h4>

	  <div id='external-events-list'>
	  </div>
	</div>
</div>
<div class="col-12  col-lg-8 btn-padding">

	<div id='calendar-wrap'>
	  <div id='calendar'></div>
	</div>
</div>
<div class="row">
</div>
  </div>
<?php require $dRoot . "/includes/footer.php"; ?>
  <script src="/js/mscorlib.js" type="text/javascript"></script> 
  <script src="/Bootstrap/jquery-ui.js"></script>
  <script src="./Bootstrap/popper.min.js"</script>
  <link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
  <script src="./Bootstrap/jquery-ui.js"></script>
  <script src="./Bootstrap/bootstrap.min.js"></script>
  <script src="/js/nav-active.js"></script>
</body>
</html>