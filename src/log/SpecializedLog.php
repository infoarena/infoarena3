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
        if (empty($path)) {
            $this->path = null;
        } else {
            $this->path = $path;
        }

        $this->format = $format;
        if ($keep_data) {
            $data = '';
        } else {
            $data = null;
        }
    }

    /**
     * Prints the data in the associative array
     * Usally m stands for message and u for user
     *
     * @param string $data
     * @return void
     */
    public function printData($data) {
        $log = id(new PhutilDeferredLog($this->path, $this->format))
            ->setFailQuietly(true)
            ->setData(array(
                'D' => date('r'),
                'p' => $_REQUEST['_page']))
            ->setData($data);
        $log->write();

        if ($this->data !== null) {
            $this->data .= idx($data, 'm', ''). "\n";
        }
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
