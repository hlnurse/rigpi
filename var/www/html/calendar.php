<?php
//calendar.php
if (!isset($GLOBALS["htmlPath"])) {
  $GLOBALS["htmlPath"] = $_SERVER["DOCUMENT_ROOT"];
}
$dRoot = $GLOBALS["htmlPath"];
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
<html>
  <head>
<meta charset="utf-8" />
<link href="/scheduler/main.css" rel="stylesheet" />
<script src="/scheduler/main.js"></script>
    <?php require $dRoot . "/includes/styles.php"; ?>
         
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/favicon.ico">
    <link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">
    
    <script src="/Bootstrap/jquery.min.js" ></script>
    <script defer src="./awe/js/all.js" ></script>
    <link href="./awe/css/all.css" rel="stylesheet">
    <link href="./awe/css/all.css" rel="stylesheet">
    <link href="./awe/css/solid.css" rel="stylesheet">	
    <link href="./scheduler/calendar.css" rel="stylesheet">	
       <script src="/scheduler/ical.js"></script>
     <script src="/scheduler/m.js"></script>
      <script src="/scheduler/moment.min.js"></script>
    <script>
    var tMyRadio='1';
    var tUserName=<?php echo "'" . $tUserName . "'"; ?>;
    var tAddedEvents1=""
    var tAddedEvents2=""
    var tAddedEvents3=""
    var tAddedEvents4=""
    var tAddedEvents5=""
    var tAddedEvents6=""
    tMyCall="W6HN";
    var tCall=tMyCall;
          document.addEventListener("DOMContentLoaded", function () {
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
        $.post("/programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: tDX, table: "MySettings"});
        return false;
      } else  {
        return true;
      }
    });
     /* initialize the external events
        -----------------------------------------------------------------*/
    $.post('/programs/getUsersText.php', function(response){
        var users=[];
        users=JSON.parse(response);
        var uList1 = "";
        var tCount=users.length;
        for (i = 0; i < tCount; i++) {
          var u = users[i];
          uList1=uList1+
        "<div class='fc-event fc-h-event  '>" +
        "<div class='fc-event-main'>" +
        u["RadioName"] + " " +
        u["MyCall"] +
        " " +
        u["Username"] +
        "</div></div>";
    }
    $("#external-events-list").html(uList1);
    });
    var containerEl = document.getElementById("external-events-list");
    new FullCalendar.Draggable(containerEl, {
      itemSelector: ".fc-event",
      eventData: function (eventEl) {
        return {
          title: eventEl.innerText.trim(),
        };
      },
    });

    /* initialize the calendar
        -----------------------------------------------------------------*/

    var calendarEl = document.getElementById("calendar");
    var calendar = new FullCalendar.Calendar(calendarEl, {
      headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
      },
      editable: true,
      droppable: true, // this allows things to be dropped onto the calendar
      timeZone: "local",
      slotDuration: '00:30:00',
      eventTimeFormat: {
        hour:'2-digit',
        minute:'2-digit',
        meridiem: false,
        hour12: false,
      },
      //eventSources
      events: 
        {
          url: "/scheduler/load.php",
          format: "json",
          },
        drop: function (arg) {
        },
        eventDragStop: function(info, jsEvent){
          var cl=document.getElementById("calendar").getBoundingClientRect().left;
          if(info.jsEvent.clientX<cl){
            $.ajax({
              url: "/scheduler/delete.php",
              type: "POST",
              data: {
                id: info.event.id,
              },
              success: function () {},
            });
            info.event.remove();
          };
        },
        eventChange: function (info) {
          var start = info.event.start;
          start = moment(start).format();
          var end;
          var end = moment(info.event.end).format();
          var title = info.event.title;
          var id = info.event.id;
          var aDay = info.event.allDay;
          if (aDay) {
            aDay = 1;
          } else {
            aDay = 0;
          }
          $.ajax({
            url: "/scheduler/update.php",
            type: "POST",
            data: {
              id: id,
              title: title,
              start_event: start,
              end_event: end,
              allDay: aDay,
            },
            success: function () {},
          });
          calendar.removeAllEvents();
          calendar.addEventSource({
            url: "/scheduler/load.php",
            format: "json",
          });
        },
        eventDidMount: function(info) {
        if (info.event.extendedProps.description){
        var tN=info.event.extendedProps.firstname;
        var tD = info.event.extendedProps.description;
        var tE='';
        if (info.event.allDay==true){
        tE = "Reserved all day";
        }else{
         if (info.event.end != 0){
           tE="End: "+moment(info.event.end).format("HH:mm");
          };
        };
        // };
        // alert(info.event.end);
        if (tD=='x'){
        tD=info.event.extendedProps.firstname+", "+info.event.extendedProps.callsign+"<br>Username: " +info.event.extendedProps.username+ "<br>Start: "+moment(info.event.start).format("HH:mm")+ "<br>"+tE;
        }
        var tI = tD.includes("Brought");
        if (tI){
          var tDi = tD.indexOf("Brought");
          if (tDi>0){
            tD=tD.substr(0,tDi);
          }
        }
          //alert(tD);
        $(info.el).popover({
            html: true,
            placement: 'top',
            trigger: 'hover',
            content: info.event.title + "<br>"+tD,
            container: 'body'
        });
      };
      },
      eventReceive: function (info) {
      var st = info.event.start;
      start = moment(st).format();
      var end;
      if (info.event.end === null) {
        end = 0; //start;
      } else {
        var en = info.event.end;
        end = moment(en).format();
      }
      var title = info.event.title;
      var aDay = info.event.allDay;
      var tName= info.event.extendedProps.FirstName;
      if (aDay) {
        aDay = 1;
      } else {
        aDay = 0;
      }
      var t = title.split(" ");
      var tRadio=t[0];
      var tCall=t[1];
      var tUser=t[2];
      $.ajax({
        url: "/scheduler/insert.php",
        type: "POST",
        data: {
          title: title,
          start_event: start,
          end_event: end,
          allDay: aDay,
          RadioName: tRadio,
          FirstName: tName,
          username: tUser,
          callsign: tCall,
          description: "",
        }
      })
            calendar.removeAllEvents();
            calendar.addEventSource({
              url: "/scheduler/load.php",
              format: "json",
            });
        if (tAddedEvents1.length>0){
            calendar.addEventSource({
                url: tAddedEvents1,
                format: 'ics',
                color: 'red',
                stick: true,
              });
            };
            if (tAddedEvents2.length>0){
              calendar.addEventSource({
                url: tAddedEvents2,
                format: 'ics',
                color: 'violet',
                stick: true,
              });
            };
            if (tAddedEvents3.length>0){
              calendar.addEventSource({
                url: tAddedEvents3,
                format: 'ics',
                color: 'gray',
                stick: true,
              });
            };
            if (tAddedEvents4.length>0){
              calendar.addEventSource({
                url: tAddedEvents4,
                format: 'ics',
                color: 'orange',
                stick: true,
              });
            };
            if (tAddedEvents6.length>0){
              calendar.addEventSource({
                url: tAddedEvents6,
                format: 'ics',
                color: 'green',
                stick: true,
              });
             };
           },
      });
      calendar.render();
      
      $('#ra').click(function() {
          //	calendar.removeAllEvents();
            $.post('/scheduler/deleteAll.php');
            calendar.removeAllEvents();
          });
          $('#uh').click(function() {
          //	calendar.removeAllEvents();
            calendar.addEventSource({
              url: "http://rigpi3.local/scheduler/Events_in_History.ics",
              format: 'ics',
              color: 'red',
              stick: true,
            });
            tAddedEvents1="http://rigpi3.local/scheduler/Events_in_History.ics";
          });
          $('#dc').click(function() {
            calendar.addEventSource({
              url: 'http://rigpi3.local/scheduler/digital.ics',
              format: 'ics',
              color: 'violet',
              stick: true,
            });
            tAddedEvents2="http://rigpi3.local/scheduler/digital.ics";
          });
          $('#mc').click(function() {
            calendar.addEventSource({
              url: 'http://rigpi3.local/scheduler/mixed.ics',
              format: 'ics',
              color: 'gray',
              stick: true,
            });
            tAddedEvents3="http://rigpi3.local/scheduler/mixed.ics";
          });
          $('#pc').click(function() {
            calendar.addEventSource({
              url: 'http://rigpi3.local/scheduler/phone.ics',
              format: 'ics',
              color: 'orange',
              stick: true,
            });
            tAddedEvents4="http://rigpi3.local/scheduler/phone.ics";
          });
          $('#cc').click(function() {
            calendar.addEventSource({
              url: 'http://rigpi3.local/scheduler/cw.ics',
              format: 'ics',
              color: 'green',
              stick: true,
            });
            tAddedEvents6="http://rigpi3.local/scheduler/cw.ics";
          });
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
  <?php require $dRoot . "/includes/header.php"; ?>
  <div class="container-fluid">
