<head>
	<meta charset="utf-8" />
	<title>Frequency from Hamlib</title>
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
	<link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" />
	<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
	<?php
		$x="";
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(E_ALL);
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Pragma: no-cache");
		$ph="Click Connect RigPi Lite";
		$f="Connect Radio";
		function doFreq(){
/*				//exec("php /var/www/html/programs/disconnectRadio.php");
						sleep(1);
						exec("php /var/www/html/programs/h.php");
						sleep(1);
						
						exec("rigctl -m 2 -r 127.0.0.1:4534 f m", $output);
						//echo $output[1]," $output[2]"," $output[3]";
		*/				$output = [
							0 => ' Zero ',
							1 => (string)15,
							2 => ' USB '
						];
						$x="Frequency: " . $output[0]." Mode: ".$output[1]." Bandwidth: ".$output[2];
						echo $x;
//						echo $x;
//						$y=trim($output[2]);
//						echo $y;
//						$z=echo $output[0];
						//echo " $output[2]";
						//echo " $output[1]";
						//echo $fa;
						//$fd=explode("\n", $fa);
						//print_r($fd);
						//echo print_r($output);
						//$fma=$output;
						//echo $fma[1];
						//$ph="Frequency: $f&#13;&#10;Mode: $fma[3]&#13;&#10;Bandwidth: $fma[4]";
		}
	?>
	<script>
		var e="<?php echo $x; ?>";
		alert(e);
</script>
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
				<textarea id="frequency" class="form-control form-control-sm mb-3" cols='60' rows='3' style='background-color: orange !important' <b><?php echo htmlspecialchars($ph, ENT_QUOTES, 'UTF-8')?></textarea>
		</div>
	</div> 
<div>
	<form method="POST">
		<button type="submit" class="btn btn-sm btn-info" name="start">Connect RigPi Lite</button>
		<button type="submit" formaction="/programs/lite.php"  class="btn btn-sm btn-info" name="stop">Disconnect RigPi Lite</button>
	</form>   	
</div>  	
</html>
</body>
	<script>
	document.addEventListener('DOMContentLoaded', function() {	
		if (typeof $ !== "undefined") {
			console.log("jQuery loaded and ready.");
		} else {
			console.log("jQuery not available.");
		}
		console.log(document.querySelector('form'));
		document.querySelector('form').addEventListener('submit', function(event) {
			console.log("Form is about to be submitted.");
			if (event.submitter.name=="start"){
				console.log("start");
				<?php
				doFreq();
				?>
				
				
			}
			if (event.submitter.name=="stop"){
				console.log("stop");
			}
			console.log("done");
	//		$.post('/programs/h.php',{},function(response){
	//$.post("/programs/h.php", function(response){
	//				console.log("response");
	//			});
	//
//			});
	});
})
	window.onerror = function(message, source, lineno, colno, error) {
		console.error("Error message: ", message);
		console.error("Source: ", source);
		console.error("Line: ", lineno, "Column: ", colno);
		console.error("Error object: ", error);
	};

	</script>
