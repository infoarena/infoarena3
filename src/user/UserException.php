<?php

/**
 *
 */
abstract class UserException extends BasicException {
    /**
     * @return string
     */
    public function getUserMessage() {
        return 'User Error';
    }
}
