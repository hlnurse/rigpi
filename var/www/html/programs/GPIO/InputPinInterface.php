<?php

require_once('/var/www/html/programs/GPIO/PinInterface.php');

interface InputPinInterface extends PinInterface
{
    const EDGE_NONE = 'none';
    const EDGE_BOTH = 'both';
    const EDGE_RISING = 'rising';
    const EDGE_FALLING = 'falling';

    /**
     * Get the pin edge.
     * 
     * @return string The pin edge value
     */
    public function getEdge();

    /**
     * Set the pin edge.
     * 
     * @param string $edge The pin edge value to set
     */
    public function setEdge($edge);
}