<div class="row">
<div class="col-1 col-lg-2 btn-padding">
</div>
<div class="col-10 col-lg-8 btn-padding">
  <span class="label text-black" style="cursor: default; margin-top:10px;" id="topCaption">RigPi Scheduler</span>
</div>
<div class="col-1 col-lg-2 btn-padding">
  <div class="dropdown">
      <button class='btn btn-color dropdown-ham hButton' type='button' style="margin-top:10px;" title='Calendar Actions' data-toggle="dropdown">
        <i class='fas fa-bars fa-fw fa-lg'></i>
      </button>
        <ul class="dropdown-menu dropdown-menu-right menu-scroll" id="fnList">
        <div class='ex'>
          <li role="presentation" class="dropdown-header">Calendar Source</li>
          <li><a class='dropdown-item calsel' id='dc' href='#'>Digital Contests</a></li>
          <li><a class='dropdown-item calsel' id='cc' href='#'>CW Contests</a></li>
          <li><a class='dropdown-item calsel' id='pc' href='#'>Phone Contests</a></li>
          <li><a class='dropdown-item calsel' id='mc' href='#'>Mixed Contests</a></li>
          <li><a class='dropdown-item calsel' id='uh' href='#'>US History</a></li>
          <li><a class='dropdown-item calsel' id='ra' href='#'>Remove all reservations</a></li>
          
        </div>
        </ul>
  </div>
  </div>
</div>
</div><hr style="width:95%;height:2px;border-width:1px0;color:gray;background-color:gray">
<br />
<div class="row">
  <div class="col-3">
      <div id="external-events">
        <h3>Users</h3>
        <div id="external-events-list">
        </div>
        <p>
        </p>
      </div>
  </div>
  <div class="col-9"?
      <div id="calendar-wrap">
        <div id="calendar"></div>
      </div>
  </div>
  <div class="status">
    <?php require $dRoot . "/includes/footer.php"; ?>
  </div>
  </body>
  <script src="./Bootstrap/popper.min.js"</script>
  <script src="Bootstrap/jquery-ui.js"></script>
  <script src="Bootstrap/bootstrap.min.js"></script>
  <script src="/js/nav-active.js"></script>
</html>
