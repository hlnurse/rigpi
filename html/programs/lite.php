<head>
	<meta charset="utf-8" />
	<title>Frequency from Hamlib</title>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" href="/Bootstrap/bootstrap.min.css">
	 <script src="/Bootstrap/jquery.min.js" ></script>
	<link rel="shortcut icon" href="./favicon.ico">
	<link rel="apple-touch-icon" href="./favicon.ico">
	<link href="/awe/css/all.css" rel="stylesheet">
	<link href="/awe/css/fontawesome.css" rel="stylesheet">
	<link href="/awe/css/solid.css" rel="stylesheet">	
	<link rel="stylesheet" href="/Bootstrap/jquery-ui.css">


	<?php
		$x="test";
		$output[1]="click";
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		$f="Connect Radio";
	?>

</head>
				
<body style="background: #444; text-align:center;">
<style>
	textarea {
		display: block;
		margin-left: auto;
		margin-right: auto;
		font:"40px 'Helvetica'", Times, serif;  
	}
</style>
<div class="container">
	<h2 class="text-center mt-4 mb-4"  style="color:white">RigPi Lite</h2>

<html>
<div class="row rows="3" mt-5 mb-5">
	<div class="col col-sm-4">&nbsp;</div>
		<div class="col col-sm-4">
				<textarea id="frequency" class="form-control form-control-sm mb-3" cols='60' rows='3' style='background-color: orange !important' <b><?php echo $f ?></textarea>
		</div>
</div> 
<div>
		<button class="btn btn-sm btn-info" id="start">Connect RigPi Lite</button>
		<button class="btn btn-sm btn-info" id="stop">Disconnect RigPi Lite</button>
</div>  	
	<script src="/js/mscorlib.js" type="text/javascript"></script> 
<script src="/js/PerfectWidgets.js" type="text/javascript"></script>
<script src="/Bootstrap/jquery-ui.js"></script>
<script src="/js/jquery.ui.touch-punch.min.js"></script>   
<script src="/Bootstrap/popper.min.js"</script>
<link rel="stylesheet" href="/Bootstrap/jquery-ui.css">
<script src="/Bootstrap/jquery-ui.js"></script>
<script src="/js/nav-active.js"></script>
</html>
</body>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// Execute the rigctl command
	$output = shell_exec('rigctl -m 2 -r 127.0.0.1:4532 f 2>&1');

	// Check if output exists and return it as JSON
	if ($output !== null) {
		echo json_encode(['status' => 'success', 'data' => trim($output)]);
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Command failed to execute']);
	}
} else {
	echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>

<script>
	if (typeof f !== 'undefined'){
		exit;
	}
/*	document.addEventListener('DOMContentLoaded', function() {	
	if (typeof $ !== "undefined") {
		console.log("jQuery loaded and ready.");
	} else {
		console.log("jQuery not available.");
	}
	console.log(document.querySelector('form'));
	document.querySelector('form').addEventListener('submit', function(event) {
		console.log("Form is about to be submitted.");
		console.log("done");
		return;
//		$.post('/programs/h.php',{},function(response){
//$.post("/programs/h.php", function(response){
//				console.log("response");
//			});
//
//			});
	});
})
*/
window.onerror = function(message, source, lineno, colno, error) {
	console.error("Error message: ", message);
	console.error("Source: ", source);
	console.error("Line: ", lineno, "Column: ", colno);
	console.error("Error object: ", error);
};
$(document).on('click', '#start', function() 
{
 	var f1=[];
	 f1[0]="Connect Radio Please";
	 f1[1]="Mode";
	 f1[2]="Bandwidth";
	f1=doFreq();
//	alert(<?php echo $output[1];?>);
//	alert("<?php echo $output[1];?>");
});
function doFreq(){
	$.post("/programs/h.php", function(response){
		console.log("post res: " + response);
		if (typeof response !== 'undefined'){
			$.post("rigctl -m 2 -r 127.0.0.1:4532 f", function(response){
				var f = response;
				f1=f;
				m="USB";
				$("#frequency").text("Frequency: " + f1 + "\n" + "Mode: " +m);

			})
		}
	});

}
</script>
