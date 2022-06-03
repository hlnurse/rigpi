<?php

/**
* @author Howard Nurse W6HN
*/

function getDistance($lat,$lon,$mylat,$mylon){
	if ($lat==""){
		$lat=0;
	}
	if ($lon==""){
		$lon=0;
	}
	if ($mylat==""){
		$mylat=0;
	}
	if ($mylon==""){
		$mylon=0;
	}
	$mylatitude = $mylat;
	$mylongitude = $mylon;
	$latitude = $lat;
	$longitude = $lon;
	$pi = pi();
	
	//get distance
	if (!($mylatitude == $latitude) || !($mylongitude == $longitude)) {
	    $a = $mylatitude * $pi / 180;
	    $b = $latitude * $pi / 180;
	    $l = ($mylongitude - $longitude) * $pi / 180;
	
	    $X = (sin($a) * sin($b)) + (cos($a) * cos($b) * cos($l));
	    $z = sqrt(-1 * $X * $X + 1);
	    if ($z > 0) {
	        $d = 180 / $pi * (69.041 * (atan(-1 * $X / sqrt(-1 * $X * $X + 1)) + 2 * atan(1)));
	    } else {
	        $d = 0;
	    }
	} else {
	    $d = 0;
	}
	if ($d < 1) {
	    $d = 0;
	}
	$FindDistance = $d;
	$pDistanceMi = $d;
	
	//get bearing
	if ($mylongitude < 0) {
	    $mylongitude = 360 + $mylongitude;
	}
	
	if ($longitude < 0) {
	    $longitude = 360 + $longitude;
	}
	
	if (!($mylatitude == $latitude) || !($mylongitude == $longitude)) {
	    $a = $mylatitude * $pi / 180;
	    $b = $latitude * $pi / 180;
	    $d = ($pDistanceMi / 69.041) * $pi / 180;
	    $l = ($mylongitude - $longitude) * $pi / 180;
	    if (!($mylongitude == $longitude) && !(abs($mylongitude - $longitude) == 180) && 
	        !(abs($mylongitude - $longitude) == 360)) {
	        if (!(cos($a) * sin($d) == 0)) {
	            $X = (sin($b) - (sin($a) * cos($d))) / (cos($a) * sin($d));
	            if (!(Abs($X) == 1)) {
	                $d = 180 / $pi * (atan(-1 * $X / Sqrt(-1 * $X * $X + 1)) + 2 * atan(1));
	                if (sin($l) >= 0) {
	                    $d = 360 - $d;
	                }
	            } else {
	                if ($latitude < 0) {
	                    $d = 180;
	                } else {
	                    $d = 0;
	                }
	            }
	        } else {
	            if ($latitude < 0) {
	                $d = 180;
	            } else {
	                $d = 0;
	            }
	        }
	    } else {
	        if ($latitude < 0) {
	            $d = 180;
	        } else {
	            $d = 0;
	        }
	    }
	} else {
	    if ($latitude < 0) {
	        $d = 180;
	    } else {
	        $d = 0;
	    }
	}
	if ($d==0){
	    $d=" ";
	}else{
	    $d=intval($d);
	}
	// |distance in mi|distance in km|bearing|
	return "|" . intval($pDistanceMi) . "|" . intval(1.609344 * $pDistanceMi) . "|" . $d . "|";
}

?>