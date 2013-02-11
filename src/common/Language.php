<?php

final class Language {
    const ROMANIAN = 1;
    const ENGLISH = 2;

    /**
     * @return void
     */
    private function __construct() {
    }

    /**
     * @param int $language
     * @return bool
     */
    public static function exists($language) {
        return in_array($language, '1', '2');
    }

    /**
     * @param string $language
     * @return bool
     */
    public static function interpret($language) {
        switch ($language) {
            case 'romanian':
                return self::ROMANIAN;

            case 'english':
                return self::ENGLISH;

            default:
                return null;
        }
    }
}
