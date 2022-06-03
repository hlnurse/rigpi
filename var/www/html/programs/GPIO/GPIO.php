<?php

require_once('/var/www/html/programs/GPIO/FileSystem.php');
require_once('/var/www/html/programs/GPIO/FileSystemInterface.php');
require_once('/var/www/html/programs/GPIO/InterruptWatcher.php');
require_once('/var/www/html/programs/GPIO/InputPin.php');
require_once('/var/www/html/programs/GPIO/GPIOInterface.php');

final class GPIO implements GPIOInterface
{
    private $fileSystem;
    private $streamSelect;

    /**
     * Constructor.
     * 
     * @param FileSystemInterface $fileSystem Optional file system object to use
     * @param callable $streamSelect Optional sream select callable
     */
    public function __construct(FileSystemInterface $fileSystem = null, callable $streamSelect = null)
    {
        $this->fileSystem = $fileSystem ?: new FileSystem();
        $this->streamSelect = $streamSelect ?: 'stream_select';
    }

    /**
     * {@inheritdoc}
     */
    public function getInputPin($number)
    {
        return new InputPin($this->fileSystem, $number);
    }

    /**
     * {@inheritdoc}
     */
    public function getOutputPin($number)
    {
        return new OutputPin($this->fileSystem, $number);
    }

    /**
     * {@inheritdoc}
     */
    public function createWatcher()
    {
        return new InterruptWatcher($this->fileSystem, $this->streamSelect);
    }
}
