<?php
/*
 * RigPi Calendar window
 *
 * Copyright (c) 2025 Howard Nurse, W6HN
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 *
*/
session_start();
$tUserName=$_SESSION['myUsername'];
$tCall=$_SESSION['myCall'];
$dRoot = "/var/www/html";
require_once $dRoot . "/classes/Membership.php";
$membership = new Membership();
$membership->confirm_Member($tUserName);
require $dRoot . "/programs/sqldata.php";
require_once $dRoot . "/classes/MysqliDb.php";
$db = new MysqliDb(
  "localhost",
  $sql_radio_username,
  $sql_radio_password,
  $sql_radio_database
);
$db->where("Username", $tUserName);
$row = $db->getOne("Users");
$tRadio = $row["SelectedRadio"];
?>
<!DOCTYPE html>
<html>
  <head>
  <title>RigPi Calendar</title>
<meta charset="utf-8" />
<link href="/scheduler/main.css" rel="stylesheet" />
<script src="/scheduler/main.js"></script>
         
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/favicon.ico">
    <link rel="stylesheet" href="./Bootstrap/bootstrap.min.css">
    
    <script src="/Bootstrap/jquery.min.js" ></script>
    <script defer src="./awe/js/all.js" ></script>
    <link href="/awe/css/all.css" rel="stylesheet">
     <link href="/awe/css/solid.css" rel="stylesheet">	
    <link href="/scheduler/calendar.css" rel="stylesheet">	
       <script src="/scheduler/ical.js"></script>
     <script src="/scheduler/m.js"></script>
      <script src="/scheduler/moment.min.js"></script>
<?php require $dRoot . "/includes/styles.php"; ?>

    <script>
    var tMyRadio='1';
    var tUserName="<?php $tUserName; ?>";
    var tAddedEvents0="/scheduler/custom.ics";
    var tAddedEvents1="/scheduler/Events_in_History.ics";
    var tAddedEvents2="/scheduler/digital.ics";
    var disconnected=1;
