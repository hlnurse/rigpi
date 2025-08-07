<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$dRoot = "/var/www/html";
require_once($dRoot . "/programs/GetUserFieldFunc.php");
$_SESSION['myCall']=getUserField($_SESSION['myUsername'],'MyCall');
if (isset($_SESSION['myUsername']) && strlen($_SESSION['myUsername']>0)){
    $username=$_SESSION['myUsername'];
    echo $_SESSION['myCall'];
}else{
  if (!isset($_SESSION['myPort'])){
    header('Location: /loginLite.php'); // Redirect to a dashboard or protected page
    exit();
}
  if (!isset($_SESSION['ip'])){
    header('Location: /loginLite.php'); // Redirect to a dashboard or protected page
    exit();
}
  if (isset($_SESSION['myCall'])){
    if ($_SESSION['myCall']=="NG"){
      header('Location: /loginLite.php'); // Redirect to a dashboard or protected page
      exit();
    }
  }
  if (!isset($_SESSION['myRadioName'])){
    header('Location: /loginLite.php'); // Redirect to a dashboard or protected page
    exit();
  }
  if (!isset($_SESSION['myInstance'])){
    header('Location: /loginLite.php'); // Redirect to a dashboard or protected page
    exit();
  }
}
  $tMyPort=$_SESSION['myPort'];
  $tMyRadio=$_SESSION['myRadio'];

