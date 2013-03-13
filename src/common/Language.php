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
        return in_array($language, array('1', '2'), true);
    }

    /**
     * @param string $language
     * @return int
     */
    public static function interpret($language) {
        switch ($language) {
            case 'romanian':
                return self::ROMANIAN;

            case 'english':
                return self::ENGLISH;

            default:
                throw new LanguageException("Unrecognized Language");
        }
    }
}
