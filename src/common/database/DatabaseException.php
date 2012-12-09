<?php

/**
 * Database exception
 * Use it in case we wrap our own database class instead of that from libphutil
 */
final class DatabaseException extends BasicException {
    /**
     * Message to be displayed to users in case of database errors
     * @return string
     */
    public function getUserMessage() {
        return 'Database Error';
    }
}
