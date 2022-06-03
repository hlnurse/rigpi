<?php

/**
 * @author Howard Nurse, W6HN.
 * @copyright 2018
 */
function GetBandFromFrequency($freq){
    switch ($freq){
        case ($freq > 1800000 && $freq < 2000000):
            return "160";
            break;
        case ($freq > 3500000 && $freq < 4000000):
            return "80";
            break;
        case ($freq > 5330000 && $freq < 5405010):
            return "60";
            break;
        case ($freq > 7000000 && $freq < 7300000):
            return "40";
            break;
        case ($freq > 10100000 && $freq < 10150000):
            return "30";
            break;
        case ($freq > 14000000 && $freq < 14350000):
            return "20";
            break;
        case ($freq > 18068000 && $freq < 18168000):
            return "17";
            break;
        case ($freq > 21000000 && $freq < 21450000):
            return "15";
            break;
        case ($freq > 24890000 && $freq < 24990000):
            return "12";
            break;
        case ($freq > 28000000 && $freq < 29700000):
            return "10";
            break;
        case ($freq > 50000000 && $freq < 54000000):
            return "6";
            break;
        case ($freq > 144000000 && $freq < 148000000):
            return "2";
            break;
        case ($freq > 219000000 && $freq < 225000000):
            return "1.25";
            break;
        default:
        	return 'UNK';
        	break; 
    }
    
}


?>