#!/bin/sh
sudo raspi-gpio set 10 pu
sudo php /var/www/html/programs/GPIO/GPIOInt1.php $1 $2 $3 > /dev/null 2>&1 &
