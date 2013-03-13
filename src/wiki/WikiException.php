<?php

/**
 * Abstract exception for all wiki problems
 */
abstract class WikiException extends BasicException {
    /**
     * Message to be displayed to users in case of wiki errors
     * @return string
     */
     public function getUserMessage() {
        return 'Wiki Error';
     }
}
