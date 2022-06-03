<?php>
<?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <div id="calendar" style="max-height: 80vh"></div>

    <link href="/fullcalendar/main.css" rel="stylesheet" />
    <script src="/fullcalendar/main.js"></script>
    <script src="https://github.com/mozilla-comm/ical.js/releases/download/v1.5.0/ical.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.5.1/locales-all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/icalendar@5.10.1/main.global.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script src="/Bootstrap/jquery.min.js" ></script>
    

    <script>
      document.addEventListener("DOMContentLoaded", function () {
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
          allDayDefault: true,
          displayEventTime: false,
          events: {
            url: "http://rigpi3.local/scheduler/basic.ics",
            // url: "https://www.gov.uk/bank-holidays/england-and-wales.ics",
            format: "ics",
            success: function(doc) {
              var start=doc.start;
            }
            
            
          },
          eventChange: function (info) {
            var st = info.event.start;
            var start = moment(st).format();
            var end;
            //            alert("change");
            //            if (info.event.end.length > 0) {
            var en = info.event.end;
            //            alert("end: " + en);
            //            if (info.event.end === null) {
            //              end = start;
            //              alert("end2: " + end);
            //            } else {
            if (en != null) {
              end = moment(en).format();
            } else {
              end = "";
            }
            var desc= info.event.extendedProps.description;
            alert(desc);
            var title = info.event.title;
            var id = info.event.id;
            var aDay = info.event.allDay;
            if (aDay) {
              aDay = 1;
            } else {
              aDay = 0;
            }
            //            if (info.event.allDay == true) {
            //            info.event.setDates(st, en, (allDay = info.event.allDay));
            //              end = null;
            //            }
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
          },
          eventReceive: function (info) {
            var st = info.event.start;
            start = moment(st).format();
            var end;
            if (info.event.end === null) {
              end = ""; //start;
              //              alert("receive");
            } else {
              var en = info.event.end;
              end = moment(en).format();
            }
            var title = info.event.title;
            var aDay = info.event.allDay;
            $.ajax({
              url: "/scheduler/insert.php",
              type: "POST",
              data: {
                id: 61,
                title: title,
                start_event: start,
                end_event: end,
                allDay: aDay,
              },
              success: function () {},
            });

            //            alert(title + " " + start + " " + end);
          },
        });

        calendar.render();
      });
    </script>
  </head>
  <body>
    <div id="calendar"></div>
  </body>
<script src="/js/mscorlib.js" type="text/javascript"></script> 
    <script src="/js/PerfectWidgets.js" type="text/javascript"></script>
 <script src="/js/jquery.ui.touch-punch.min.js"></script>   
<script src="./Bootstrap/popper.min.js"</script>
  <link rel="stylesheet" href="./Bootstrap/jquery-ui.css">
  <script src="./Bootstrap/jquery-ui.js"></script>
  <script src="./Bootstrap/bootstrap.min.js"></script>
    <script src="/js/nav-active.js"></script>
</html>
