<?php

final class SecurityLevelException extends BasicException {
    /**
     * @return string
     */
    public function getUserMessage() {
        return "Internal Error";
    }
}
