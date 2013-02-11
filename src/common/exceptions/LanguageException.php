<?php

final class LanguageException extends BasicException {
    /**
     * @return string
     */
    public function getUserMessage() {
        return "Internal Language Error";
    }
}
