<?php
$fileName = basename('rigpi.adi');
$adif_file="/var/www/down/rigpi.adi";

//echo $filePath
if(!empty($fileName) && file_exists($adif_file)){
	header('Content-Description: File Transfer');
    header('Content-Type: "application/octet-stream"');
    header('Content-Disposition: attachment; filename="'.$fileName.'"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Cache-Control: private',false);
    header('Pragma: public');
    header('Content-Length: ' . filesize($adif_file));
    flush();
    @readfile($adif_file);
    }    
?>