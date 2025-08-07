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
  var tUserName=<?php echo $_SESSION['myUsername']; //<php echo "admin";?>';

/*  $.post('/programs/GetSelectedRadio.php',{un:tUserName}, function(response)
  {
    tMyRadio=response;
  })
*/ 
var tCall=<?php echo $_SESSION['myCall'];?>
var tMyRadio="<?php echo $_SESSION['myRadio'];?>";
  $.post(
    "/programs/GetInterfaceIn.php",
    { radio: tMyRadio, un: tUserName, myCall: tCall },
    function (response) {
      var tAData = response.split("`");
      var tBW=tAData[17];
       console.log("mode after in: " + tAData[3]);
       var tM = tAData[3].trim();
              let freqs = "14074000|7074000|21074000|1840000|3573000|5357000|10136000|18100000|24915000|28074000|50313000".split("|");
           if (freqs.includes(tF)) {
             tM='USB-D';
             tAData[3]=tM;
        }
       console.log("mode after convert: " + tAData[3]);


      if (tAData.indexOf("NG")<0) {
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
         if (tM == "PKTUSB") {
          tM = "USB-D";
        }
        if (tM == "PKTLSB") {
          tM = "LSB-D";
        }
        $("#fPanel3").text("Mode: " + tM + " - BW: "+tBW);
        $("#fPanel1").attr("style", "background-color:black");
      }
     }
  );
}
