<?php

/**
 *
 */
final class User {
    private $id;
    private $username;
    private $password;
    private $fullName;
    private $email;
    private $newsletter;
    private $defaultLanguage;
    private $securityLevel;
    private $givenRights;
    private $takenRights;

    /**
     * @param array $dictionary
     * @return void
     */
    public function __construct(array $dictionary) {
        $this->id = (int)$dictionary['id'];
        $this->username = (string)$dictionary['username'];
        // Hashed password
        $this->password = (string)$dictionary['password'];
        $this->fullName = (string)$dictionary['full_name'];
        $this->email = (string)$dictionary['email'];
        $this->newsletter = (bool)$dictionary['newsletter'];
        $this->defaultLanguage = Language::interpret($dictionary['language']);
        $this->securityLevel =
            UserSecurityLevel::interpret($dictionary['security_level']);
        $this->givenRights = (int)$dictionary['given_rights'];
        $this->takenRights = (int)$dictionary['taken_rights'];
        if ($this->givenRights & $this->takenRight) {
            throw new UserParameterException(
            "Given rights must be different from taken rights");
        }
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getFullName() {
        return $this->fullName;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function receivesNewsletter() {
        return $this->newsletter;
    }

    /**
     * @return int
     */
    public function getDefaultLanguage() {
        return $this->defaultLanguage;
    }

    /**
     * @return int
     */
    public function getSecurityLevel() {
        return $this->securityLevel;
    }

    /**
     * @return int
     */
    public function getGivenRights() {
        return $this->givenRights;
    }

    /**
     * @return int
     */
    public function getTakenRights() {
        return $this->takenRights;
    }

    /**
     * @return int
     */
    public function getRights() {
        return
            (UserSecurityLevel::getRights($this->securityLevel) |
             $this->givenRights) & (~$this->takenRights);
    }

    /**
     * @param string $username
     * @return bool
     */

    public static function isUsername($username) {
        if (!is_string($username)) {
            throw new UserParameterException(
                'User::isUserName expects `username` to be a string');
        }

        if (strlen($username) < 1 || strlen($username) > 32) {
            return false;
        }

        return preg_match('/^[a-z0-9][a-z0-9_\-\.]*$/i', $username) > 0;
    }

    /**
     * @param string $password
     * @return bool
     */
    public static function isPassword($password) {
        if (!is_string($password)) {
            throw new UserParameterException(
                'User::isPassword expects `password` to be a string');
        }

        if (strlen($password) < 8 || strlen($password) > 64) {
            return false;
        }

        return true;
    }

    /**
     * @param string $name
     * @return bool
     */
    public static function isName($name) {
        if (!is_string($name)) {
            throw new UserParameterException(
                'User::isName expects `name` to be a string');
        }

        if (strlen($name) < 1 || strlen($name) > 64) {
            return false;
        }

        return preg_match('/^[a-z0-9_\ \@\.\-]+$/i', $name) > 0;
    }
}
