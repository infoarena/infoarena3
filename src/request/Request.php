<?php

/**
 * Request builder
 *
 * Will contain information like the domain, the path, the request type (json,
 * normal), the request method(GET, POST) and so on
 */
final class Request {
    public static $accectableProtocols = array('HTTP', 'HTTPS');
    public static $accectableMethods = array('GET', 'POST');

    private $host;
    private $path;
    private $protocol; // default HTTP
    private $data; // default empty array
    private $method; // default GET

    /**
     * This holds information from $_GET
     */
    private $arguments;

    /**
     * The most important information rellies on the host and path
     * so we require them when we construct the request object
     *
     * @param string $host
     * @param string $path
     * @return this
     */
    public function __construct($host, $path) {
        if (!is_string($host)) {
            throw new RequestException(
                "Request expects `host` to be a string");
        }

        if (!is_string($path)) {
            throw new RequestException(
                "Request expects `path` to be a string");
        }

        $this->host = $host;

        $this->path = $this->fixPath($path);

        $this->protocol = 'HTTP';
        $this->data = array();
        $this->method = 'GET';
        $this->arguments = array();
    }

    /**
     * Creates a new request identical to this one but with a different path
     *
     * Useful for when you need to pass the partial path for a controller
     *
     * @param string $path
     * @return Request
     */
    public function cloneWithDifferentPath($path) {
        if (!is_string($path)) {
            throw new RequestException(
                "Request::alterPath expects `path` to be a string");
        }

        $new_object = clone $this;

        $new_object->path = $this->fixPath($path);

        return $new_object;
    }

    /**
     * Returns the current host
     *
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Returns the current path
     *
     * @return string
     */
    public function getPath() {
        return $this->path;
    }

    /**
     * Returns wether the given protocol is or not an accepted one
     *
     * @param string
     * @return bool
     */
    public static function isProtocol($protocol) {
        return in_array($protocol, self::$accectableProtocols, true);
    }

    /**
     * Sets the current protocol:
     * HTTP or HTTPS
     * We might start supporting more of them
     *
     *
     * @param string $protocol
     * @return this
     */
    public function setProtocol($protocol) {
        if (!self::isProtocol($protocol)) {
            throw new RequestException(
                "Request::setProtocol expects `protocol` to be HTTP or HTTPS");
        }
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * Returns the protocol of this request
     *
     * @return string
     */
    public function getProtocol() {
        return $this->protocol;
    }

    /**
     * Sets the current data
     *
     * Roughly similar to $_POST but we may add things here
     * as we see fit
     *
     * @param array $data
     * @return this
     */
    public function setData($data) {
        if (!is_array($data)) {
            throw new RequestException(
                "Request::setData expects `data` to be an array");
        }
        $this->data = $data;
        return $this;
    }

    /**
     * Returns the current data
     *
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Returns the data with the given key or a default if it does not exist
     *
     * @param string $key
     * @param mixed $default optional
     * @return mixed
     */
    public function data($key, $default = null) {
        if (!isset($this->data[$key])) {
            return $default;
        }
        return $this->data[$key];
    }

    /**
     * Returns wether the given method is or not an accepted one
     *
     * @param string
     * @return bool
     */
    public static function isMethod($method) {
        return in_array($method, self::$accectableMethods, true);
    }

    /**
     * Sets the current method
     *
     * @param string $method
     * @return this
     */
    public function setMethod($method) {
        if (!self::isMethod($method)) {
            throw new RequestException(
                "Request::setMethod expects `method` to be GET or POST");
        }
        $this->method = $method;
        return $this;
    }

    /**
     * Returns the current method used, GET or POST
     *
     * @return string
     */
    public function getMethod() {
        return $this->method;
    }

    /**
     * Sets the current arguments
     * Should be used for the wiki for example for giving the action (edit, ..)
     *
     * @param array $arguments
     * @return this
     */
    public function setArguments($arguments) {
        if (!is_array($arguments)) {
            throw new RequestException(
                "Request::setArguments expects `arguments` to be an array");
        }
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * Returns the current arguments
     *
     * @return array
     */
    public function getArguments() {
        return $this->arguments;
    }

    /**
     * Returns the argument with the given key or a default if it does not
     * exist
     *
     * @param string $key
     * @param mixed $default optional
     * @return mixed
     */
    public function argument($key, $default = null) {
        if (!isset($this->arguments[$key])) {
            return $default;
        }

        return $this->arguments[$key];
    }

    /**
     * Fixes the path so that it does not have trailing or starting whitespace
     *
     * It is also modified so that it ends with a slash, making it easier for
     * the dispatcher
    */
    private function fixPath($path) {
        $path = trim($path);
        $path = trim($path, '/');
        $path .= '/';
        return $path;
    }
}
