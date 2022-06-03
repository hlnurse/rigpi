<?php
ini_set('display_errors',true);
error_reporting(E_ALL);
$tPortion=$_POST['portion'];
switch ($tPortion){
	case "1": //download
		$file = "https://rigpi.net/downloads/RigPiUpdates.zip";
		if (file_exists("/var/www/fi/RigPiUpdates.zip")){
			echo "Old RigPiUpdates.zip file exists, now deleting.<br>";
			exec('sudo chown www-data:www-data /var/www/fi/RigPiUpdates.zip');
			exec("sudo rm /var/www/fi/RigPiUpdates.zip");
		}
		$htmlfolder="/var/www/fi/RigPiUpdates";
			
		if (file_exists($htmlfolder)){
			echo "Old RigPiUpdates directory exists, now deleting.<br>";
			exec('sudo chown -R www-data:www-data '.$htmlfolder);
			exec("rm -rf ".$htmlfolder);
		}
		$dest = fopen("/var/www/fi/RigPiUpdates.zip", "w");
		$src = file_get_contents($file);
		fwrite($dest, $src, strlen($src));
		fclose($dest);
		$file = '/var/www/fi/RigPiUpdates.zip';
		exec('sudo chown -R pi:pi /var/www/fi/RigPiUpdates.zip');
		echo "New RigPiUpdates.zip from https://rigpi.net downloaded.<br>";
		break;
	case "2": //unzip
		$file = '/var/www/fi/RigPiUpdates.zip';
		echo "Unzipping RigPiUpdates.zip.<br>";
		$path = pathinfo( realpath( $file ), PATHINFO_DIRNAME )."/";
		$zip = new ZipArchive;
		$res = $zip->open($file);
		if ($res === TRUE) {
		    $zip->extractTo( $path );
		    for($i=0; $i<$zip->numFiles; $i++){ //this updates all files to their mtime rather than current time
			    touch($path.$zip->statIndex($i)['name'],$zip->statIndex($i)['mtime']);
		    }
		    $zip->close();
		    echo "RigPiUpdates.zip extracted.<br>";
		}else{
		    echo "Error: Couldnt unzip $file<br>";
		    exit;
		};
		break;
	case "3":  
		echo "Now copying files.<br>";
		//change ownership to www-data to permit rsync permission
		exec('sudo chown -R www-data:www-data /var/www/html');
		exec("sudo chown -R pi:pi /var/www/fi/RigPiUpdates");
		exec("rsync -avz /var/www/fi/RigPiUpdates/html/ /var/www/html/");
		//change ownership to pi for normal operation
		exec('sudo chown -R pi:pi /var/www/html');
		break;
	case "4":
		echo "<p>RSS has received the update files.<p><p><h2>OK to reboot RSS to refresh all files?</h2>";
		break;

	}

?>