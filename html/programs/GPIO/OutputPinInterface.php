<?php

require_once('/var/www/html/programs/GPIO/PinInterface.php');

interface OutputPinInterface extends PinInterface
{
    /**
     * Set the pin value.
     * 
     * @param int $value The value to set
     */
    public function setValue($value);
}