//$username="z";
//$tMyRadio=1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>RigPi Lite</title>
  <!-- Bootstrap CSS -->
  <script src="/Bootstrap/jquery.min.js"></script>
  <link rel="stylesheet" href="/Bootstrap/bootstrap.min.css">
  <!-- Bootstrap JS and dependencies -->
  <script src="/Bootstrap/popper.min.js"</script>
 <script src="/Bootstrap/bootstrap.min.js"></script>
 <style>
   body {
      background-color: #444;
   }
   .btn {
     width:120px;
   }
   canvas {
     border: 2px solid black;
     background-color: orange;
     box-shadow:  0px 0px 10px 5px rgba(250, 250, 250, 0.5);
     border-radius: 15px; 
     display: block;
     margin: 20px auto; /* Centers the canvas horizontally */
   }
   .content {
     margin-top: 20px; /* Adjusts spacing between canvas and buttons/text */
   }
   .text-below {
     width: 300px;
     color:lightgray;
     text-align: center; /* Centers the text horizontally */
     margin: 0 auto; /* Aligns with the canvas */
   }
   .text-above {
    text-align: center; /* Centers the text */
    color:white;
    font-size: 32px;
    margin-top: 20px; /* Adds space above the canvas */
   }
   #lan-info, #wan-info {
     width: 300px;
     color:white;
     text-align: center; /* Centers the text horizontally */
     margin: 0 auto; /* Aligns with the canvas */
     font-size: 16px;
     margin-top: 10px; /* Adds space above the canvas */
   }
 .prevent-select {
     -webkit-user-select: none; /* Safari */
     -ms-user-select: none; /* IE 10 and IE 11 */
     user-select: none; /* Standard syntax */
     cursor:default;
   }
 .hidden-submit {
     border: 0 none;
     height: 0;
     width: 0;
     padding: 0;
     margin: 0;
     overflow: hidden;
 }
 </style>
 <script>
   var tMyPort='0';
   let tMyIP=0;
   var tMyRadio=<?php echo($tMyRadio);?>;
  var tUsername="<?php echo($_SESSION['myUsername']);?>";
     $(document).ready(function(){
      sessionStorage.setItem('connected','0');
      
      window.addEventListener("beforeunload", function () {
           sessionStorage.setItem("isReloading", "true");
       });
       
       // Detect window close (not a refresh)
       window.addEventListener("unload", function () {
           if (!sessionStorage.getItem("isReloading")) {
               navigator.sendBeacon("/programs/logout.php"); // Only logout if not a refresh
               exit();
           }
           sessionStorage.removeItem("isReloading"); // Reset the flag
       });
           
            sessionStorage.setItem('freq',0);
            const canvas = document.getElementById('myCanvas');
           ctx = canvas.getContext('2d');
           waiting(400);
           $.post("/programs/GetInfo.php", {what: 'IPAdr'}, function(response){
             var info=response.split('+');
              // Combine JavaScript and PHP variables
             const phpPort = "<?php echo $_SESSION['myPort']; ?>";
             const rotPort=(2*tMyRadio)+4531;
             tMyIP=info[0];
             const LANConnectionInfo = 'LAN: ' + info[0] + ":" + "<?php echo $_SESSION['port'];?>";
             const WANConnectionInfo = 'WAN: ' + info[2] + ":" + "<?php echo $_SESSION['port'];?>";
//             const rotPort=<?php echo $_SESSION['port'];?>+1;
             // Write combined info to the page
             document.getElementById("lan-info").textContent = LANConnectionInfo;
             document.getElementById("wan-info").textContent = WANConnectionInfo+'*';
            document.getElementById("wanPort").textContent = '*Forward port ' + "<?php echo $_SESSION['port'];?>" + ' (radio) and port ' + rotPort + ' (rotor) in your router for remote ops.'
;

            });
           
           var mode="USB";
           var bw="1200";
           $.getScript("/programs/drawPanel.php");
            
           function closeAlert() {
                 document.getElementById('alert-overlay').style.display = 'none';
             }
     
           tUpdate = setInterval(updateTimer,2000);
     
           function updateTimer()
           {
               if (sessionStorage.getItem('connected')=='1')
               {
                   doUpdate();    
                   $("#connectMe").text('Connected');
              }
           };

          $(document).keydown(function(e)
          {
              if (e.keyCode == 13) // 27=esc
              {
                connect(0);
             }
          });
            

        });
          var tRadioName="KWM-380";

         function waiting(offset){
            ctx.fillStyle = "orange";
            ctx.fillRect(offset, 0, 270 + offset, 150);
             ctx.font = "30px 'Helvetica'";
             var r_a = 0.3
             ctx.fillStyle = `rgba(20, 20, 20, ${r_a})`;
             ctx.fillText("Waiting...",20,60);
          }

         function connecting(offset){
            ctx.fillStyle = "orange";
            ctx.fillRect(offset, 0, 270 + offset, 150);
             ctx.font = "30px 'Helvetica'";
             var r_a = 0.3
             ctx.fillStyle = `rgba(20, 20, 20, ${r_a})`;
             ctx.fillText("Connecting...",20,60);
          }

         function disconnecting(offset){
            ctx.fillStyle = "orange";
            ctx.fillRect(offset, 0, 290 + offset, 150);
             ctx.font = "30px 'Helvetica'";
             var r_a = 0.3
             ctx.fillStyle = `rgba(20, 20, 20, ${r_a})`;
             ctx.fillText("Disconnecting...",20,60);
          }

        function connect() {
            connecting(0);
           sessionStorage.setItem('connected','1');
          sessionStorage.setItem('freq',0)
//                  $.post("/programs/disconnectRadio.php", function(response){
        tMyRadio=1;
          var tMyKeyer='CAT';
          var tCWPort="";
          var tMyTCPPort=0;
          var tMyRotorPort="/dev/ttyUSB1";
          var tMyKeyerPort=0;
          var tMyKeyerIP=0;
          var tMyKeyerFunction=0;
          var tUDPPort=0;
          $.post('/programs/GetSelectedRadio.php', {un:tUsername}, function(response) 
          {
            $.get('/programs/GetMyRadio.php', 'f=Port&r='+response, function(response1) {
              tMyRadio=response;
              tRadioPort=response1;
              $.post('/programs/h.php',{test: 0, keyer: tMyKeyer,radio:tMyRadio, user:tUsername, radioPort:tRadioPort, CWPort:tCWPort, tcpPort:tMyTCPPort, rotorPort: tMyRotorPort, keyerPort:tMyKeyerPort, keyerIP:tMyKeyerIP, keyerFunc:tMyKeyerFunction, UDPPort: tUDPPort, startUpDelay: 0, lite: 0},function(response){
              $("#connectMe").text('Connected');
               doUpdate();
          });
        });
      });
    };

     function doUpdate(){ //update panel
              if (sessionStorage.getItem('connected')=='1'){
                tMyPort="<?php echo $_SESSION['myPort']; ?>"; 
 //         tMyPort=4534;
 
// var tMyRadioPort='172.16.0.43:4532';
//tMyIP='127.0.0.1';
var tMyRadioPort=tMyIP + ":" + <?php echo $_SESSION['port'];?>;
                 $.post('/programs/rigctl_handler.php', {port:tMyRadioPort}, function (response) {
                     const result = JSON.parse(response);
                     if (result.status === 'success') {
                         sessionStorage.setItem('connected','1');
                         const aResult=result.data.split("|");
                         if (aResult[0].indexOf("rigctld")>-1){
                           var f=addPeriods(aResult[1]);
                           var m= aResult[2];
                           var b = aResult[3];
                           var t= aResult[4];
                         }else{
                           var f=addPeriods(aResult[0]);
                            var m= aResult[1];
                            var b = aResult[2];
                            var t= aResult[3];
                         }
                         if (m=='PKTUSB'){
                           m='USB-D';
                         }
                         var now = new Date();
                         var now_hours=now.getUTCHours();
                         now_hours=("00" + now_hours).slice(-2);
                         var now_minutes=now.getUTCMinutes();
                         now_minutes=("00" + now_minutes).slice(-2);
                           var timeUTC=now_hours+":"+now_minutes;
 
                         if(sessionStorage.getItem('connected')==1 && (timeUTC !== sessionStorage.getItem('time') || t !== sessionStorage.getItem('tx') || f !== sessionStorage.getItem('freq') || m !== sessionStorage.getItem('mode') || b !== sessionStorage.getItem('bw'))){
                           console.log("UPDATING");
                             sessionStorage.setItem('freq',f);
                             sessionStorage.setItem('mode',m);
                             sessionStorage.setItem('bw',b);
                             sessionStorage.setItem('tx',t);
                             sessionStorage.setItem('time',timeUTC);
                             getImg(f,m,b, t, timeUTC);
                             $("#connectMe").removeClass('btn-danger');
                             $("#connectMe").addClass('btn-success');
                        }
                    }
                 })
               }
             }
 
      function addPeriods(nStr) {  //convert '14025000' to '14.025.000'
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

  </script>

</head>
<body class="prevent-select">
  <div class="hidden-submit"><input type="submit" tabindex="-1"/></div>
  <p class="text-above prevent-select">
   <a target="_blank" rel="noopener noreferrer" id="rigpi" href="https://www.rigpi.com" title="Go to https://www.rigpi.com">
           <img src="/Images/RigPiW.png" alt="RigPi" title="Go to https://www.rigpi.com" style="margin-top:-5px;width:30px;height:30px;"></img>
   </a>
   <?php echo $_SESSION['myCall'];?>&nbsp;RigPi Lite
 </p>
     <!-- Canvas -->
    <canvas class="prevent-select" id="myCanvas" width="300" height="150"></canvas>

    <!-- Buttons -->
    <div class="content d-flex flex-column justify-content-center align-items-center">
      <div class="mt-3">
      <button type="button" class="btn btn-snall btn-danger mr-2 rounded-pill " id="connectMe" onclick="connect()" >Connect</button>
      <button type="button" class="btn btn-small btn-info rounded-pill " onclick="confirmLogout()" id="stop">Disconnect</button>
    </div>
    </div>
 <p class="mt-3 text-center prevent-select text-below" id="radio">
  Radio is <?php echo $_SESSION['myRadioName']?> for Username <?php echo $_SESSION['myUsername']?>
  </p>
  <p class="mt-3 prevent-select text-center text-below">
   To connect to RigPi Lite, use 'Hamlib Net rigctl' in other programs plus the IP:port for this RigPi.<p>
    </p>
    <p class="mt-3 text-center text-below">
    <p id="lan-info"></p>
    <p id="wan-info"></p>
  <p class="mt-3 prevent-select text-center text-below" id='wanPort'>
    </p>


      </body>
</html>
<script>
  function confirmLogout() {
   if (confirm("Are you sure you want to log out?")) {
     sessionStorage.setItem("isReloading", "false");
     sessionStorage.setItem("connected", "0");
     disconnecting(0);
      $.post('/programs/disconnectRadio.php', {radio:<?php echo $_SESSION['myRadio'];?>, port:'<?php echo $_SESSION['myPort'];?>', user:'<?php echo $_SESSION['myUsername'];?>', rotor: 1, instance:<?php echo $_SESSION['myInstance'];?>}, function(response) {
        // Redirect to logout.php if confirmed
         window.location.href = "/programs/logoutLite.php";
        exit();
    });
  }
  };

 </script>

