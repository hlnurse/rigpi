/*$(document).on("click", "#searchButton", function () {
  var dx = $("#searchText").val().toUpperCase();
  if (dx.length == 0 || ~dx.indexOf("*") || ~dx.indexOf("=")) {
    return;
  }
  tUserName="<?php echo $_SESSION['myUsername'];?>"

  tMyRadio=1;
  $.post(
    "/programs/GetUserField.php",
    { un: tUserName, field: "uID" },
    function (response) {
      tUser = response;
      $.post("/programs/SetSettings.php", {
        field: "waitReset",
        radio: tMyRadio,
        data: 1,
        table: "RadioInterface",
      });

      $.post(
        "./programs/GetCallbook.php",
        { call: dx, what: "QRZData", user: tUser, un: tUserName },
        function (response) {
          $(".modal-body").html(response);
          $.post(
            "./programs/GetCallbook.php",
            { call: dx, what: "QRZpix", user: tUser, un: tUserName },
            function (response) {
              var aPix = response.split("|");
              var h = aPix[1];
              var w = aPix[2];
              if (h > 0) {
                var wP = aPix[2] / 280;
                var tW = w / wP;
                var tH = h / wP;
                $(".modal-pix").attr("height", tH + "px");
                $(".modal-pix").attr("width", tW + "px");
                $(".modal-pix").attr("src", aPix[0]);
              } else {
                $(".modal-pix").attr("height", "0px");
                $(".modal-pix").attr("width", "0px");
                $(".modal-pix").attr("src", "about:blank");
              }
              $(".modal-title").html(dx);
              $("#myModal").modal({ show: true });
            }
          );
        }
      );
      $.post("./programs/SetSettings.php", {
        field: "DX",
        radio: tMyRadio,
        data: dx,
        table: "MySettings",
      });
    }
  );
});
*/
$(document).on("click", "#stop", function () {
  $.post("./programs/SetMyRotorBearing.php", {
    w: "stop",
    i: tMyRadio,
    a: "1",
  });
});

$(document).on("click", "#modalClose", function () {
  $.post("/programs/SetSettings.php", {
    field: "waitReset",
    radio: tMyRadio,
    data: 0,
    table: "RadioInterface",
  });
  $("#myModal").modal("hide");
});

$(document).on("click", "#rotate", function () {
  if (tDisconnected==1){
    showConnectAlert();
    return false;
  }

  var dx = $("#searchText").val().toUpperCase();
  $.post(
    "./programs/GetCallbook.php",
    { call: dx, what: "Bearing", user: tUser, un: tUserName },
    function (response) {
      if (confirm("xxxRotate to " + dx + " at " + response + " degrees?")) {
        $.post(
          "/programs/SetSettings.php",
          {
            field: "waitReset",
            radio: tMyRadio,
            data: 1,
            table: "RadioInterface",
          },
          function (response1) {
            $.post("./programs/SetMyRotorBearing.php", {
              w: "turn",
              i: tMyRadio,
              a: response,
            });
          }
        );
      }
    }
  );
});

/*$(document).keydown(function(event) { 
  if (event.keyCode == 27) { 
        document.getElementById('modalClose').click();
//        document.getElementById('modalAlertClose').click();
  }
});
*/
//var tUpdate = setInterval(bearingTimer,500)

/*function bearingTimer()
{
	$.post("./programs/GetRotorIn.php", {rotor: tMyRadio},function(response){
		var tAData=response.split('+');
		var tAz=Math.round(tAData[0])+"&#176;";
		$(".angle").html(tAz);
	});	
	
}
*/
