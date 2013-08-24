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
}
