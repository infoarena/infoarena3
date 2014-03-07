<?php

final class UnsafeSetException extends BasicException {
    public function getUserMessage() {
        return
            "There is a problem with us, please send us a report " .
            "at contact@infoarena.ro";
    }
}
