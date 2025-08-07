<?php

/**
 * @author Howard Nurse, W6HN.
 * @copyright 2018
 */
function GetEnabled($band){
    switch ($band){
        case '160'):
            return "160";
            break;
        default:
        	return 'UNK';
        	break; 
    }
    
}


?>