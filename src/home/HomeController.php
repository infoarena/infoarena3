<?php

/**
 * The controller which serves the home page
 */
final class HomeController extends BasicController {
    public function execute() {
        $session_info = $this->session->getInfo();
    }
}
