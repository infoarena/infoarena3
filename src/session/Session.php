<?php

/**
 * Class to hold the session information
 *
 * The session will not be created unless an user explicitly requires something
 * special, such as a different language
 */
final class Session {
    private $connection;
    private $sessionKey;
    private $sessionInfo;
    private $model;

    /**
     * We require the cookie data and a database connection
     *
     * This should be enough for some time, but we might add callbacks and
     * other things in the future
     *
     * @param AphrontDatabaseConnection $connection
     * @param array $cookie_data
     * @return Session
     */
    public function __construct(
        AphrontDatabaseConnection $connection,
        array $cookie_data) {

        $this->connection = $connection;

        $cookie_prefix = InfoarenaEnvironment::getEnvConfig("cookie.prefix");

        $this->sessionKey = idx(
            $cookie_data,
            $cookie_prefix . "skey");

        $this->model = new SessionModel($connection);
    }

    /**
     * Sets the key to this given one
     *
     * This way you can test without cookies
     *
     * @param string $new_data
     * @return this
     */
    public function setKey($new_key) {
        $this->sessionKey = $new_key;
        return $this;
    }

    /**
     * Returns the current SessionInfo
     *
     * It creates it if it does not exists
     * Might return null if the session does not exist for that key
     *
     * @return SessionInfo
     */
    public function getInfo() {
        $this->prepareSessionInfo();
        return $this->sessionInfo;
    }

    /**
     * Returns true if the current key is a session key
     */
    public static function isSessionKey($key) {
        if (!is_string($key)) {
            return false;
        }

        if (strlen($key) != 64) {
            return false;
        }

        return preg_match('/[0-9a-f]+$/i', $key) > 0;
    }

    /**
     * Searches for cookies and builds appropriate session information
     *
     * @return void
     */
    private function prepareSessionInfo() {
        if ($this->sessionInfo !== null) {
            return;
        }

        if ($this->sessionKey) {
            if (Session::isSessionKey($this->sessionKey)) {
                $this->sessionInfo =
                    $this->model->getSessionByKey($this->sessionKey);
                if ($this->sessionInfo !== null) {
                    return;
                }
            }
        }
    }
}
