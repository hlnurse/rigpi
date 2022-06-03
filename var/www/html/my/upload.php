<?php
$res = array(); 
foreach ($_FILES["files"]["error"] as $key => $error)
{
    if ($error == UPLOAD_ERR_OK)
    {
        $name = $_FILES["files"]["name"][$key];
        if(file_exists('/var/www/fi/'.$name))
        {
            unlink('/var/www/fi/'.$name);
        }
        if (move_uploaded_file( $_FILES["files"]["tmp_name"][$key], "/var/www/fi/" . $name)){
	        $res[] =  $name;
	    }else{
		    $res[]= "Move failed.";
	    }
    }
    else
    {
        echo json_encode(array('res'=>FALSE,'data'=>'Error uploading '.$name));
        exit(1);
    }
}
echo json_encode(array('res'=>TRUE,'data'=>$res));
exit(0);