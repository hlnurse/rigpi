<?php
namespace IDCT\Net;

use IDCT\Net\TelnetConnectionException;
use IDCT\Net\TelnetTransportException;

class PhpTelnet
{
    const TELNET_NOTHINGTOREAD = '!nothing_to_read';
    const TELNET_EOL = "\r\n";

    /**
     * Buffer size in bytes. Default = 2048 bytes
     * @var int
     */
    protected $buffer = 2048;

    /**
     * Connection socket.
     * @var Resource
     */
    protected $socket;

    /**
     * Read timeout in seconds. Default = 10s
     * @var int
     */
    protected $readTimeout = 10;

    /**
     * Write timeout in seconds. Default = 10s
     * @var int
     */
    protected $writeTimeout = 10;

    /**
     * Returns description of the last error or null if no error.
     *
     * @return string|null
     */
    public function getLastErrorDescription()
    {
        $errorCode = socket_last_error($this->socket);
        if ($errorCode === 0) {
            return null;
        }
        return socket_strerror($errorCode);
    }

    /**
     * Establishes a connection to the provider $host on the provided $port (23
     * by default).
     *
     * @param string $host
     * @param int $port 23 by default.
     * @throws TelnetConnectionException
     * @return $this
     */
    public function connect($host, $port = 23)
    {
        $this->socket = @socket_create(AF_INET, SOCK_STREAM, 0);
        socket_set_option($this->socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=> $this->readTimeout, "usec"=> 0));
        socket_set_option($this->socket, SOL_SOCKET, SO_SNDTIMEO, array("sec"=> $this->writeTimeout, "usec"=> 0));
        $result = @socket_connect($this->socket, $host, $port);
        if (!$result) {
            throw new TelnetConnectionException($this->getLastErrorDescription());
        }

        return $this;
    }
    
    public function disconnect()
    {
        if ($this->socket !== null) {
            socket_close($this->socket);
        }
    }

    /**
     * Reads a message (string) from the server until newline or buffer reached.
     * Returns the TELNET_NOTHINGTOREAD constant value when there is no data.
     *
     * @throws TelnetTransportException
     * @return string
     */
    public function read()
    {
        $response = trim(socket_read($this->socket, 2048, PHP_BINARY_READ));
        if ($response === false) {
            throw new TelnetTransportException($this->getLastErrorDescription());
        }
        if ($response === '') {
            return static::TELNET_NOTHINGTOREAD;
        }

        return $response;
    }

    /**
     * Reads messages from the connected server until empty response.
     * Returns an array of lines or flat string separated by newlines if $flatten
     * set to true.
     *
     * @param boolean $flatten Returns string if set to true.
     * @throws TelnetTransportException via `read` method. No rethrowing.
     * @return array|string
     */
    public function readToEnd($flatten = false)
    {
        $response = array();
        while (($line = $this->read()) !== static::TELNET_NOTHINGTOREAD) {
            $response[] = $line;
        }
        if ($flatten) {
            $response = join(PHP_EOL, $response);
        }
        return $response;
    }

    /**
     * Writes the message to the connected server.
     *
     * @param string $message Text to send.
     * @throws TelnetTransportException
     * @return int Bytes successfully sent.
     */
    public function write($message)
    {
        $response = socket_write($this->socket, $message);
        if ($response === false) {
            throw new TelnetTransportException($this->getLastErrorDescription());
        }
        return $response;
    }

    /**
     * Writes the message followed by newline symbol.
     *
     * @param string $message Text to send.
     * @throws TelnetTransportException via `write` method. No rethrowing.
     * @return int Bytes successfully sent.
     */
    public function writeln($message)
    {
        $response = $this->write($message . static::TELNET_EOL);
        return $response;
    }

    /**
     * Sets the read timeout (in seconds).
     *
     * @param int $seconds Read timeout (at least 1 second).
     * @throws InvalidArgumentException
     * @return $this
     */
    public function setReadTimeout($seconds)
    {
        $value = intval($seconds);
        if ($value < 1) {
            throw new \InvalidArgumentException("Read timeout must be an int greater than 0.");
        }
        $this->readTimeout = $value;
        return $this;
    }

    /**
     * Gets the read timeout (in seconds).
     *
     * @return $this
     */
    public function getReadTimeout()
    {
        return $this->readTimeout;
    }

    /**
     * Sets the write timeout (in seconds).
     *
     * @param int $seconds Write timeout (at least 1 second).
     * @throws InvalidArgumentException
     * @return $this
     */
    public function setWriteTimeout($seconds)
    {
        $value = intval($seconds);
        if ($value < 1) {
            throw new \InvalidArgumentException("Write timeout must be an int greater than 0.");
        }
        $this->writeTimeout = $value;
        return $this;
    }

    /**
     * Gets the write timeout (in seconds).
     *
     * @return $this
     */
    public function getWriteTimeout()
    {
        return $this->writeTimeout;
    }

    /**
     * Sets the buffer size (in butes).
     *
     * @param int $bytes Size (in bytes).
     * @throws InvalidArgumentException
     * @return $this
     */
    public function setBufferSize($bytes)
    {
        $value = intval($bytes);
        if ($value < 1) {
            throw new \InvalidArgumentException("Buffer size must be an int greater than 0.");
        }
        $this->buffer = $value;
        return $this;
    }

    /**
     * Gets the buffer size (in bytes).
     *
     * @return $this
     */
    public function getBufferSize()
    {
        return $this->buffer;
    }

    /**
     * Gets the internal socket.
     *
     * @return Resource|null
     */
    public function _getSocket()
    {
        return $this->socket;
    }

    /**
     * Closes the socket if still open when destroying the object.
     */
    public function __destruct()
    {
        if ($this->socket !== null) {
            socket_close($this->socket);
        }
    }
}
