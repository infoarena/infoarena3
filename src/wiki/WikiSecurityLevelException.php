<?php

final class WikiSecurityLevelException extends BasicException {
    /**
     * @return string
     */
    public function getUserMessage() {
        return "Internal Error";
    }
}
