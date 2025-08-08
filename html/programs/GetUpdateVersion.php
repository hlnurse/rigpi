 <?php /**
  * @author Howard Nurse, W6HN
  *
  * This routine gets current latest RSS version from download site
  *
  * It must live in the programs folder
  */

 echo file_get_contents("https://rigpi.net/getRSSVersion4.txt");

?>
