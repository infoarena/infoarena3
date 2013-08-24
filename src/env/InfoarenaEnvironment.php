<?php

/**
 * Class responsible with loading the libphutil libraries, loading the config
 * and handling fatal errors
 *
 * Inspired Mostly from PhabricatorStartup and PhabricatorEnv
 */

final class InfoarenaEnvironment {
    private static $config;
    private static $root;
    private static $log;
    private static $debugLog;
    private static $request;

    /**
     * Method to be called to prepare everything
     * @return void
     */
    public static function start() {
        self::setRoot(realpath(dirname(dirname(dirname(__FILE__)))));

        static $registered_shutdown;
        if (!$registered_shutdown) { // this way we can call this function
                                     // multiple times safely
            register_shutdown_function(array(__CLASS__, 'stop'));
            $registered_shutdown = true;
        }

        self::setupPHP();
        self::loadLibraries();

        // now we have acces to the power of libphutil
        self::loadConfig();
        self::setTimezone();

        self::initLog();

        if (self::getEnvConfig('debugging.mode')) {
            self::initDebugLog();
        }

        self::getLog()->printData(array(
            'm' => 'Request started'));

        self::buildRequest();
    }

    /**
     * Will be called at the end of execution
     * Should fancy display errors
     *
     * @return void
     */
    public static function stop() {
        $event = error_get_last();
        if (!$event) {
            return;
        }

        switch ($event['type']) {
            case E_ERROR:
            case E_PARSE:
            case E_COMPILE_ERROR:
                break;
            default:
                return;
        }

        // we've got an error, and a pretty bad one
        $message = $event['message'].' at line '.$event['line'].' in file '.
                   $event['file']. " - That's pretty bad :-(";
        $user_message = "Fatal Error";

        self::crash($message, $user_message);
    }

    /**
     * Lightweight exit, used when we can not do anything else
     *
     * @param string $message       to be written in the error log
     * @param string $user_message  to be displayed to the user
     * @return void
     */
    public static function crash($message, $user_message = 'Fatal Error') {
        header(
            'Content-Type: text/plain; charset=utf-8',
            $replace = true,
            $http_response_code = 500); // Internal System Error

        error_log($message);
        echo $user_message;

        exit(1);
    }

    /**
     * Reads a configuration entry
     * It assumes the key exists, will throw exception if it does not
     *
     * @param string $key
     * @return mixed
     */
    public static function getEnvConfig($key) {
        return self::$config->$key;
    }

    /**
     * Gets the log object
     *
     * @return SpecializedLog
     */
    public static function getLog() {
        return self::$log;
    }

    /**
     * Gets the debug log object
     *
     * @return SpecializedLog
     */
    public static function getDebugLog() {
        return self::$debugLog;
    }

    /**
     * Gets the root folder of the project
     *
     * @return string
     */
    public static function getRoot() {
        return self::$root;
    }

    /**
     * Returns the current remote ip information
     * TODO: make it smarter
     *
     * @return string
     */
    public static function getRemoteIpInfo() {
        return idx($_SERVER, 'REMOTE_ADDR', '');
    }

    /**
     * Returns the current request object
     *
     * @return Request
     */
    public static function getRequest() {
        return self::$request;
    }

    /**
     * Sets the root folder of the project
     * To be used when we still require files, like for loading configrations
     *
     * @param string $new_root
     * @return void
     */
    private static function setRoot($new_root) {
        self::$root = $new_root;
    }

    /**
     * Make sure the php is good (version and settings)
     *
     * @return void
     */
    private static function setupPHP() {
        ini_set('memory_limit', -1);
        $required_version = '5.2.3'; // we trust Phabricator for this value
                                    // if we find a lower version good we can
                                    // change this value
        if (version_compare(PHP_VERSION, $required_version) < 0) {
            self::crash(
                "You are running a PHP version '".PHP_VERSION."' which is ".
                "older than the minimum required version '{$required_version}".
                "'. Update to at least '{$required_version}'.");
        }

        if (get_magic_quotes_gpc()) {
            self::crash(
                "Your server is configured with PHP 'magic_quotes_gpc' ".
                "enabled. This feature is 'highly discouraged' by PHP's ".
                "developers and you must disable it to run Infoarena ".
                "locally. Consult the PHP manual for instructions.");
        }
    }

    /**
     * Loads the libphutil and infoarena libraries
     *
     * @return void
     */
    private static function loadLibraries() {
        @include_once
            self::getRoot().'/libphutil/src/__phutil_library_init__.php';

        if (!@constant('__LIBPHUTIL__')) {
            self::crash(
                "Unable to load libphutil, have you run make setup?",
                "Setup not done");
        }

        phutil_load_library(self::getRoot().'/src');
    }

    /**
     * Loads the configuration containing all settings
     *
     * @return void
     */
    private static function loadConfig() {
        try {
            $default_config =
                Filesystem::readFile(
                    self::getRoot().'/conf/.default-iaconfig');
            $user_config =
                Filesystem::readFile(
                    self::getRoot().'/.iaconfig');
            self::$config = new Configuration($user_config, $default_config);
        } catch (BasicException $e) {
            self::crash(
                "[Configuration exception] ".$e->getMessage(),
                $e->getUserMessage());
        } catch (Exception $e) {
            self::crash(
                "[Configuration filesystem exception] ".$e->getMessage());
        }
    }

    /**
     * Sets the current timezone based on the configuration
     *
     * @return void
     */
    private static function setTimezone() {
        date_default_timezone_set(self::getEnvConfig("timezone"));
    }

    /**
     * Initializes the specialized log (not access, nor error)
     *
     * @return void
     */
    private static function initLog() {
        self::$log = new SpecializedLog(
            self::getEnvConfig("log.path"),
            self::getEnvConfig("log.format"));
    }

    /**
     * Initializes the debug log
     *
     * You should print to this log for debugging
     *
     * @return void
     */
    private static function initDebugLog() {
        self::$debugLog = new SpecializedLog(
            self::getEnvConfig("debug.log.path"),
            self::getEnvConfig("debug.log.format"),
            $keep_data = true);
    }

    /**
     * Build the request with the $_GET, $_POST data
     */
    private static function buildRequest() {
        self::$request = new Request(
            idx($_SERVER, 'SERVER_NAME', 'localhost'),
            idx($_GET, '_path', '/'));

        if (empty($_SERVER['HTTPS'])) {
            self::$request->setProtocol('HTTP');
        } else if (!strcasecmp($_SERVER['HTTPS'], 'off')) {
            self::$request->setProtocol('HTTP');
        } else {
            self::$request->setProtocol('HTTPS');
        }

        self::$request->setData($_POST);
        self::$request->setMethod(idx($_SERVER, 'REQUEST_METHOD', 'GET'));

        $arguments = $_GET;
        unset($arguments['_path']);
        self::$request->setArguments($arguments);
    }
}
