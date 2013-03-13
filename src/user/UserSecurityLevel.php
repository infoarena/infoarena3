<?php

final class UserSecurityLevel {
    // TODO: properly add rights
    private static $rights = array(
        1 => 0,
        2 => 1,
        3 => 2,
        4 => 3);

    const BASIC_USER = 1;
    const TASK_ADDER = 2;
    const MODERATOR = 3;
    const ADMINISTRATOR = 4;

    /**
     * @return void
     */
    private function __construct() {
    }

    /**
     * @param int $security_level
     * @return bool
     */
    public static function exists($security_level) {
        return in_array($security_level, array('1', '2', '3', '4'), true);
    }

    /**
     * @param string $security_level
     * @return int
     */
    public static function interpret($security_level) {
        switch ($security_level) {
            case 'basic_user':
                return self::BASIC_USER;
            case 'task_adder':
                return self::TASK_ADDER;

            case 'moderator':
                return self::MODERATOR;

            case 'administrator':
                return self::ADMINISTRATOR;

            default:
                throw new UserSecurityLevelException(
                    "Unrecognized user security level");
        }
    }

    /**
     * @param int
     * @return int
     */
    public static function getRights($security_level) {
        if (!self::exists($security_level)) {
            throw new UserSecurityLevelException(
                'Unrecognized user security level');
        }

        return self::$rights[$security_level];
    }
}
