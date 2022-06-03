<?php
shell_exec(
  "rigctl -m 3073 -r /dev/serial/by-id/usb-Silicon_Labs_CP2102_USB_to_UART_Bridge_Controller_IC-7300_02020433-if00-port0 --set-conf=auto_power_on=1 > /dev/null &"
);
?>
