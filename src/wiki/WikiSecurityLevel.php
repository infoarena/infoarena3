<?php

final class WikiSecurityLevel {
    const PUBLIC_ACCESS = 1;
    const PROTECTED_ACCESS = 2;
    const PRIVATE_ACCESS = 3;

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
        return in_array($security_level, array('1', '2', '3'), true);
    }

    /**
     * @param string $security_level
     * @return int|null
     */
    public static function interpret($security_level) {
        switch ($security_level) {
            case 'public':
                return self::PUBLIC_ACCESS;

            case 'protected':
                return self::PROTECTED_ACCESS;

            case 'private':
                return self::PRIVATE_ACCESS;

            default:
                throw new WikiSecurityLevelException(
                    "Unrecognized wiki security level");
        }
    }
}
