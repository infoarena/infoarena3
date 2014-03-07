<?php

final class StringUtils {
    /**
     * Internal function needed because substr gives false instead of empty
     * string in this case: substr("ab", 2)
     *
     * @param string $string
     * @param int $start
     * @return $string
     */
    public static function nonFalsesubstr($string, $start) {
        $substring = substr($string, $start);
        if ($substring === false)
            $substring = '';
        return $substring;
    }

    /**
     * To remove comments like /* * / (space added to not break comment)
     * or like this // comment
     * Useful when parsing JSON
     *
     * @param string $string
     * @return string
     */
    public static function removeComments($string) {
        return preg_replace(
            "#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|".
            "([\s\t]//.*)|(^//.*)#", '', $string);
    }
}
