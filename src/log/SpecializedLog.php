<?php

/**
 * Specialized log to print messages more special than accesses and errors
 *
 * Can also be made to keep the logged information for later redisplay,
 * like debugging
 */
final class SpecializedLog {
    private $path;
    private $format;
    private $data;
    private $commonData;

    /**
     * Constructs a log that outputs at $path using the format $format
     *
     * If $keep_data is true it also keeps tha data to be used at the end
     *
     * @param string $path
     * @param string $format
     * @param bool $keep_data
     * @return this
     */
    public function __construct($path, $format, $keep_data = false) {
        if (!is_string($path)) {
            throw new SpecializedLogException(
                "SpecializedLog expects `path` to be a string");
        }

        if (!is_string($format)) {
            throw new SpecializedLogException(
                "SpecializedLog expects `format` to be a string");
        }

        if (!is_bool($keep_data)) {
            throw new SpecializedLogException(
                "SpecializedLog expects `keep_data` to be a boolean");
        }

        if (empty($path)) {
            $this->path = null;
        } else {
            try {
                Filesystem::assertIsFile($path);
            } catch(Exception $e) {
                throw new SpecializedLogException(
                    "Log path is not an accesible file");
            }

            try {
                Filesystem::assertWritable($path);
            } catch(Exception $e) {
                throw new SpecializedLogException(
                    "Log file is not writable");
            }

            $this->path = $path;
        }

        $this->format = $format;
        if ($keep_data) {
            $this->data = '';
        } else {
            $this->data = null;
        }

        $this->commonData = array();
    }

    /**
     * Set this as extra common data for logging
     *
     * @param array $extra_data
     * @return this
     */
    public function setCommonData(array $extra_data) {
        $this->commonData = $extra_data;
        return $this;
    }

    /**
     * Prints the data in the associative array
     *
     * By defautl D is then date, p is the request URL and i the remote info,
     * they are already set
     *
     * Optional elements are m which is for the message and u for the userid
     *
     * @param array $data
     * @return this
     */
    public function printData($data) {
        if (!is_array($data)) {
            throw new SpecializedLogException(
                "SpecializedLog::printData expects `data` to be an array");
        }

        $log = id(new PhutilDeferredLog($this->path, $this->format))
            ->setFailQuietly(false)
            ->setData(array(
                'D' => date('r'),
                'p' => idx($_REQUEST, '_path'),
                'i' => InfoarenaEnvironment::getRemoteIPInfo()))
            ->setData($this->commonData)
            ->setData($data);
        $log->write();

        if ($this->data !== null) {
            $this->data .= idx($data, 'm', ''). "\n";
        }

        return $this;
    }

    /**
     * Prints this message to the specified log
     *
     * More user friendly
     *
     * @param string
     * @return this
     */
    public function printMessage($message) {
        if (!is_string($message)) {
            throw new SpecializedLogException(
                "SpecializedLog::printMessage expects `message` to be a string"
            );
        }

        return $this->printData(array(
            'm' => $message));
    }

    /**
     * Returns the stored data
     *
     * @return string
     */
    public function getStoredData() {
        if ($this->data === null) {
            return '';
        } else {
            return $this->data;
        }
    }
}
