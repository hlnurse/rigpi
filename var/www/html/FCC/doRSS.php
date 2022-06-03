<?php
//executes hamdb for full FCC database.  Not it is possible to download daily update which are much smaller.  See commented lines in hamdb.
//
//by Howard Nurse, W6HN
//exec('sudo /var/www/html/my/rssUpdate getrssfile > /dev/null');
//exec('wget https://rigpi.net/downloads/boton.png');
//echo exec('hamdb w6hn');
//echo $result;



$file = "https://rigpi.net/downloads/RigPi-Updates.zip";
$dest = fopen("/var/www/html/my/downloads/RigPi-Updates.zip", "wb");
$src = file_get_contents($file);
fwrite($dest, $src, strlen($src));
fclose($dest); 
chmod("/var/www/html/my/downloads/RigPi-Updates.zip",0644);
//chown("/var/www/html/my/downloads/boton.png","pi");
exec('sudo chown pi:pi /var/www/html/my/downloads/RigPi-Updates.zip');
//exec('sudo chmod 0644 "/var/www/html/my/downloads/boton.png"');
		$file = '/var/www/html/my/downloads/RigPi-Updates.zip';
		$path = pathinfo( realpath( $file ), PATHINFO_DIRNAME );
		echo $path;
		$zip = new ZipArchive;
		$res = $zip->open($file);
		if ($res === TRUE) {
		    $zip->extractTo( $path );
		    $zip->close();
		    echo "$file extracted to $path\n";
		}else{
		    echo "Couldn't unzip $file\n";
		}
exec('sudo chown pi:pi /var/www/html/my/downloads/RigPi-Updates/html/about.php');
echo "\n\n\n".error_get_last();
?>