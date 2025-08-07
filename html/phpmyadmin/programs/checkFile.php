<?php

/**
 * @author Howard Nurse, W6HN.
 *
 * This routine checks for presence of file
 *
 * It must live in the programs folder
 */

if (file_exists("../scheduler/custom.ics") == true) {
  echo 1;
} else {
  echo 0;
}

?>