//    var tAddedEvents3="/scheduler/mixed.ics";
    var tAddedEvents4="/scheduler/phone.ics";
    var tAddedEvents5="/scheduler/us_hol.ics";
    var tAddedEvents6="/scheduler/cw.ics";
    var tUH=1;
    var tCU=2;
    var tDC=4;
    var tMC=8;
    var tPC=16;
    var tCC=32;
    var tUHo=64;
    var tMyCall=<?php echo "'" . $tCall . "'"; ?>;
    var tCall=tMyCall, disconnected=0;
    var tCalendarFilter=0;
    var calendar;
    var tMyRadio=<?php echo $tRadio; ?>;
    var customExists=0;
    document.addEventListener("DOMContentLoaded", function () {
    $(document).on('click', '#stop', function(){ 
      $.post("./programs/SetMyRotorBearing.php", {w: "stop", i: tMyRadio, a: "1"});
    });	
    function showConnectAlert(){
      $("#modalA-body").html("<br>The radio is not connected.<p><p>");			  				
      $("#modalA-title").html("Radio Connection");
        $("#myModalAlert").modal({show:true});
        setTimeout(function(){ 
          $("#myModalAlert").modal('hide');
       },
        2000);
      return;
    }

    $(document).on('click', '#rotate', function(){
       if (disconnected==1){
         showConnectAlert();
         return false;
       }

      var dx=$('#searchText').val();//.toUpperCase();
      $.post("./programs/GetCallbook.php", {call: dx, what: 'Bearing', user: tUser, un: tUserName},function(response){
        if(confirm('Rotate to '+dx+' at '+response+' degrees?') ){
          $.post("./programs/SetMyRotorBearing.php", {w: "turn", i: tMyRadio, a: response});
        }
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
      form.submit();
    };
    
    function logOut(){
      openWindowWithPost("/login.php", {
      status: "loggedout",
      username: tUserName});
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
          window.$("#myModalCancelOnly").modal({show:true});
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
    if (e.ctrlKey){
      var t=event.key;
      event.multiple
      var w=event.which;
      switch(w){
        case 88: //exit to login
        logOut();
      };
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
    
    $(document).on('click', '#ckBut', function() 
    {
      var tCkUH = document.getElementById('uhck');
      var tCkUHo = document.getElementById('uhock');
      var tCkCC = document.getElementById('ccck');
      var tCkPC = document.getElementById('pcck');
//      var tCkMC = document.getElementById('mcck');
      var tCkDC = document.getElementById('dcck');
      var tCkCU = document.getElementById('cuck');
      if ((tCalendarFilter & tUH) == tUH){
        tCkUH.checked=true; 
      }else{
        tCkUH.checked=false;
      }
      if ((tCalendarFilter & tUHo) == tUHo){
        tCkUHo.checked=true; 
      }else{
        tCkUHo.checked=false;
      }
      if ((tCalendarFilter & tCC) == tCC){
        tCkCC.checked=true; 
      }else{
        tCkCC.checked=false;
      }
      if ((tCalendarFilter & tPC) == tPC){
        tCkPC.checked=true; 
      }else{
        tCkPC.checked=false;
      }
//      if ((tCalendarFilter & tMC) == tMC){
//        tCkMC.checked=true; 
//      }else{
//        tCkMC.checked=false;
//      }
      if ((tCalendarFilter & tDC) == tDC){
        tCkDC.checked=true; 
      }else{
        tCkDC.checked=false;
      }
      if ((tCalendarFilter & tCU) == tCU){
        tCkCU.checked=true; 
      }else{
        tCkCU.checked=false;
      }
    });
    
    $.post('./programs/GetSetting.php',{radio: tMyRadio, field: 'DX', table: 'MySettings'}, function(response)
    {
      $('#searchText').val(response.toUpperCase());
    });
    
    
    $(document).on('click', '#searchButton', function() 
    {
      var tUserName="<?php echo $tUserName; ?>";

      var dx=$('#searchText').val().toUpperCase();
      if (dx.length==0 || ~dx.indexOf('*')){
        return;
      }
        $.post('/programs/GetUserField.php',{un: tUserName, field: 'uID'}, function(response) {
          tUser=response;
          $.post("./programs/GetCallbook.php", {call: dx, what: 'QRZData', user: tUser, un: tUserName},function(response){
            $(".modal-body").html(response);
            $.post("/programs/SetSettings.php", {field: "waitReset", radio: tMyRadio, data: 1, table: "RadioInterface"}, function (response1){

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
              $(".modal-pix").attr("src",'');
              }
              $('.modal-title').html(dx);
              $('#myModal').modal({show:true});
            });
          });
        });
        $.post("./programs/SetSettings.php", {field: "DX", radio: tMyRadio, data: dx, table: "MySettings"});
      })
    
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
    
   $('.modal').on('hide.bs.modal', function (e) {
       $.post("/programs/SetSettings.php", {field: "waitReset", radio: tMyRadio, data: 0, table: "RadioInterface"});
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
     /* initialize the external events
        -----------------------------------------------------------------
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
*/

    /* initialize the calendar
        -----------------------------------------------------------------*/
$.post('/programs/GetSetting.php',{radio: tMyRadio, field: 'CalendarFilter', table: 'MySettings'}, function(response){
        tCalendarFilter=response;

///....................calendar start
    var calendarEl = document.getElementById("calendar");
    calendar = new FullCalendar.Calendar(calendarEl, {
     headerToolbar: {
        left: "prev,next today",
        center: "title",
        right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
      },
      editable: false,
      droppable: false, // this allows things to be dropped onto the calendar
      timeZone: "local",
      slotDuration: '00:30:00',
      eventTimeFormat: {
        hour:'2-digit',
        minute:'2-digit',
        meridiem: false,
        hour12: false,
      },
      //eventSources
        eventClick: function(info) {
          if (event.url) {
            window.open(event.url, "_blank");
            return false;
          }else if(info.event.extendedProps.description.indexOf("http")>0){
            var tE=info.event.extendedProps.description;
            event.url=tE.substring(tE.indexOf(">http")+1,tE.indexOf("</a"));
            window.open(event.url, "_blank");
            return false;
          }
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
           if (info.event.end!=null){
                tE="End: "+moment(info.event.end).format();
              }else{
                tT=moment(info.event.start).add(1, 'hours').format();
                tE="End: " + tT;
               };
            };
            // };
            // alert(info.event.end);
            if (tD=='x'){
              if (info.event.extendedProps.type==1){
                tD="Start: "+moment(info.event.start).format("HH:mm")+ "<br>"+tE;
              }else{
               tD=info.event.extendedProps.firstname+"Start: "+moment(info.event.start).format("HH:mm")+ "<br>";
               if (!moment(info.event.end).format("HH:mm").includes('Invalid date')){
                 tD=tD + "End: "+moment(info.event.end).format("HH:mm");
               }
              }
            }
            var tI = tD.includes("Brought");
            if (tI){
              var tDi = tD.indexOf("Brought");
              if (tDi>0){
                tD=tD.substr(0,tDi);
              }
            }
              //alert(tD);
            $('[data-toggle="popover"]').popover('destroy');   
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
        },
        success: function () {
          calendar.removeAllEvents();
          calendar.refetchEvents();
        }
      });
    },
    });
    updateCalendar(calendar);
    calendar.render();
});


$.post('/programs/checkFile.php', function(response)
{
  customExists=response;
});
      
      $('#ra').click(function() {
          //	calendar.removeAllEvents();
        $.post('/scheduler/deleteAll.php');
        calendar.removeAllEvents();
      });
      $('#uh').click(function() {
       if ($(this).find('input').is(':checked')) {
         tCalendarFilter=tCalendarFilter | tUH;
       } else {
         tCalendarFilter=tCalendarFilter ^ tUH;
//         location.reload();
       };
       calendar.removeAllEventSources();
       updateCalendar(calendar);
      });
      $('#uho').click(function() {
             if ($(this).find('input').is(':checked')) {
               tCalendarFilter=tCalendarFilter | tUHo;
             } else {
               tCalendarFilter=tCalendarFilter ^ tUHo;
      //         location.reload();
      //         tAddedEvents1="";
             };
             updateCalendar(calendar);
           });
      $('#cu').click(function() {
        if (customExists==1)
        {
          if ($(this).find('input').is(':checked')) {
             tCalendarFilter=tCalendarFilter | tCU;
           } else {
             tCalendarFilter=tCalendarFilter ^ tCU;
           }
           calendar.removeAllEventSources();
           updateCalendar(calendar);
        }else{
           tCalendarFilter=tCalendarFilter ^ tCU;
          alert("Error: custom.ics not found in /scheduler");
        }
      });
      $('#dc').click(function() {
        if ($(this).find('input').is(':checked')) {
           tCalendarFilter=tCalendarFilter | tDC;
         } else {
           tCalendarFilter=tCalendarFilter ^ tDC;
//           location.reload();
         }
         calendar.removeAllEventSources();
         updateCalendar(calendar);
      });
      $('#mc').click(function() {
        if ($(this).find('input').is(':checked')) {
           tCalendarFilter=tCalendarFilter | tMC;           
         } else {
           tCalendarFilter=tCalendarFilter ^ tMC;
//           location.reload();
         }
         calendar.removeAllEventSources();
         updateCalendar(calendar);
      });
      $('#pc').click(function() {
        if ($(this).find('input').is(':checked')) {
           tCalendarFilter=tCalendarFilter | tPC;
         } else {
           tCalendarFilter=tCalendarFilter ^ tPC;
//           location.reload();
         }
         calendar.removeAllEventSources();
         updateCalendar(calendar);
      });
      $('#cc').click(function() {
        if ($(this).find('input').is(':checked')) {
           tCalendarFilter=tCalendarFilter | tCC;
         } else {
           tCalendarFilter=tCalendarFilter ^ tCC;
//           location.reload();
         }
         calendar.removeAllEventSources();
         updateCalendar(calendar);
      });
      
      function updateCalendar(calendar){
        calendar.removeAllEvents();
        calendar.removeAllEventSources();
        calendar.addEventSource({
          url: '/scheduler/load.php',
          format: 'json',
        });
        if ((tCalendarFilter & tCU) == tCU){
          if (customExists==1)
            {
            calendar.addEventSource({
              url: tAddedEvents0,
              format: 'ics',
              color: 'red',
              stick: true,
            });
          }else{
           // alert("custom.ics not found in /scheduler");
          };
        };
        if ((tCalendarFilter & tUH) == tUH){
          calendar.addEventSource({
              url: tAddedEvents1,
              format: 'ics',
              color: 'red',
              stick: true,
            });
        }else{
         // tR.remove();
        };
        if ((tCalendarFilter & tUHo) == tUHo){
        calendar.addEventSource({
            url: tAddedEvents5,
            format: 'ics',
            color: 'red',
            stick: true,
          });
        };
        if ((tCalendarFilter & tDC) == tDC){
          calendar.addEventSource({
            url: tAddedEvents2,
            format: 'ics',
            color: 'violet',
            stick: true,
          });
        };
/*        if ((tCalendarFilter & tMC) == tMC){
          calendar.addEventSource({
            url: tAddedEvents3,
            format: 'ics',
            color: 'gray',
            stick: true,
          });
        };
*/        if ((tCalendarFilter & tPC) == tPC){
          calendar.addEventSource({
            url: tAddedEvents4,
            format: 'ics',
            color: 'orange',
            stick: true,
          });
        };
        if ((tCalendarFilter & tCC) == tCC){
          calendar.addEventSource({
            url: tAddedEvents6,
            format: 'ics',
            color: 'green',
            stick: true,
          });
         };
          $.post("/programs/SetSettings.php", {field: "CalendarFilter", radio: tMyRadio, data: tCalendarFilter, table: "MySettings"});
         // location.reload();
     };
     $("#calendar_main").addClass('active');  //fixes issue only with calendar button

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
      };
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
              disconnected=1;
            }else{
              disconnected=0;
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
               
     </script>
  </head>
  <body style="width:99%">
   <?php require $dRoot . "/includes/header.php"; ?>
<div class="row">
<div class="col-1 col-lg-2 btn-padding"></div>
<div class="col-10 col-lg-8 btn-padding">
  <span class="label text-black" style="cursor: default; margin-top:10px;" id="topCaption">RigPi Calendar</span>
</div>
<div class="col-1 col-lg-2 btn-padding">
  <div class="dropdown">
      <button class='btn btn-color dropdown-ham hButton' id='ckBut' type='button' style="margin-top:10px;" title='Calendar Actions' data-toggle="dropdown">
        <i class='fas fa-bars fa-fw fa-lg' id='ckBut' ></i>
      </button>
        <ul class="dropdown-menu dropdown-menu-right menu-scroll" id="fnList">
        <div class='list-group'>
          <li role="presentation" style="cursor: default;" class="dropdown-header">Calendar Source</li>
          <label class='list-group-item' id="dc" style="border: 0px; height: 10px; margin-left: 10px" >
             <input class="form-check-input me-1"  id="dcck" type="checkbox" value="" aria-label="digital contests">
               Digital Contests
          </label>          
           <label class='list-group-item'  id="pc" style="border: 0px; height: 10px; margin-left: 10px" >
              <input class="form-check-input me-1" id="pcck" type="checkbox" value="" aria-label="phone contests">
                Phone Contests
           </label>
           <label class='list-group-item'  id="cc" style="border: 0px; height: 10px; margin-left: 10px" >
               <input class="form-check-input me-1" id="ccck" type="checkbox" value="" aria-label="cw contests">
                 CW Contests
            </label>
           <label class='list-group-item' id="uh" style="border: 0px; height: 10px; margin-left: 10px" >
              <input class="form-check-input me-1" id="uhck"  type="checkbox" value="" aria-label="us history">
                US History
           </label>
           <label class='list-group-item' id="uho" style="border: 0px; height: 10px; margin-left: 10px" >
              <input class="form-check-input me-1" id="uhock"  type="checkbox" value="" aria-label="us holidays">
                US Holidays
           </label>
           <label class='list-group-item' id="cu" style="border: 0px; height: 10px; margin-left: 10px" >
               <input class="form-check-input sme-1" id="cuck" type="checkbox" value="" aria-label="custom">
                 Custom
            </label>
          
        </div>
        </ul>
 </div>
 </div>
</div>
<hr style="width:95%;height:2px;border-width:1px;color:gray;background-color:gray">
<br />
<div class="row">
  <div class="col-12">
        <div id="calendar"></div>
  </div>
</div>
  <div class="status">
    <?php require $dRoot . "/includes/footer.php"; ?>
    <script>
 $("#modalA-title").html("No Internet");
     
    </script>
  </div>
  </body>
 <?php require $dRoot . "/includes/modal.txt"; ?>
 <?php require $dRoot . "/includes/modalCancelOnly.txt"; ?>
 <?php require($dRoot . "/includes/modalAlert.txt"); ?>

  <script src="/js/mscorlib.js" type="text/javascript"></script> 
 <script src="/js/PerfectWidgets.js" type="text/javascript"></script>
 <script src="/Bootstrap/jquery-ui.js"></script>
 <script src="/js/jquery.ui.touch-punch.min.js"></script>   
 <script src="./Bootstrap/popper.min.js"</script>
 <link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
 <script src="./Bootstrap/jquery-ui.js"></script>
 <script src="./Bootstrap/bootstrap.min.js"></script>
 <script src="/js/nav-active.js"></script>
</html>
