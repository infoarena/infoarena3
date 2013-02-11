<?php

/**
 * The most basic exception from which all other exceptions must extend
 */
abstract class BasicException extends Exception {
    /**
     * Typical message to display to users in case of critical errors
     * @return string
     */
    public function getUserMessage() {
        return 'System Error';
    }
}
